<?php

class QuerySurveyReply extends CActiveRecord {
    
    public $filter;
    public $score;
    
    public static function model($classname=__CLASS__){
        return parent::model($classname);
    }
    
    public function tableName(){
        return 'surveyreply';
    }
    
    public function relations(){
        return array(
            'SurveyRelation'=>array (self::BELONGS_TO, 'Survey', 'SurveyId', 'select'=>'Title'),
            'SurveyItemRelation'=>array (self::HAS_MANY, 'SurveyItem', 'SurveyReplyId', 'together' => true, 'select' => 'Points'),
            'PersonRelation'=>array (self::BELONGS_TO, 'QueryPerson', 'PersonId', 'select'=>'LastName, FirstName'),
            'PersonRelation_1'=>array (self::BELONGS_TO, 'QueryPerson', 'PersonId', 'select'=>'*'),
            'HouseholdRelation'=>array (self::HAS_ONE, 'QueryHousehold', 'Household', 'through' => 'PersonRelation', 'select' => 'Name'),
            'RoleRelation'=>array (self::HAS_ONE, 'PersonType', 'Type', 'through' => 'PersonRelation_1', 'joinType' => 'INNER JOIN', 'select' => 'Name')
        );
    }

    public function scopes(){    
        return array('bySession' => array('condition' => 'SessionId = :session_id', 'params' => array(':session_id'=>(int)$_GET['session']),));
    }
    
    public function search($pageSize=20){
        $criteria=new CDbCriteria;
        
        $with = array();
        $with['SurveyRelation'] = array();
        $with['SurveyItemRelation'] = array();      
        $with['PersonRelation'] = array();
        $with['HouseholdRelation'] = array();
        $with['RoleRelation'] = array();
        
        $criteria->with = $with;
        $criteria->select = array('*', "IF (SUM(SurveyItemRelation.Points) is not NULL, SUM(SurveyItemRelation.Points), 0) AS score");        
        $criteria->group = "PersonId, SurveyId";
        $criteria->condition = "(PersonRelation.LastName LIKE :filter OR PersonRelation.FirstName LIKE :filter
                                 OR SurveyRelation.Title LIKE :filter) AND IsAnonymous = 0 AND IsCompleted = 1";
        $criteria->params = array(':filter' => '%'.$this->filter.'%');
       
        $sort = new CSort();
        $sort->attributes = array(
            'Name'=>array(
                'asc'=>'PersonRelation.LastName ASC',
                'desc'=>'PersonRelation.LastName DESC',
            ),
            'Role'=>array(
                'asc'=>'RoleRelation.Name ASC',
                'desc'=>'RoleRelation.Name DESC',
            ),
            'Household'=>array(
                'asc'=>'HouseholdRelation.Name ASC',
                'desc'=>'HouseholdRelation.Name DESC',
            ),

            'Survey'=>array(
                'asc'=>'SurveyRelation.Title ASC',
                'desc'=>'SurveyRelation.Title DESC',
            ),
            'Score'=>array(
                'asc'=>'score ASC',
                'desc'=>'score DESC',
            ),
        );
        return  new CActiveDataProvider($this, array('criteria'=>$criteria, 'sort'=>$sort, 'pagination'=>array('pageSize'=>$pageSize,)));
    }
        
    public function itemsToEvaluate($replyId){
        $criteria=new CDbCriteria;
        
        $criteria->condition = 'ID_ = '.(int)$replyId;  
        $with = array();
        $with['SurveyItemRelation_'] = array();
        $criteria->with = $with;
        
        return new CActiveDataProvider($this, array('criteria'=>$criteria));
    }
    
    public function GetBySurveyIdPersonId($survey_id, $person_id) {
        $criteria = new CDbCriteria;
        
        $criteria->condition = 'SurveyId = :survey_id AND PersonId = :person_id';
        $criteria->params = array(':survey_id' => $survey_id, ':person_id' => $person_id);
        
        $result = $this->findAll($criteria);
        if ($result && count($result) > 0){
            return $result[0];
        }
        return null;
    }

    public function getReplies($surveyId){
        // $criteria = new CDbCriteria;
        
        // $criteria->with = 'PersonRelation';
        // $criteria->select =  array('*');
        // $criteria->condition = 'SurveyId = :survey_id';
        // $criteria->params = array(':survey_id' => $surveyId);
        
        // $result = QuerySurveyReply::model()->findAll($criteria);

        // return $result;
        $persons = Yii::app()->db->createCommand()
                        ->select('sr.*, p.BarCodeID, p.LastName, p.FirstName, p.EmailAddress, p.WorkPhone, hh.ZIPPostal, c.Name as City, s.Name as School')
                        ->from('surveyreply AS sr')->join('persons AS p', 'sr.PersonId = p.ID')
                        ->leftjoin('household AS hh', 'p.Household = hh.ID')
                        ->leftjoin('locations as loc', 'hh.Location = loc.ID')
                        ->leftjoin('city as c', 'loc.City = c.ID')
                        ->leftjoin('schools as s', 'p.School = s.ID')
                        ->where('surveyId='.$surveyId)->queryAll();

        return $persons;
    }

    public function getUsersBySurveys($surveys){

        $usersId = Yii::app()->db->createCommand()
                        ->select('sr.*, p.BarCodeID, p.LastName, p.FirstName, p.EmailAddress, 
                                COALESCE(p.MobilePhone, p.HomePhone, p.WorkPhone) AS Phone, hh.ZIPPostal, 
                                c.Name as City, s.Name as School, 
                                COALESCE(CONCAT_WS(" ",hh.Emergency1FirstName,hh.Emergency1LastName), CONCAT_WS(" ",hh.Emergency2FirstName,hh.Emergency2LastName)) AS Parent, 
                                (YEAR(CURRENT_DATE)-YEAR(p.DateOfBirth))-(RIGHT(CURRENT_DATE,5)<RIGHT(p.DateOfBirth,5)) AS Age') 
                        ->from('surveyreply AS sr')->join('persons AS p', 'sr.PersonId = p.ID')
                        ->leftjoin('household AS hh', 'p.Household = hh.ID')
                        ->leftjoin('locations as loc', 'hh.Location = loc.ID')
                        ->leftjoin('city as c', 'loc.City = c.ID')
                        ->leftjoin('schools as s', 'p.School = s.ID')
                        ->where('surveyId IN ('. implode(',', $surveys) .')')
                        ->group('PersonId')
                        ->queryAll();//text
        // echo $usersId; die;
        return $usersId;
    }

    public function getMaxQuestionIndex($surveys){

        $maxQuestionId = Yii::app()->db->createCommand()
                        ->select('max(sri.QuestionId)') 
                        ->from('surveyreply AS sr')->join('surveyreplyitem AS sri', 'sr.ID = sri.SurveyReplyId')
                        ->where('surveyId IN ('. implode(',', $surveys) .')')
                        ->queryScalar();

        return $maxQuestionId;
    }
}

?>
