<?php

class SynchronizationWorker {
    private $userId;
    private $jobId;
    private $job;
    private $config;
    private $serviceClient;
    
    public function __construct($userId) {
        $this->userId = $userId;
        $this->jobId = 1;
        $this->config = Yii::app()->params['ionCmSynchronization'];
        $this->serviceClient = new CuriosityMachineServiceClient($this->config['cmServiceUrl']);
    }

    public function Start(){
        $this->job = JobUser::model()->GetByJobIdAndUserId($this->jobId, $this->userId);
        PersonSynchronizationResult::model()->ClearPreviousResults($this->jobId, $this->userId);
        $this->job->StartStop(TRUE);
        
        try {
            $this->serviceClient->Connect();
            $this->serviceClient->Login($this->config['cmUserName'], $this->config['cmPassword']);
            if ($this->serviceClient->LoggedIn) {
                $person = $this->getNextPerson();
                while ($person != NULL) {
                    $person_id = $person->ID;
                    try {
                        $related_info = null;
                        $cm_user_profile = CMUserProfile::model()->findByPk($person_id);
                        if($cm_user_profile) {
                            $related_info = $this->serviceClient->GetCMUserById($cm_user_profile->CMUserId);
                        }
                        else {
                            $cm_user_profile = new CMUserProfile();
                            $cm_user_profile->PersonId = $person_id;
                            $dateOfBirth = $person->DateOfBirth == null ? '' : $person->DateOfBirth;
                            $related_info = $this->serviceClient->GetCMUserByInfo($person->FirstName, $person->LastName, $dateOfBirth);
                        }
                        
                        if ($related_info) {
                            $this->saveCMUserProfile($cm_user_profile, $related_info);
                            $this->saveSynchronizationResult($person_id, TRUE);
                        }
                        else {
                            $person_location = Locations::model()->GetLocationByPersonId($person_id);
                            $cm_user_to_save = (object) array('FirstName' => $person->FirstName, 'LastName' => $person->LastName,
                                                              'Gender' => $person->Sex, 'DateOfBirth' => $person->DateOfBirth,
                                                              'Email' => $person->EmailAddress,
                                                              'Country' => $person_location ? $person_location->Country : '',
                                                              'City' => $person_location ? $person_location->City : '');
                            $related_info = $this->serviceClient->SaveCMUser($cm_user_to_save);
                            if ($related_info && $related_info->succeed) {
                                $this->saveCMUserProfile($cm_user_profile, $related_info->user);
                                $this->saveSynchronizationResult($person_id, TRUE);
                            }
                            else {
                                $this->saveSynchronizationResult($person_id, FALSE, "Could not save to Curiosity Machine: " . $related_info->error);
                            }
                        }                        
                    }
                    catch (Exception $e) {//sync as many as possible
                        $this->saveSynchronizationResult($person_id, FALSE, "Exception occured: " . $e->getMessage());
                        Yii::log("Synchronization Failed. Participant ID: " . $person_id . ". Exception: " . $e->getMessage());
                    }
                    $person = $this->getNextPerson();
                }
            }
            else {
                $this->Stop(FALSE, "Could not login to Curiosity Machine");
            }
        }
        catch (Exception $e) {
            $this->Stop(FALSE, 'Error occured: ' . $e->getMessage());
        }
        $this->Stop();
    }
    
    public function Stop($is_succeed = TRUE, $details = NULL) {
        $this->serviceClient->Logout();
        $this->job->StartStop(FALSE, $is_succeed, $details);
        $this->job = NULL;
    }
    
    public function IsRunning() {
        return JobUser::model()->GetIsRunning($this->jobId, $this->userId);
    }
    
    private function getNextPerson() {
        return QueryPerson::model()->GetNextForSynchronization($this->userId);
    }
    
    private function saveCMUserProfile(&$cm_user_profile, &$related_info) {
        if (!$related_info->user_projects) {
            $related_info->user_projects = array();
        }
        if (!$related_info->user_badges) {
            $related_info->user_badges = array();
        }
        $cm_user_profile->CMUserId = $related_info->user_id;
        $cm_user_profile->ProfileUrl = $related_info->user_profile_link;
        $cm_user_profile->ProfilePictureUrl = $related_info->user_profile_picture_link;
        $cm_user_profile->Points = $related_info->user_points;
        $cm_user_profile->Experiments = serialize($related_info->user_projects);
        $cm_user_profile->Badges = serialize($related_info->user_badges);
        $cm_user_profile->save();
    }
    
    private function saveSynchronizationResult($person_id, $is_succeed, $details = NULL) {
        $result = new PersonSynchronizationResult();
        $result->UserId = $this->userId;
        $result->PersonId = $person_id;
        $result->IsSucceed = CommonHelper::BoolToInt($is_succeed);
        $result->Details = $details;
        $result->save();
    }
}

?>
