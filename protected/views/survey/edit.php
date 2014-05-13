<?php
    $programSessionStyle = $model->IsAnonymous ? 'style="display: none;"' : '';

    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.Sort.js');

    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/BaseQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/BaseVariantQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/DropdownQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/GridQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/MultipleChoiceQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/ScaleQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/SingleChoiceQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/TextAreaQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/TextInputQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/ImageQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/VideoQuestion.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey/SmileQuestion.js');
    
    
    $json_questions = $model->JsonQuestions;
    
    Yii::app()->clientScript->registerScript('EditSurvey', "
        var json_questions = '" . $json_questions . "';
            
        $(document).ready(function() {
            survey.init(json_questions);
            survey.draw();   
            
            $('#saveButton').click(function() {
                if (survey.validate()) {
                    $('#JsonQuestions').val(survey.getJSON());
                    $('#surveyForm').submit();
                }
            });
            
            $('#bottom_add_button').click(function() {
                survey.addQuestion();
            });
            
            $(function(){
                $('.surveyBodyQuestions').sortable({
                        placeholder: 'surveyQuestion',
                        zIndex : 9999,
                        handle: '.sortSurveyQuestion',
                        stop: function(){
                            survey.reorderQuestions();
                            /*var k=0;
                            $('.surveyBodyQuestions .surveyQuestion').each(function(){
                                k++;
                                $(this).attr('id', 'question_' + k);
                            });*/
                        }
                });
            });
            
            $('.niceCheck').click(function () {
                input = $(this).find('input[type=\'checkbox\']').eq(0);   
                if(!input.attr('checked')) {
                    $('#programControl').show();
                    $('#sessionControl').show();
                } else {
                    $('#programControl').hide();
                    $('#sessionControl').hide();
                }
            });
        });
    ");
?>

<div id="syrveyCustomForm"><!--START [syrveyCustomForm]-->

    <?php $form=$this->beginWidget('CActiveForm', array( 'id'=>'surveyForm', 'enableAjaxValidation'=>false,)); ?>
    <?php echo $form->hiddenField($model, 'ID', array('name'=>'surveyId')); ?>
    <?php echo $form->hiddenField($model, 'JsonQuestions', array ('name'=>'JsonQuestions')); ?>
    
    <?php echo $form->errorSummary($model); ?>
    <div class="surveyHeader"><!--START [surveyHeader]-->
        <h2><?php echo $this->getAddEditText($model->ID) ?> Survey</h2>
        <div class="row surveyHeaderRow">
            <?php echo $form->labelEx($model, 'Title', array('class'=>'label')); ?>
            <span class="short-input input-width-350"><?php echo $form->textField($model, 'Title', array('size'=>25, 'maxlength'=>50)); ?></span>
            <?php echo $form->error($model, 'Title'); ?>
        </div><!--END [surveyHeaderRow]-->
        <div class="clear"></div>
        <div class="row surveyHeaderRow">
            <?php echo $form->labelEx($model, 'Description', array('class'=>'label')); ?>
            <span class="short-textarea">
                <?php echo $form->textArea($model, 'Description', array('maxlength'=>255)); ?>
            </span>
            <?php echo $form->error($model, 'Description'); ?>
        </div><!--END [surveyHeaderRow]-->
        <div class="clear"></div>
        <div class="row surveyHeaderRow">
            <?php echo $form->labelEx($model, 'IsAnonymous', array('class'=>'label')); ?>
            <span class="niceCheck" style="margin-left: 5px;">
                <?php echo $form->checkBox($model, 'IsAnonymous', array('uncheckValue' => 0)) ?>
            </span>
            <?php echo $form->error($model, 'IsAnonymous'); ?>
        </div>
        <div class="clear"></div>
        <div id="programControl" class="row surveyHeaderRow" <?php echo $programSessionStyle; ?>>
            <?php $programList = CHtml::listData(QueryProgram::model()->findAll(array('order' => 'Description')), 'ID', 'Description'); ?>
            <label class="label">Program</label>
            <span class="surveyHeaderSelect">                
                <?php echo CHtml::dropDownList('ProgramId', $model->ProgramId, $programList,
                    array(
                        'empty' => 'Select a Program:', 
                        'ajax' => array(
                            'type' => 'POST',
                            'url' => CController::createUrl('misc/SessionSelect'),
                            'update'=>'#session_select',
                            'data'=>array('program_id'=> 'js:$(\'#ProgramId\').val()', 'input_name'=>'Survey[SessionId]'), 
                        )
                    )
                 ); ?>
            </span>
            <?php echo $form->error($model, 'ProgramId'); ?>
        </div><!--END [surveyHeaderRow]-->
        <div class="clear"></div>
        <div id="sessionControl" class="row surveyHeaderRow" <?php echo $programSessionStyle; ?>>
            <?php $sessionList = array();
                  if ($model->ProgramId) {
                      $sessionList = CHtml::listData(QuerySession::model()->SelectListByProgramId($model->ProgramId), 'ID', 'Description');
                  }
            ?>
            <label class="label">Session</label>
            <span id="session_select" class="surveyHeaderSelect">
                <?php echo $form->dropDownList($model, 'SessionId', $sessionList, array('empty' => 'Select a Session:')); ?>
            </span>
            <?php echo $form->error($model, 'SessionId'); ?>
        </div><!--END [surveyHeaderRow]-->
    </div><!--END [surveyHeader]-->

    <?php $this->endWidget(); ?>

    <h2 class="surveyBodyQuestionsH2">List of Questions</h2>
    <div class="surveyBodyQuestions"><!--START [surveyBodyQuestions]-->
    </div><!--END [surveyBodyQuestions]-->
    <div class="add-new-question"><!--START [add-new-question]-->
        <input id="bottom_add_button" type="button" value="" />
        <span class="label">Add new question</span>
    </div><!--END [add-new-question]-->
    
    
    <div class="wrapper-styled-buttons">
        <span class="styled-bttn">
            <?php echo CHtml::button('Cancel', array ('id'=>'cancelButton', 'onclick'=> "js: history.back();")); ?>
        </span>
        <span class="styled-bttn">
            <?php echo CHtml::button('OK', array ('id'=>'saveButton')); ?>
        </span>
    </div>

</div><!--END [syrveyCustomForm]-->
