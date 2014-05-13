<?php

class SurveyService {
    
    const PersonIdentityStore = 'PersonIdentity';
    const BarcodeCookie = 'barcodeId';
   
    /* Connect to server
     * Returns: If Person is alredy checked in - infromation about Person
     */
    public function Connect() {
        $result = $this->getServiceReuslt();
        $result->Person = $this->getPersonIdentity();
        return $result;
    }
    
    /* Check in Person by Barcode Id
     * Returns: current Person information
     */
    public function CheckIn($barcode_id) {
        $result = $this->getServiceReuslt();
        $result->Person = $this->getPersonIdentity();
        if (!$result->Person) {
            if ($barcode_id) {
                $person = Person::model()->GetByBarcodeID($barcode_id);
                if ($person) {
                    setcookie(self::BarcodeCookie, $barcode_id, time()+60*60*24*30);
                    $person_identity = (object) array('ID' => $person->ID, 'BarcodeID' => $person->BarcodeID,
                                        'FirstName' => $person->FirstName, 'LastName' => $person->LastName);
                    $this->setPersonIdentity($person_identity);
                    $result->Person = $person_identity;
                }
                else {
                    $this->setError($result, 'Invalid Barcode ID');
                }
            }
            else {
                $this->setError($result, 'Barcode ID is not specified');
            }
        }
        return $result;
    }
    
    /* Check out current Person */
    public function CheckOut() {
        $result = $this->getServiceReuslt();
        $this->setPersonIdentity(null);
        setcookie(self::BarcodeCookie, '', time() - 3600);
        return $result;
    }

    /* Save uploaded file to data base
     * Returns: fileId
     */
    public function SaveFile($file) {
        $result = $this->getServiceReuslt();
        $person = $this->getPersonIdentity();
        $result->FileId = 0;
        if (!$person){
            $this->setAccessDeniedError($result);
            return $result;
        }
        
        if ($file && ($file->type == 1 || $file->type == 2)) {           
            
            //$path = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $file->name;
            $path = tempnam(realpath(sys_get_temp_dir()), 'file');
            
            if ($fp = fopen($path, 'wb')) {
                fwrite($fp, $file->content);
                fclose($fp);
                
                $handler = new FileHandler(NULL);
                if ($file->type == 1) {//Image
                    $fileId = $handler->uploadImage($this->getFileInfo($path, $file->name, $file->mime), false);
                    $result->FileId = $fileId;
                }
                else if ($file->type == 2) {//Video
                    $fileId = $handler->uploadVideo($this->getFileInfo($path, $file->name, $file->mime), false);
                    if ($fileId) {
                        $handler->convertVideo($fileId);
                    }
                    $result->FileId = $fileId;
                }
            }
            else {
                $this->setError($result, "The file could not be created.");
            }
        }
        else {
            $this->setError($result, "Invalid file: must not be empty");
        }
        return $result;
    }
    
    /* Returns: List of available Surveys */
    public function GetSurveyList ($page_size = 20, $page_index = 1) {
        $result = $this->getServiceReuslt();
        $person = $this->getPersonIdentity();
        $result->SurveyList = array();
        if (!$person){
            $this->setAccessDeniedError($result);
            return $result;
        }

        $surveys = QuerySurvey::model()->GetListByPerson($page_size, $page_index, $person->ID);
        if ($surveys) {
            foreach($surveys as $survey) {
                $result->SurveyList[] = (object) array('title' => $survey->Title, 'surveyId' => $survey->ID,
                                        'surveyReplyId' => $survey->SurveyReplyRelation->ID,
                                        'isCompleted' => $survey->SurveyReplyRelation->IsCompleted);
            }
        }
        return $result;
    }
    
    /* Returns: List of available Anonymous Surveys */
    public function GetAnonymousSurveyList($page_size = 20, $page_index = 1) {
        $result = $this->getServiceReuslt();
        $result->SurveyList = array();
        
        $surveys = QuerySurvey::model()->GetAnonymousList($page_size, $page_index);
        if ($surveys) {
            foreach($surveys as $survey) {
                $result->SurveyList[] = (object) array('title' => $survey->Title, 'surveyId' => $survey->ID);
            }
        }
        return $result;
    }
    
