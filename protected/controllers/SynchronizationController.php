<?php

class SynchronizationController extends BaseManageController {
    
    public function secondLevelNavigationType() {
        return NavigationType::MANAGE_SYNCHRONIZATION;
    }
    
    public function accessRules(){
        return array(
            array('allow', 'users'=>array('*'),
                'actions' => array('SynchronizeWithCuriosityMachineInternal'),
                'ips' => array('127.0.0.1', $_SERVER['SERVER_ADDR']),
            ),
            array('allow', 'users'=>array('@'), 'expression'=>'UserIdentity::IsAdmin()'),
            array('deny', 'users'=>array('*')),
        );
    }
    
    public function actionCheckSynchronization() {
        $worker = new SynchronizationWorker(Yii::app()->user->id);
        $result->runUrl = $this->createAbsoluteUrl('synchronization/synchronizeWithCuriosityMachine');
        $result->redirectUrl = $this->createAbsoluteUrl('synchronization/index');
        $result->isRunning = $worker->IsRunning();
        $result->estimatedCount = QueryPerson::model()->GetPersonsCountToSync(Yii::app()->user->id);
        $this->ajaxResopnse($result);
    }

    public function actionSynchronizeWithCuriosityMachine() {
        $request = Yii::app()->request;
        $url = $this->createAbsoluteUrl('synchronization/synchronizeWithCuriosityMachineInternal', array ('userId' => Yii::app()->user->id));
        CommonHelper::SendAsync($url);
    }
    
    public function actionSynchronizeWithCuriosityMachineInternal($userId) {
        set_time_limit(0);
        $worker = new SynchronizationWorker($userId);
        if (!$worker->IsRunning()) {
            $worker->Start();
        }
    }

    public function actionIndex() {
        $model=new QueryPersonSynchronizationResult();
        $model->unsetAttributes();
        if(isset($_GET['QueryPersonSynchronizationResult'])){
            $model->attributes=$_GET['QueryPersonSynchronizationResult'];
        }
        $this->render('index', array('model'=>$model,));
    }
    
    public function actionTest() {
        var_dump(Locations::model()->GetLocationByPersonId(1242));
    }
}

?>
