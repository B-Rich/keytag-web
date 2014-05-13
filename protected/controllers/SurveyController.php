<?php

class SurveyController extends BaseManageController {
    public function secondLevelNavigationType() {
        return NavigationType::MANAGE_SURVEYS;
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
    
    public function actionEdit() {        
        $surveyId;
        
        if (isset($_POST['surveyId'])){
            $surveyId = $_POST['surveyId'];
        }
        
        $model = $surveyId ? Survey::model()->findByPk($surveyId) : new Survey;
        
        if(isset($_POST['Survey'])){
            if ($_POST['cancel']) {
                $this->redirect(array('index'));
            }
            $model->attributes = $_POST['Survey'];
            $model->ProgramId = $_POST['ProgramId'];
            $model->JsonQuestions = $_POST['JsonQuestions'];
            if($model->save()){
                $this->redirect(array('index'));
            }
        }
        
        $this->render('edit', array('model'=>$model));
    }
    
    public function actionDelete(){
        $surveyId = $_POST['surveyId'];
        if(Yii::app()->request->isPostRequest)
        {
            if (!QuerySurvey::model()->IsAlreadyReplied($surveyId)) {
                $this->loadModel($surveyId)->delete();
            }
            if(!isset($_GET['ajax'])){
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
            }
        }
        else{
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionIndex() {
        $model=new QuerySurvey();
        $model->unsetAttributes();
        if(isset($_GET['QuerySurvey'])){
            $model->attributes=$_GET['QuerySurvey'];
        }
        $this->render('index', array('model'=>$model));
    }
    
    public function loadModel($id){
        $model=Survey::model()->findByPk($id);
        if($model===null){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $model;
    }
}

?>