    /* Returns: Survey in form: {title: string, questions: array()} */
    public function GetSurvey($survey_id) {
        $result = $this->getServiceReuslt();
        $person = $this->getPersonIdentity();
        $result->Survey = null;
        if (!$person && !QuerySurvey::model()->Anonymous($survey_id)){
            $this->setAccessDeniedError($result);
            return $result;
        }
        
        $survey = Survey::model()->findByPk($survey_id);
        if ($survey) {
            $result->Survey = (object) array('title' => $survey->Title, 'questions' => unserialize($survey->Questions));
        }
        return $result;
    }
    
    /*Get Survey Reply Item: {ID, SurveyReplyId, QuestionType, QuestionId, ImageFileId, VideoFileId, Text, Variants}*/
    public function GetSurveyReplyItem($survey_id, $survey_reply_id, $question_id) {
        $result = $this->getServiceReuslt();
        $person = $this->getPersonIdentity();
        $result->SurveyReplyItem = null;
        if (!$survey_id || !$person && !QuerySurvey::model()->Anonymous($survey_id)){
            $this->setAccessDeniedError($result);
            return $result;
        }
        
        if ($survey_reply_id) {
            $surveyReplyItem = SurveyItem::model()->GetBySurveyReplyIdAndQuestionId($survey_reply_id, $question_id);
            if ($surveyReplyItem){
                $result->SurveyReplyItem = (object) array('id' => $surveyReplyItem->ID, 'surveyReplyId' => $surveyReplyItem->SurveyReplyId,
                                        'questionType' => $surveyReplyItem->QuestionType, 'questionId' => $surveyReplyItem->QuestionId,
                                        'imageFileId' => $surveyReplyItem->ImageFileId, 'videoFileId' => $surveyReplyItem->VideoFileId,
                                        'text' => $surveyReplyItem->Text, 'variants'=> $surveyReplyItem->Variants,
                                        'mediaFileName' => null, 'mediaFileUrl' => null);
                $fileId = 0;
                if ($surveyReplyItem->QuestionType == QuestionType::IMAGE) {
                    $fileId = $surveyReplyItem->ImageFileId;
                }
                else if($surveyReplyItem->QuestionType == QuestionType::VIDEO) {
                    $fileId = $surveyReplyItem->VideoFileId;
                }
                if ($fileId) {
                    $fileInfo = File::model()->getFileInfo($fileId);
                    $result->SurveyReplyItem->mediaFileName = $fileInfo->Name;
                    $result->SurveyReplyItem->mediaFileUrl = Yii::app()->request->getBaseUrl(true) .
                        ($surveyReplyItem->QuestionType == QuestionType::IMAGE ?
                             $fileInfo->OriginalPath : $fileInfo->ConvertedPath);
                }
            }
        }
        return $result;
    }
    
