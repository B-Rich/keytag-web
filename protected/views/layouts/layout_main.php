<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/attendance.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/keytag.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
    
    
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/jquery-1_7.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/jquery-ui-core.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/top-tabs.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/pop-up-add.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/calendar.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/custom-select.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/autocomplete.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/left-tabs.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/manage-left-tabs.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/reports-page.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/checkbox.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/pop-upOnResize.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/jquery.timeentry.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/js-styling/main.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/common.js"></script>
    
    <script type="text/javascript">
        /* Select appropriate tab */
        $(document).ready(function (){
            <?php if ($this->firstLevelNavigationType() == NavigationType::MANAGE): ?>
                $("#managePage").addClass("top-tab-active");
                $(".content-tabs").parent().addClass("managePage");
            <?php elseif ($this->firstLevelNavigationType() == NavigationType::REPORTING): ?>
                $("#reportsPage").addClass("top-tab-active");
            <?php elseif ($this->firstLevelNavigationType() == NavigationType::SURVEY_REPLY):?>
                $("#surveyPage").addClass("top-tab-active");
                $(".right-part-tabs").removeClass("right-part-tabs").addClass("content-evaluateSurvey");                
            <?php elseif ($this->firstLevelNavigationType() == NavigationType::TRACKING): ?>
                $("#checkinPage").addClass("top-tab-active");
                $(".content-tabs").css({'overflow': 'visible'});
                $(".right-part-tabs").removeClass("right-part-tabs").addClass("content-checkinPage");
            <?php endif ?>
                
            $('#wrapper-container').css({
                'min-height': $(window).height() - 120
            });
        });
        
        function runSynchronization() {
            $.ajax({
                type: 'GET',
                url: '<?php echo $this->createUrl('synchronization/checkSynchronization'); ?>',
                datatype: 'json',
                traditional: true,
                success: function(data) {
                    if (data){
                        if (!data.isRunning) {
                            if (data.estimatedCount <= 10 ||
                            confirm('Are you sure you want to synchronize ' + data.estimatedCount + ' participants? It could take a lot of time.')) {
                                var redirectUrl = data.redirectUrl;
                                $.ajax({
                                    type: 'POST',
                                    url: data.runUrl,
                                    datatype: 'json',
                                    traditional: true,
                                    async: true,
                                    success: function(data) {
                                        window.location.href = redirectUrl;
                                    }
                                });
                            }
                        }
                        else {
                            window.location.href = data.redirectUrl;
                        }
                    }
                }
            });
        }
    </script>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<?php
    $isAdmin = (Yii::app()->user->checkAccess('Super Admin')) || (Yii::app()->user->checkAccess('Local Admin'));
?>
<body>
    <div id="wrapper-container">
        <ul class="top-tab-menu">
            <li class="logo">
                <?php echo CHtml::link(CHtml::image('images/icon.png', 'ION'), Yii::app()->homeUrl); ?>
            </li>
            <li class="logo-text">
               <h1 style="width: 150px;">ION</h1>
            </li>
            <?php  if (!Yii::app()->user->isGuest) { ?>
                <!--<li id="checkinPage" class="tab top-tab">
                    <a href="<?php echo $this->createUrl('attendance/index') ?>">
                        <img src="images/check-in-icon.png" height="" width="" alt="" id="checkinPageImg" />
                        <span id="checkinPageSpan">Check In</span>
                    </a>
                </li>-->
                <?php if (UserIdentity::IsAdmin()) { ?>
                <li id="synchronizationPage" class="tab top-tab">
                    <a href="javascript:runSynchronization();">
                        <img src="images/synchronize-icon.png" height="" width="" alt="" id="checkinPageImg" />
                        <span id="surveyPageSpan">Synchronize</span>
                    </a>
                </li>
                <?php } ?>
                
                <li id="surveyPage" class="tab top-tab">
                    <a href="<?php echo $this->createUrl('surveyReply/index') ?>">
                        <img src="images/surveys-icon.png" height="" width="" alt="" id="surveyPageImg" />
                        <span id="surveyPageSpan">Evaluation</span>
                    </a>
                </li>
                
                <li id="reportsPage" class="tab top-tab">
                    <a href="<?php echo $this->createUrl('reports/index') ?>">
                        <img src="images/reports-icon.png" height="" width="" alt="" s="reportsPageImg" />
                        <span id="reportsPageSpan">Reports</span>
                    </a>
                </li>
                <li id="managePage" class="tab top-tab">
                    <a href="<?php echo $this->createUrl($isAdmin ? 'user/index': 'person/index') ?>">
                        <img src="images/manage-icon.png" height="" width="" alt="" id="managePageImg" />
                        <span id="managePageSpan">Manage</span>
                    </a>
                </li>
                <li class="log-out">
                    <a href="<?php echo $this->createUrl('home/logout') ?>">
                        <img src="images/log-out-icon.png" height="" width="" alt="" />
                    </a>
                </li>
            <?php } else {?>
                <li class="log-out log-in">
                    <a href="<?php echo $this->createUrl('home/login') ?>">
                        <img src="images/log-in-icon.png" height="" width="" alt="" />
                    </a>
                </li>
            <?php } ?>
        </ul>
        <span class="clear"></span>

        <div class="content">
            <div class="content-tabs">
                <?php if (!Yii::app()->user->isGuest && count($this->navigationMenu) > 0): ?>
                    <?php $this->widget('zii.widgets.CMenu',array('htmlOptions' => array('class' => 'left-tabs'),'items'=> $this->navigationMenu )); ?>
                <?php endif ?>
                <div class="right-part-tabs">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>  
        
        <div id="keytag-footer">
            <p>Copyright 2012 <b>Iridescent</b><br />
                Sponsored by Office of Naval Research.<br />
                All rights reserved.<br />
                Copyrigths &copy; 2012 <b>Iridescent</b></p>
            <span class="icon-footer"><?php echo CHtml::link(CHtml::image('images/icon.png', 'ION'), Yii::app()->homeUrl); ?></span>
            <div class="clear"></div>
        </div>
        
    </div>
    
    <!--Animation loader-->
    <div class="gif-animation-wrapper"><!--START [gif-animation-wrapper]-->
        <div class="gif-animation"></div>
    </div><!--END [gif-animation-wrapper]-->
    
    <script type="text/javascript">
        $(document).ready(function(){
           $('.gif-animation-wrapper').css({
               'width' : $(document).width(),
               'height' : $(document).height()
           }); 
           $('.gif-animation-wrapper .gif-animation').css({
               'top' : ($(window).height() - 128)/2 + 'px',
               'left' : ($(window).width() - 128)/2 + 'px'
           });
        });
    </script>
    
</body>
</html>
