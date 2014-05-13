<?php

class QuerySurveyItem extends CActiveRecord {
    
    public $filter = "";
    
    public static function model($classname=__CLASS__){
        return parent::model($classname);
    }
    
    public function tableName(){
        return 'surveyreplyitem';
    }

    public function rules() {
        return array(
            array('Points', 'numerical', 'integerOnly'=>true),
            array('Points', 'length', 'max'=>6),
            array('Points', 'match', 'pattern' => '/^[0-9]+$/', 'message'=>'Score must contain numbers'),
           ); 
    }
    
    public function relations(){
        return array(
            'SurveyReplyRelation'=>array (self::BELONGS_TO, 'QuerySurveyReply', 'SurveyReplyId', 'select'=>'*'),            
        );
    }

    public function scopes(){
        
        return array('bySurveyReply' => array('condition' => 'SurveyReplyId = :reply_id', 'params' => array(':reply_id'=>(int)$_POST['surveyReplyId'])));
    }
    
    public function search($pageSize=20){
        $criteria=new CDbCriteria;
        
        $with = array();
        
        $with['SurveyReplyRelation'] = array();
        $criteria->with = $with; 
        
        return new CActiveDataProvider($this, array('criteria'=>$criteria));
    }
    
    public function toEvaluate($surveyReplyId){
        
        $criteria=new CDbCriteria;
        $questionTypes = array(QuestionType::IMAGE,QuestionType::TEXT_AREA,QuestionType::TEXT_INPUT,QuestionType::VIDEO);
        
        $criteria->addCondition('SurveyReplyId = '.$surveyReplyId);         
        $criteria->addInCondition('QuestionType', $questionTypes);
      //  $criteria->params = array(':surveyReplyId' => $surveyReplyId); // ':questionTypes' => $questionTypes,          
        
        return $this->findAll($criteria); 
    }
    
    public function getAnswersById($surveyReplyId) {
        //$answers = Yii::app()->db->createCommand()->select()->from('surveyreplyitem')->where('SurveyReplyId='.$surveyReplyId)->order('questionId ASC')->queryAll();
        $criteria = new CDbCriteria();
        $criteria->condition = 'SurveyReplyId=:SurveyReplyId';
        $criteria->params = array(':SurveyReplyId'=>$surveyReplyId);
        $criteria->order = 'questionId ASC';

        $answers = self::model()->findAll($criteria);

        return $answers;
    }   

    public function getUserSurveyAnswers($personId, $surveyId) {
        
        $answer = Yii::app()->db->createCommand()
                ->select('sri.*, sr.CreateDate as replyDate')
                ->from('surveyreplyitem AS sri')
                ->join('surveyreply AS sr', 'sri.SurveyReplyId = sr.ID')
                ->where('sr.SurveyId = '.$surveyId.' AND sr.PersonId = '.$personId)
                ->order('questionId ASC')
                ->queryAll();

        return $answer;
    } 

    public function  getUserSurveyAnswerByQuestion($personId, $surveyId, $questionId) {
        
        $answer = Yii::app()->db->createCommand()
                ->select('sri.*, sr.CreateDate as replyDate')
                ->from('surveyreplyitem AS sri')
                ->join('surveyreply AS sr', 'sri.SurveyReplyId = sr.ID')
                ->where('sr.SurveyId = '.$surveyId.' AND sr.PersonId = '.$personId.' AND QuestionId = '.$questionId)
                ->order('questionId ASC')
                ->queryRow();

        return $answer;
    }
}

?>
