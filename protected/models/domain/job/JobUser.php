<?php

class JobUser extends BaseModel {
    
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return 'jobuser';
    }
    
    public function rules() {
        return array(
            array('JobId, UserId, IsSucceed, Details, IsRunning, LastRunDate', 'safe'),
        );
    }
    
    public function primaryKey() {
        return array('JobId', 'UserId');
    }
    
    /* $direction: TRUE - start, FALSE - stop
    * $is_succeed: TRUE/FALSE
    * $details: error details
    */
    public function StartStop($direction, $isSucceed = TRUE, $details = NULL) {
        $attributes = array();
        $attributes['Details'] = $details;

        if ($direction) {
            $attributes['IsRunning'] = 1;
            $attributes['IsSucceed'] = NULL;
            $attributes['LastRunDate'] = NULL;
        }
        else {
            $attributes['IsRunning'] = 0;
            $attributes['IsSucceed'] = $this->boolToInt($isSucceed);
            $attributes['LastRunDate'] = $this->getCurrentDateTime();
        }
        $this->updateAll($attributes, 'JobId = :job_id AND UserId = :user_id', array (':job_id' => $this->JobId, ':user_id' => $this->UserId));
    }
    
    public function GetByJobIdAndUserId($jobId, $userId) {
        $result = $this->findByPk(array('JobId' => $jobId, 'UserId' => $userId));
        if (!$result) {
            $result = new JobUser();
            $result->JobId = $jobId;
            $result->UserId = $userId;
            $result->save();
        }
        return $result;
    }
    
    public function GetIsRunning($jobId, $userId) {
        $result = $this->findByPk(array('JobId' => $jobId, 'UserId' => $userId));
        return $result && $result->IsRunning;
    }
}

?>