    /*Save Survey Reply Item: {SurveyId, SurveyReplyId, QuestionType, QuestionId, ImageFileId, VideoFileId, Text, Variants}*/
    public function SaveSurveyReplyItem($dto, $isCompleted) {
        $result = $this->getServiceReuslt();
        $person = $this->getPersonIdentity();
        $result->SurveyReplyId = 0;
        
        $isAnonymousSurvey = QuerySurvey::model()->Anonymous($dto->SurveyId);
        if (!$dto->SurveyId || !$person && !$isAnonymousSurvey){
            $this->setAccessDeniedError($result);
            return $result;
        }
        
        if ($dto && $dto->QuestionType && $dto->QuestionId){
            $surveyReplyItem = null;
            if ($dto->SurveyReplyId){
                $survey_reply_id = $dto->SurveyReplyId;
                $question_id = $dto->QuestionId;
                
                $surveyReplyItem = SurveyItem::model()->GetBySurveyReplyIdAndQuestionId($survey_reply_id, $question_id);
                
                if(!$surveyReplyItem){
                    $surveyReplyItem = new SurveyItem();
                    $surveyReplyItem->SurveyReplyId = $dto->SurveyReplyId; 
                    $surveyReplyItem->QuestionType = $dto->QuestionType;
                    $surveyReplyItem->QuestionId = $dto->QuestionId;
                }
            }
            else if ($dto->SurveyId){
                $surveyReply = QuerySurveyReply::model()->GetBySurveyIdPersonId($dto->SurveyId, $person->ID);
                if (!$surveyReply) {
                    $surveyReply = new SurveyReply();
                    $surveyReply->SurveyId = $dto->SurveyId;
                    $surveyReply->PersonId = $person->ID;
                    $surveyReply->Save();
                }
                
                $surveyReplyItem = new SurveyItem();
                $surveyReplyItem->SurveyReplyId = $surveyReply->ID; 
                $surveyReplyItem->QuestionType = $dto->QuestionType;
                $surveyReplyItem->QuestionId = $dto->QuestionId;
            }
            
            if ($surveyReplyItem){
                $result->SurveyReplyId = $surveyReplyItem->SurveyReplyId;
                
                switch($surveyReplyItem->QuestionType){
                    case QuestionType::TEXT_INPUT:
                    case QuestionType::TEXT_AREA:{
                        $surveyReplyItem->Text = $dto->Text;
                        break;
                    }
                    case QuestionType::IMAGE:{
                        $surveyReplyItem->ImageFileId = $dto->ImageFileId ? $dto->ImageFileId : NULL;
                        break;
                    }
                    case QuestionType::VIDEO:{
                        $surveyReplyItem->VideoFileId = $dto->VideoFileId ? $dto->VideoFileId : NULL;
                        break;
                    }
                    case QuestionType::SINGLE_CHOICE:
                    case QuestionType::DROPDOWN:
                    case QuestionType::MULTIPLE_CHOICE:
                    case QuestionType::SCALE:
                    case QuestionType::SMILES:{
                        $surveyReplyItem->Variants = $dto->Variants;
                        break;
                    }
                }
                
                $transaction = $surveyReplyItem->dbConnection->beginTransaction();
                try
                {
                    $surveyReplyItem->save();
                    if ($isCompleted) {
                        SurveyReply::model()->setCompleted($result->SurveyReplyId);
                    }
                    $transaction->commit();
                }
                catch(Exception $e)
                {
                    $transaction->rollBack();
                }
            }
        }
        else {
            $this->setError($result, 'Ivalid data: QuestionType and QuestionId must be specified');
        }
        return $result;
    }
    
    //Private helper methods
    private function getPersonIdentity() {
        $result = $_SESSION[self::PersonIdentityStore];
        if (!$result && isset ($_COOKIE[self::BarcodeCookie])) {
            $person = Person::model()->GetByBarcodeID($_COOKIE[self::BarcodeCookie]);
            if ($person) {
                $person_identity = (object) array('ID' => $person->ID, 'BarcodeID' => $person->BarcodeID,
                                    'FirstName' => $person->FirstName, 'LastName' => $person->LastName);
                $this->setPersonIdentity($person_identity);
                $result = $person_identity;
            }
        }
        return $result;
    }
    
    private function setPersonIdentity($person_identity) {
        $_SESSION[self::PersonIdentityStore] = $person_identity;
    }
    
    private function getServiceReuslt() {
        $result->Succeed = TRUE;
        $result->Error = '';
        return $result;
    }
    
    private function setAccessDeniedError(&$result){
        $this->setError($result, "Access denied");
    }
    
    private function setError(&$result, $message) {
        $result->Succeed = FALSE;
        $result->Error = $message;
    }
    
    function getFileInfo($path, $name, $mime) {
        $result = array();
	$result['name'] = $name;
	$result['type'] = $mime;
	$result['tmp_name'] = $path;
	$result['error'] = 0;
	$result['size'] = filesize($path);
	return $result;
    }
}

?>
