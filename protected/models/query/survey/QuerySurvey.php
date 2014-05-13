<?php

class QuerySurvey extends CActiveRecord {
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()	{
        return 'survey';
    }
    
    public function relations(){
        return
            array(
                'SessionRelation' => array(self::BELONGS_TO, 'DomainSession', 'SessionId', 'select' => 'Description'),
                'SurveyReplyRelation' => array(self::HAS_ONE, 'SurveyReply', 'SurveyId', 'select' => 'ID, PersonId, IsCompleted'),
                'ProgramRelation' => array(self::HAS_ONE, 'DomainProgram', 'Program', 'through' => 'SessionRelation',
                                            'joinType' => 'LEFT JOIN', 'select' => 'Description'),
            );
    }
    
    public function attributeLabels(){
        return array(
            'ID' => 'Id',
            'Title' => 'Title',
            'Description' => 'Description',
        );
    }
    
    public function search($pageSize=20){
        $criteria=new CDbCriteria;
        $sort = new CSort;
        
        $criteria->select = 'ID, Title';
        $criteria->with = array('SessionRelation', 'ProgramRelation');

        /*$sort->attributes = array(
            'ProgramType'=>array(
                'asc'=>'ProgramTypeRelation.Name ASC',
                'desc'=>'ProgramTypeRelation.Name DESC',
            ),
            '*',
        );*/
        
        return new CActiveDataProvider($this, array('criteria'=>$criteria, 'sort'=>$sort, 'pagination'=>array('pageSize'=>$pageSize,)));
    }
    
    public function GetListByPerson($page_size = 20, $page_index = 1, $person_id){
        $limit = $page_size;
        $offset = ($page_index - 1) * $page_size;
        
        $criteria = new CDbCriteria;
        $criteria->limit = $limit;
        $criteria->offset = $offset;
        $criteria->select = 'ID, Title';
        $criteria->with = array('SurveyReplyRelation' =>
            array('on' => 'PersonId = :person_id',
                  'alias' => 'sr',
                  'params' => array(':person_id' => $person_id)));
        $criteria->join = 'INNER JOIN attendance a ON a.Session = SessionId AND Person = :person_id';
        $criteria->condition = "sr.CreateDate IS NULL OR TIME_TO_SEC(TIMEDIFF(:currentDate, sr.CreateDate)) <= :finishDelay";
        $criteria->params = array(':person_id' => $person_id,
                                  ':currentDate' => date(Localization::SERVER_DATETIME_FORMAT),
                                  ':finishDelay' => 7200);
        $criteria->order = 'sr.ID desc, Title';
        
        return $this->findAll($criteria);
    }
    
    public function GetAnonymousList($page_size = 20, $page_index = 1) {
        $limit = $page_size;
        $offset = ($page_index - 1) * $page_size;
        
        $criteria = new CDbCriteria;
        $criteria->limit = $limit;
        $criteria->offset = $offset;
        $criteria->select = 'ID, Title';
        $criteria->condition = 'IsAnonymous = 1';
        $criteria->order = 'LastUpdated desc';
        
        return $this->findAll($criteria);
    }
    
    public function DoesAlreadyExist($survey_id, $session_id) {
        $id = $survey_id ? $survey_id : 0;
        return intval($this->count('SessionId = :session_id AND ID <> :id AND Enabled = 1',
                        array(':session_id' => $session_id, ':id' => $id))) > 0;
    }
    
    public function IsAlreadyReplied($survey_id) {
        if ($survey_id) {
            $criteria = new CDbCriteria;
            $criteria->with = array('SurveyReplyRelation' => array('joinType' => 'INNER JOIN'));
            $criteria->compare('t.ID', $survey_id);

            return intval($this->count($criteria)) > 0;
        }
        return false;
    }
    
    public function Anonymous($id) {
        $command = Yii::app()->db->createCommand();
        $result = $command->select('IsAnonymous')
                          ->from($this->tableName())
                          ->where('ID = :id', array(':id' => $id))->queryScalar();
        return intval($result) == 1;
    }

    public function SelectListBySessions($sessions) {
        $criteria = new CDbCriteria();
        $criteria->addInCondition("SessionId", $sessions);
        return $this->findAll($criteria);
    }
}

?>
