<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vkharyt
 * Date: 1/6/12
 * Time: 5:53 PM
 * To change this template use File | Settings | File Templates.
 */
Yii::import('application.extensions.phpexcel.JPhpExcel');

class ReportsController extends BaseReportingController
{
    public $layout='//layouts/reporting';
    private $report;
    private $excelReport;
    public $buttons;
    public $isSurveyReport = false;
    
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array('allow', 'users'=>array('@')),
            array('deny', 'users'=>array('*')),
        );
    }

    public function actionProgramSessionsPersons()
    {
        $this->report =  new ProgramsReport; //new AttendanceReport;
        $this->layout = '//layouts/view_reporting'; //'//layouts/view_reporting_bare';
        $programId = $_POST['program'];
        $sessions = $_POST['sessions'];
        $schools = $_POST['schools'];
        $sites = $_POST['sites'];
        
        if(isset($_POST['fromDate']) and ($_POST['fromDate'] != NULL)) {
            $fromDate = date ('Y-m-d', strtotime($_POST['fromDate']));     
        } 

        if(isset($_POST['toDate']) and ($_POST['toDate'] != NULL)) {
            $fromDate = date ('Y-m-d', strtotime($_POST['fromDate']));     
        }
        
       
        $showHours = $_POST['showHours'];
        $viewSummary = $_POST['viewSummary'];
        $perfectAttendance = $_POST['perfectAttendance'];
        
        // $dataToExport = 
        $this->render('ProgramsReport', array( //'ProgramSessionsPersons',array(
            'model'=>$this->report->report($programId, $sessions, $schools, $sites, $fromDate, $toDate, $showHours, $perfectAttendance),
            'programId'=>$programId,
            'sessions'=>$sessions,
            'schools'=>$schools,
            'sites'=>$sites,
            'fromDate'=>$fromDate,
            'toDate'=>$toDate,
            'showHours'=>$showHours,
            'viewSummary'=>$viewSummary,
            'perfectAttendance'=>$perfectAttendance
        ));//, true); 
        
       // $this->actionExcel($dataToExport);
        
        
    } 

    public function actionExcel () //($dataToExport)
    {    
     
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=Report_".date('Y-m-d', time()).'_'.date('H', time()).'-'.date('i', time()).".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

      /*  $csv_output ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
                        <head>
                        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
                        <meta name="author" content="Iridescent" />
                        <title></title>
                        </head>
                        <body>';
        */                
        $csv_output = $_POST['data'];

        $csv_output .='</body></html>';

        print $csv_output; //$dataToExport; 
       
    }    
    
    public function actionIndex()
    { 
        $this->render('reports');
    }

    public function actionSurvey()
    { 
        $this->layout='//layouts/reporting_survey';

        $this->render('SurveyReport', array('userInfo' => $this->userInfo));
    }

    public function actionSurveyReport()
    {
        $this->layout = '//layouts/view_reporting';
        $this->isSurveyReport = true;

        if( !isset($_POST['surveys']) || empty($_POST['surveys']) ) {
            throw new CException('surveys is empty');
        } else {
            $surveysId = $_POST['surveys'];
            if( !is_array($surveysId) ) {
                $surveysId = explode(',', $_POST['surveys']);
            }
        }

        $userInfo = array();
        if( isset($_POST['user_info']) )
        {
            $userInfo = $_POST['user_info'];
            if( !is_array($userInfo) ) {
                $userInfo = explode(',', $_POST['user_info']);
            }
        }
        
        $reportTitle = 'Survey Reports: ';
        $reportSurvey = array();

        $collated = false;
        if( isset($_POST['collated']) && !empty($_POST['collated']) )
        {
            $collated = true;
        }

        $allSurveys = QuerySurvey::model()->findAllByPk($surveysId);
        $allPersons = QuerySurveyReply::getUsersBySurveys($surveysId);

        
        // Table Header
        $tableHeader = array();
        $tableHeader[0] = 'No.'; 
        $tableHeader[1] = 'First Last'; 

        if( !$collated ) {
            // Default Report
            $this->gererateReportHeader($allSurveys, $userInfo, $tableHeader, $reportTitle);
            $this->gererateReport($allSurveys, $allPersons, $userInfo, $reportSurvey);
        } else {
            // Collated Report
            $maxQuestionIndex = QuerySurveyReply::getMaxQuestionIndex($surveysId);
            $this->gererateCollatedReportHeader($allSurveys, $userInfo, $tableHeader, $reportTitle, $maxQuestionIndex);
            $this->gererateCollatedReport($allSurveys, $allPersons, $userInfo, $reportSurvey, $maxQuestionIndex);
        }

        if( isset($_POST['google_export']) ){
            $reportCSV = array();
            foreach ($reportSurvey as $key => $report) {
                $r = array_merge(array($key+1, $report['FirstLast']), $report['userInfo'], $report['answers']);
                $r = array_combine($tableHeader, $r);
                $reportCSV[] = $r;
            }

            $fileName = 'surveyreport_'.date('mdY_His').'.csv';
            
            $gDocs = new GoogleDocs($fileName);
            $message = $gDocs->CSVExport($reportCSV);
            echo $message;
            exit();
        }
        
        $this->render('SurveyAnswersReport', array( 
            'model'=>$reportSurvey,
            'tableHeader'=>$tableHeader, 
            'reportTitle'=>$reportTitle
        ));
    }

    function unionArrays($arrFirst, $arrSecond)
    {
        $tempArray = array();
        foreach($arrFirst as $key=>$value)
        {
            $tempArray[$key] = $value;
        }
        foreach($arrSecond as $key=>$value)
        {
            $tempArray[$key] = $value;
        }
        return $tempArray;
    }

    // Participants data
    private $userInfo = array(
                'BarCodeID' => 'Barcode',
                'Age' => 'Age',
                'EmailAddress' => 'E-mail',
                'Parent' => 'Parent',
                'City'  => 'City',
                'School' => 'School',
                'Phone' => 'Phone',
                'ZIPPostal'  => 'ZIP Code',
    );

    private function generateAnswer(&$answer, &$questions) {
        $question = $questions[$answer['QuestionId'] - 1];
        $resAnswer = '-';

        if( !empty($answer['Text']) ) {

            $resAnswer = $answer['Text'];
        } elseif( !empty($answer['ImageFileId']) ) {

            $file = File::model()->findByPk($answer['ImageFileId']);
            //$resAnswer = '<a href="'.Yii::app()->request->hostInfo.Yii::app()->getBaseUrl().$file['OriginalPath'].'" target="_blank">image</a>';
            $resAnswer = Yii::app()->request->hostInfo.Yii::app()->getBaseUrl().$file['OriginalPath'];
        } elseif( !empty($answer['VideoFileId']) ) {

            $file = File::model()->findByPk($answer['VideoFileId']);
            //$resAnswer = '<a href="'.Yii::app()->request->hostInfo.Yii::app()->getBaseUrl().$file['ConvertedPath'].'" target="_blank">video</a>';
            $resAnswer = Yii::app()->request->hostInfo.Yii::app()->getBaseUrl().$file['ConvertedPath'];
        } elseif( !empty($answer['Variants']) ) {

            $resAnswer = $this->variantToAnswer(explode(',', $answer['Variants']), $question);
        }

        return $resAnswer;
    }

    private function variantToAnswer($variants, $question, $delimiter = '; ') {
        $resultAnswer = '';

        foreach ($variants as $variant) {
            if( ($question->type == QuestionType::SINGLE_CHOICE) || ($question->type == QuestionType::MULTIPLE_CHOICE) 
                    || ($question->type == QuestionType::DROPDOWN) ){

                $resultAnswer .= $question->variants[$variant - 1]->title.$delimiter;
            }  else {
                $resultAnswer .= $variant.$delimiter;
            }
        }

        return $resultAnswer;
    }

    private function gererateReportHeader(&$allSurveys, &$userInfo, &$tableHeader, &$reportTitle) {
        foreach ($userInfo as $info) {
            $tableHeader[] = $this->userInfo[$info];        
        }

        foreach ($allSurveys as $survey) {
            $questions = unserialize($survey['Questions']);
            $reportTitle .= $survey['Title'].'; ';
            $tableHeader[] = $survey['Title'];
            $tableHeader[] = 'Date of survey';

            foreach ($questions as $question) {
                $tableHeader[] = $question->title; 
            }
        }
    }

    private function gererateReport(&$allSurveys, &$allPersons, &$userInfo, &$reportSurvey) {

        foreach ($allPersons as $person) {
            $personId = $person['PersonId'];
            $reportRow['FirstLast'] = $person['FirstName'].' '.$person['LastName'];

            $infoRow = array();
            foreach ($userInfo as $info) {
                $infoRow[] = empty($person[$info])?'-':$person[$info];
            }
            $reportRow['userInfo'] = $infoRow;
            $curAnswers = array();
            
            foreach ($allSurveys as $survey) {
                   $curAnswers[] = '#';
                   $curAnswers[] = empty($survey['LastUpdated'])?'-':$survey['LastUpdated'];
                   
                   $surveyId = $survey['ID'];  
                   $questions = unserialize($survey['Questions']);

                   $answers = QuerySurveyItem::getUserSurveyAnswers($personId, $surveyId);         

                   // answers
                   foreach ($answers as $answer) {
                        $curAnswers[] = $this->generateAnswer($answer, $questions); 
                    }

                    $totalQuestions = count($questions);
                    $totalAnswers = count($answers);
                    // skip empty answers
                    for ($i=0; $i < ($totalQuestions - $totalAnswers); $i++) { 
                            $curAnswers[] = '-';
                    }
            }
            $reportRow['answers'] = $curAnswers;
            $reportSurvey[] = $reportRow;
        }

    }

    private function gererateCollatedReportHeader(&$allSurveys, &$userInfo, &$tableHeader, &$reportTitle, $maxQuestionIndex) {
        foreach ($userInfo as $info) {
            $tableHeader[] = $this->userInfo[$info];        
        }

        $reportTitle = 'Collated Report';

        for ($questionIndex=1; $questionIndex <= $maxQuestionIndex; $questionIndex++) { 
            $tableHeader[] = '#';

            foreach ($allSurveys as $survey) {
                $questions = unserialize($survey['Questions']);
                
                $tableHeader[] = $survey['Title'].' - '.$questions[$questionIndex - 1]->title;
            }
        }
    }

    private function gererateCollatedReport(&$allSurveys, &$allPersons, &$userInfo, &$reportSurvey, $maxQuestionIndex) {

        foreach ($allPersons as $person) {
            $personId = $person['PersonId'];
            $reportRow['FirstLast'] = $person['FirstName'].' '.$person['LastName'];

            $infoRow = array();
            foreach ($userInfo as $info) {
                $infoRow[] = empty($person[$info])?'-':$person[$info];
            }
            $reportRow['userInfo'] = $infoRow;
            $curAnswers = array();

            for ($questionIndex=1; $questionIndex <= $maxQuestionIndex; $questionIndex++) { 
                $curAnswers[] = '#';

                foreach ($allSurveys as $survey) {
                       
                        $surveyId = $survey['ID'];  
                        $questions = unserialize($survey['Questions']);

                        $answer = QuerySurveyItem::getUserSurveyAnswerByQuestion($personId, $surveyId, $questionIndex); 

                        // ---
                        if( !empty($answer) ) {
                            $curAnswers[] = $this->generateAnswer($answer, $questions); 
                        } else {
                            $curAnswers[] = '-';
                        }
                }

            }

            $reportRow['answers'] = $curAnswers;
            $reportSurvey[] = $reportRow;
        }

    }
}
