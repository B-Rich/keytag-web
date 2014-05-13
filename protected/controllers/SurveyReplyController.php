<?php

class SurveyReplyController extends BaseSurveyReplyController {
    
    public function accessRules(){
        return array(array('allow', 'users'=>array('@')),array('deny', 'users'=>array('*')),);
    }
    
    public function actionIndex(){
        $model=new QuerySurveyReply();
        $model->unsetAttributes();
        if(isset($_GET['QuerySurveyReply'])){
            $model->attributes=$_GET['QuerySurveyReply'];
        }
        if(isset($_GET['filter'])){
            $model->filter=$_GET['filter'];
        }
        if(isset($_GET['session']) && $_GET['program'] != ''){
            $model = $model->bySession();
        }
        $this->render('index', array('model'=>$model));
    }
    
    public function actionEvaluate(){
        
        $surveyReplyId;

        if (isset($_POST['surveyReplyId'])){
            $surveyReplyId = $_POST['surveyReplyId'];
        }

        $model = $surveyReplyId ? $this->loadModel($surveyReplyId) : new QuerySurveyReply();
        
        /* Get array of related item objects */
        $items = array();
        if ($surveyReplyId) {
            $items = QuerySurveyItem::model()->toEvaluate($surveyReplyId); //QuerySurveyItem::model()->findAll('SurveyReplyId ='. $surveyReplyId);
        }
        $questions_number = count($items);

        $this->render('edit', array('model'=>$model, 'items' => $items, 'questions_number' => $questions_number));    
    }
    
    public function actionSaveEvaluation(){
        $result = array();
        
        if (isset($_POST['surveyreply_id'])){
            $surveyReplyId = $_POST['surveyreply_id'];
        }

        if (isset($_POST['question_id'])){
            $questionId = $_POST['question_id'];
        }

        if (isset($_POST['points'])){
            $points = $_POST['points'];
        }
       
        $currentItem = QuerySurveyItem::model()->findByAttributes(array('SurveyReplyId'=>$surveyReplyId, 'QuestionId'=>$questionId));
        $currentItem->Points = $points;
                
        if ($currentItem->save()) {
           if ((int)$_POST['is_finish'] == 1) {
               $result = (object)array('finish'=> true);
           } else {
               $result = (object)array('points'=>$currentItem->Points);
                           
           }   
           $this->ajaxResopnse($result);    
        }
    }

    // Temp csv survey report 
    public function actionReporttmp(){
        Yii::import('ext.ECSVExport');

        $sql = 'SELECT s.Title, s.Questions, sr.CreateDate, p.BarCodeID, sri.QuestionId, sri.Points, sri.Variants, sri.ImageFileId, sri.VideoFileId, sri.Text
                    FROM surveyreply sr
                        INNER JOIN surveyreplyitem sri ON sr.ID = sri.SurveyReplyId
                        INNER JOIN persons p ON p.ID = sr.PersonId
                        INNER JOIN survey s ON sr.SurveyID = s.ID';

        $report = array();

        $results = Yii::app()->db->createCommand($sql)->queryAll();

        foreach ($results as $res) {
            $q = unserialize($res['Questions']);
            $qq = $q[$res['QuestionId'] - 1];

            $resultVariants = '';
            if( !empty($res['Variants']) ) {
                foreach ( explode(',',$res['Variants']) as $variant ) {

                        if( isset($qq->variants) && ($qq->type != 6) && is_array($qq->variants) ) {
                            $key = $variant - 1;
                            $resultVariants .= $qq->variants[$key]->title.'; ';
                        } 
                        else {
                            $resultVariants .= $variant.'; ';
                        }
                }
            }
            
            $report[] = array(
                'Title' => $res['Title'],
                'BarCodeID' => $res['BarCodeID'],
                'CreateDate' => $res['CreateDate'],
                'Points' => $res['Points'],
                'Question' => $qq->title,
                'Answers'   => $resultVariants,
                'AnserImage' => $res['ImageFileId'],
                'AnserVideo' => $res['VideoFileId'],
                'AnserText' => $res['Text'],
                );
        }

        // var_dump($report);die;

        $csv = new ECSVExport($report);

        $outputCsv = $csv->toCSV(); 

        Yii::app()->getRequest()->sendFile('surveyAnswers.csv', $outputCsv, "text/csv", false);
        exit();
    }
    
    public function actionGetItem($questionId){
        
        if (isset($_POST['surveyReplyId'])){
            $surveyReplyId = $_POST['surveyReplyId'];
        }

        $model = $surveyReplyId ? $this->loadModel($surveyReplyId) : new QuerySurveyReply();

        /* Get array of related item objects */
        $items = array();
        if ($surveyReplyId) {
            $items = QuerySurveyItem::model()->toEvaluate($surveyReplyId); //QuerySurveyItem::model()->findAll('SurveyReplyId ='. $surveyReplyId);    
        }
        
        $questions_number = count($items);

        $this->render('edit', array('model'=>$model, 'item' => $items[0], 'questions_number' => $questions_number));    
        
    }    
    
    public function loadModel($id){	
        $model = QuerySurveyReply::model()->findByPk($id);
     //   print_r ($model); die();
        if($model===null){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $model;
    }
    
    public function actionSessionsByProgram() {
        $data=DomainSession::model()->findAll('Program=:program_id',
                      array(':program_id'=>(int) $_POST['program_id']), 'ID', 'Description');

        if (!empty($data)) {
            $data=CHtml::listData($data,'ID','Description');
        } else {
            $data = ReportsController::unionArrays(array("_any" => "any") , $data);    
        }
        
        foreach($data as $value=>$name) {
            echo CHtml::tag('option',
                array('value'=>$value),CHtml::encode($name),true);
        }
    }

    public function actionError() {
        $error=Yii::app()->errorHandler->error;
        if(!$error) {
            $this->redirectToHome();
            return;
        }
        Yii::log($error);
        $this->render('error');
    }
}
?>
