<?php

class SurveyItem extends BaseModel {
    public static function model($classname=__CLASS__){
        return parent::model($classname);
    }
    
    public function tableName(){
        return 'surveyreplyitem';
    }
    
    public function rules() {
        return array(
            array('ID, SurveyReplyId, QuestionType, QuestionId, ImageFileId, VideoFileId, Text, Variants, Points', 'safe'),
        );
    }
    
    public function GetBySurveyReplyIdAndQuestionId($survey_reply_id, $question_id) {
        return $this->find('SurveyReplyId = :survey_reply_id AND QuestionId = :question_id',
                    array(':survey_reply_id' => $survey_reply_id, ':question_id' => $question_id));
    }
}

?>
