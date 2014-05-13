<?php

class SurveyReply extends BaseModel {
    
    public $filter = "";
    
    public static function model($classname=__CLASS__){
        return parent::model($classname);
    }
    
    public function tableName(){
        return 'surveyreply';
    }
    
    public function rules() {
        return array(
            array('ID, SurveyId, PersonId, CreateDate, UpdateUserId', 'safe'),
        );
    }
    
    public function relations(){
        return array(
            'SurveyRelation'=>array (self::BELONGS_TO, 'Survey', 'SurveyId', 'select'=>'Title'),
            'PersonRelation'=>array (self::BELONGS_TO, 'QueryPerson', 'PersonId', 'select'=>'*'),
        );
    }
    
    public function search($pageSize=20){
        $criteria=new CDbCriteria;
        
        $with = array();
        
        $with['PersonRelation'] = array();
        $with['SurveyRelation'] = array();
        $criteria->with = $with;
        
        $criteria->compare('PersonRelation.LastName', $this->filter, true, 'OR');
        $criteria->compare('PersonRelation.FirstName', $this->filter, true, 'OR');
        $criteria->compare('SurveyRelation.Title', $this->filter, true, 'OR');
        
        return new CActiveDataProvider($this, array('criteria'=>$criteria, 'sort'=>$sort, 'pagination'=>array('pageSize'=>$pageSize,)));
    }
    
    
    public function toSurvey($sessionID){
        $criteria=new CDbCriteria;
        
        $criteria->condition = 'SurveyId.Sessionid = '.(int)$sessionID;  
//        $criteria->condition = 'SurveyRelation.Sessionid = '.(int)$sessionID;  
        $with = array();
        $with['SurveyRelation'] = array();
        $criteria->with = $with;
        
        return new CActiveDataProvider($this, array('criteria'=>$criteria));
    }
    
    public function setCompleted($id) {
        if ($id) {
            $this->updateAll(array('IsCompleted' => 1, 'CreateDate' => date(Localization::SERVER_DATETIME_FORMAT)),
                             'ID = :id AND IsCompleted = 0',
                             array(':id' => $id ));
        }
    }
}

?>
