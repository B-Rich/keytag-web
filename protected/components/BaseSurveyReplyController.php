<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseTrackingController
 *
 * @author avasyl
 */
class BaseSurveyReplyController extends Controller {
   
    public $layout='//layouts/layout_tracking';
    
    public function firstLevelNavigationType() {
        return NavigationType::SURVEY_REPLY;
    }
}

?>
