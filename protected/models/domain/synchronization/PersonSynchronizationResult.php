<?php

class PersonSynchronizationResult extends BaseModel {
    
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return 'personsynchronizationresult';
    }
    
    public function rules() {
        return array(
            array('PersonId, UserId, CompleteDate, IsSucceed, Details', 'safe'),
        );
    }
    
    public function ClearPreviousResults($jobId, $userId) {
        $sql = 'DELETE psr '.
               'FROM personsynchronizationresult psr ' .
               'INNER JOIN jobuser ju ON psr.UserId = ju.UserId AND ju.JobId = :jobId AND ju.IsRunning = 0 ' .
               'INNER JOIN persons p ON psr.PersonId = p.ID ' .
               'INNER JOIN household h ON p.Household = h.ID ' .
               'INNER JOIN locations l ON h.Location = l.ID ' .
               'WHERE ' . Locations::model()->getLocationClause($userId);
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(':jobId', $jobId);
        $command->execute();
    }

    public function beforeSave() {
        $this->CompleteDate = $this->getCurrentDateTime();
        return parent::beforeSave();
    }
}

?>
