<?php
    $cs=Yii::app()->clientScript;
    $cs->registerCoreScript('jquery');
?>

<input id="id1" type="hidden" value="<?php echo Yii::app()->createUrl("reports/ProgramSessionsPersons");?>">

    <?php

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

        /* Programs section */
        echo '<div class="reports-programs-row under-toggle"><span class="space-for-checkbox"></span>';

        echo CHtml::Label('Programs', 'Programs', array('class'=>'label')); 

        $programList = CHtml::listData(QueryProgram::model()->findAll(array('order' => 'Description')), 'ID', 'Description');
        $programList = unionArrays(array(""=>"any") , $programList);

        echo '<span class="short-input-select select-reports multi-select left-0">';
        echo CHtml::dropDownList('program', 'any', $programList,
            array(
            'ajax' => array(
                'type'=>'POST',
                'url'=>CController::createUrl('misc/SessionMultiSelect'),
                'update'=>'#ss',
                'data'=>array('program_id'=> 'js:$(\'#program\').val()', 'flag' => TRUE), 
                'beforeSend' => 'function(){ $("#ss").addClass("loading"); }',
                'complete' => 'function (){
                    $("#ss").removeClass("loading");
                    if ($("#surveymulti")){
                        $("#surveys").remove();
                        $("#surveymulti .select-reports").hide();
                        $(".reports-surveys-session-row .ui-multiselect-none").click();
                        $("#surveymulti .select-reports").show();
                    };
                }',
            ))
         );

        echo'</span>';
        echo '</div>';

    ?>
       
    <div id="ss" class="reports-surveys-session-row">
        <?php /* Sessions section - dependant */
            $this->renderPartial('/misc/sessionmultiselect', array('data'=>array(), 'flag'=>TRUE));
        ?>
    </div>
    <div class="clear"></div>

    <div id="surveymulti">
        <?php
            /* Surveys multiselect */
            $surveyList = CHtml::listData(QuerySurvey::model()->findAll(array('order' => 'Title')), 'ID', 'Title');
            $this->renderPartial('/misc/surveymultiselect', array('data'=>$surveyList, 'flag'=>TRUE));
            echo '<span class="short-input-select select-reports multi-select left-0" style="display:none;">';
            echo CHtml::dropDownList('sessions', 'any', array(),
                    array(
                    'ajax' => array(
                        'type'=>'POST',
                        'url'=>CController::createUrl('/misc/surveyMultiSelect'),
                        'update'=>'#surveymulti',
                        'data'=>array('sessions'=> 'js:$(\'#sessions\').val()'),
                        'beforeSend' => 'function(){ $("#surveymulti").addClass("loading");}',
                        'complete' => 'function(){ $("#surveymulti").removeClass("loading");}',
                        'onchange' => 'function(){ $("#surveymulti").addClass("loading");}',
                    ))                                 
                 );
            echo'</span>';
        ?>

    </div>

    <div class="report-survey-collated"><!--START [report-survey-collated]-->
        <div class="space-for-checkbox">
            <span class="niceCheck">
                <?= CHtml::checkBox('collated', false, array('value' => true)) ?>
            </span>
            <span class="label">
                <?= CHtml::Label('Collated', 'collated', array('class'=>'label')) ?> 
            </span>
        </div>
    </div><!--END [report-survey-collated]-->
    <div class="clear"></div>

    <div class="toggle-reports-advanced">       

        <div class="open-close-block">
            <h4>Participants data</h4>
            <div class="clear"></div>
        </div> 

        <div class="three-blocks"><!--START [three-blocks]-->
        <?php foreach($userInfo as $key => $info):?>
            <div class="space-for-checkbox">
                <span class="niceCheck">
                    <?= CHtml::checkBox('user_info[]', false, array('value' => $key)) ?>
                </span>
                <span class="label">
                    <?= CHtml::Label($info, 'user_info', array('class'=>'label')) ?> 
                </span>
            </div>
        <?php endforeach; ?>
        </div><!--END [three-blocks]-->

    </div>   
    
<div class="toggle-reports-advanced-content" style="display: block; ">    
 
<script type="text/javascript">

    $(document).ready(function(){
    	
        //wrap every 3 divs 
        var contInTogglePanel = $(".toggle-reports-advanced .three-blocks .space-for-checkbox");
        for(var i = 0; i < contInTogglePanel.length; i+=3) {
          contInTogglePanel.slice(i, i+3).wrapAll('<div class="one-block"></div>');
        }
    	
        if ($('.select-reports #program option:selected').text() == 'any'){
            $('.space-for-perfectAttendance').css('display','none');
            $('.perfectAttendance-label').css('display','none');
        }
        $('.select-reports #program').change(function(){
            if ($('.select-reports #program option:selected').text() == 'any'){
                $('.space-for-perfectAttendance').css('display','none');
                $('.perfectAttendance-label').css('display','none');
            }else{
                $('.space-for-perfectAttendance').css('display','block');
                $('.perfectAttendance-label').css('display','block');
            }
        });
    });

    $("#showAdvanced").toggle(function(){$(this).attr("src", $(this).attr("tag")+"/images/downarrow.png");}, function(){$(this).attr("src", $(this).attr("tag")+"/images/uparrow.png");});
    function changeUrl()
    {
        var url =$("#id1").val();
        url=url+"&programId="+$("#programs").val();
        url=url+"&sessionId="+$("#sessions").val();
        window.open(url, 'Programs Report');
    }
    $(document).ready(function(){
        $(".advanced").hide();
    });
</script>
