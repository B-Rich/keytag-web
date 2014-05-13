<?php

class QueryPersonSynchronizationResult extends CActiveRecord {
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return 'personsynchronizationresult';
    }
    
    public function relations(){
        return array('PersonRelation' => array(self::BELONGS_TO, 'QueryPerson', 'PersonId', 'select'=>'LastName, FirstName'));
    }
    
    public function attributeLabels(){
        return array(
            'CompleteDate' => 'Complete Date',
            'IsSucceed' => 'Successful',
            'Details' => 'Details',
        );
    }
    
    public function search($pageSize=20) {
        $criteria = new CDbCriteria;
        $criteria->select = 'CompleteDate, IsSucceed, Details';
        $criteria->with = array('PersonRelation');
        
        return new CActiveDataProvider($this, array('criteria'=>$criteria, 'pagination'=>array('pageSize'=>$pageSize,)));
    }
}

?>
