<?php

class MiscController extends Controller {
    public function accessRules(){
        return array(
            array('allow', 'users'=>array('@')),
            array('deny', 'users'=>array('*')),
            );
    }
    
    public function actionSessionMultiSelect() {
        $data = QuerySession::model()->SelectListByProgramId((int) $_POST['program_id']);
        $data = CHtml::listData($data, 'ID', 'Description');
        $flag = FALSE;
        if (isset ($_POST['flag'])) {
            $flag = (bool)$_POST['flag'];
        }
        $this->renderPartial('sessionmultiselect', array('data'=>$data, 'flag'=>$flag), false, true);
    }
    
    public function actionSessionSelect() {
        $input_name = 'SessionId';
        if (isset($_POST['input_name'])) {
            $input_name = $_POST['input_name'];
        }
        $data = QuerySession::model()->SelectListByProgramId((int) $_POST['program_id']);
        $data = CHtml::listData($data, 'ID', 'Description');
        $this->renderPartial('sessionselect', array('input_name'=>$input_name, 'data'=>$data), false, true);
    }

    public function actionSurveyMultiSelect() {
        $sessions = array();

        if( isset($_POST['sessions']) && !empty($_POST['sessions']) && $_POST['sessions'] != 'null' ) {
            $sessions = $_POST['sessions'];
        }

        $data = QuerySurvey::model()->SelectListBySessions($sessions);
        $data = CHtml::listData($data, 'ID', 'Title');

        $this->renderPartial('/misc/surveymultiselect', array('data'=>$data, 'flag'=>TRUE), false, true);
    }
}

?>
