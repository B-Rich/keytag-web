<?php

class Survey extends BaseModel {
    public $ProgramId;
    public $JsonQuestions;
    
    public static function model($classname=__CLASS__){
        return parent::model($classname);
    }
    
    public function tableName(){
        return 'survey';
    }
    
    public function rules() {
        return array(
            array('Title', 'required'),
            array('Title', 'length', 'max' => 50),
            array('SessionId', 'ValidateSessionId'),
            array('Description', 'length', 'max' => 255),
            array('Questions', 'application.extensions.Validators.SerializedArrayNotEmpty'),
            array('ID, Title, Description, Questions, SessionId, Enabled, IsAnonymous, UpdateUserId, LastUpdated', 'safe'),
        );
    }
    
    public function relations(){
        return array('SessionRelation' => array(self::BELONGS_TO, 'DomainSession', 'SessionId', 'select' => 'Program'),);
    }
    
    public function attributeLabels(){
        return array(
            'ID' => 'Id',
            'Title' => 'Title',
            'Description' => 'Description',
            'IsAnonymous' => 'Anonymous',
            'SessionId' => 'Session',
        );
    }
    
    public function ValidateSessionId($attribute, $params) {
        if (!$this->IsAnonymous) {
            $ev = CValidator::createValidator('required', $this, $attribute, $params);
            $ev->validate($this);
        }
    }
    
    public function beforeValidate(){
        $this->Questions = $this->jsonToPHP($this->JsonQuestions);
        /*if (QuerySurvey::model()->DoesAlreadyExist($this->ID, $this->SessionId)) {
            $this->addError('SessionId', 'Another Survey for selected Session already exists');
            return false;
        }*/
        if (QuerySurvey::model()->IsAlreadyReplied($this->ID)) {
            $this->addError('ID', 'There is Reply on this Survey');
            return false;
        }
        return parent::beforeValidate();
    }

    public function beforeSave() {
        $this->SetUpdateInfo();
        if (empty ($this->SessionId) || $this->IsAnonymous) {
            $this->SessionId = NULL;
        }
        return parent::beforeSave();
    }
    
    public function afterFind(){
        $this->ProgramId = $this->SessionRelation->Program;
        if ($this->Questions) {
            $questions = unserialize($this->Questions);
            $this->JsonQuestions = json_encode($questions);
        }
    }
    
    public function afterConstruct() {
        $this->JsonQuestions = json_encode(array());
    }
}

?>
