<?php

class HouseholdController extends BaseManageController{
    public function secondLevelNavigationType() {
        return NavigationType::MANAGE_HOUSEHOLDS;
    }
    
    public function filters(){
        return array('accessControl',);
    }
    
    public function accessRules() {
        return array(
            array('allow', 'users'=>array('@')),
            array('deny', 'users'=>array('*')),
            );
    }

    public function actionExcludePerson() { 
        $personId = $_POST['personId'];
        $person = Person::model()->findByPk($personId);
        Person::model()->updateByPk($personId, array('Household'=>null));
        $model=$this->loadModel($person->Household);
        $this->render('edit', array('model'=>$model));
    }
    
    public function actionEdit($personHouseholdId = NULL) { 
        $householdId;

        if (isset($_POST['householdId'])){
            $householdId = $_POST['householdId'];
        } elseif (isset($personHouseholdId)) {
            $householdId = $personHouseholdId;
        }

        $model = $householdId ? $this->loadModel($householdId) : new QueryHousehold();
        if(isset($_POST['QueryHousehold'])){
            $model->attributes=$_POST['QueryHousehold'];
            if ($_POST['cancel']) {
                $this->redirect(array('index'));
            }
            if($model->save()){
                $this->redirect(array('index'));
            }
        }
        $this->render('edit',array('model'=>$model));    
    }
	
    public function actionDelete() {
        $householdId = $_POST['householdId'];
        if(Yii::app()->request->isPostRequest) {
            $this->loadModel($householdId)->delete();

            if(!isset($_GET['ajax'])){
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
            }
        }
        else{
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }            
    }

    public function actionIndex() {
        $model=new QueryHousehold();
        $model->unsetAttributes();  // clear any default values
        
        if(isset($_GET['QueryHousehold'])) {
            $model->attributes=$_GET['QueryHousehold'];
        }
        if(isset($_GET['filter'])){
            $model->filter=$_GET['filter'];
        }

        $this->render('index',array('model'=>$model));
    }

    public function loadModel($id) {
        $model=QueryHousehold::model()->findByPk($id);
        $model->getLocation();

        if($model===null) {
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $model;
    }

    protected function performAjaxValidation($model) {
        if(isset($_POST['ajax']) && $_POST['ajax']==='household-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
