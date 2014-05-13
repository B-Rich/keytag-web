<?php
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/survey.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/js-styling/jquery.slider.js');    
?>
<div class="surveyReplyInside"><!--START [surveyReplyInside]-->
<!--START HEADER -->
<div class="surveyReplyHeader"><!--START [surveyReplyHeader]-->
    
    <?php echo CHtml::hiddenField('questions_number', $questions_number); ?>
    <?php echo CHtml::hiddenField('surveyreply_id', $model->ID); ?>  
    
    <div class="horisontalDivider"></div>
    <div class="clear"></div>

    <div class="programName surveyHeaderLabel"><!--START [programName]-->
        <?php echo CHtml::activeLabelEx($model, DomainProgram::model()->findByPk($model->SurveyRelation->SessionRelation->Program)->Description, array('class'=>'label')); ?>
    </div><!--END [programName]-->
    <div class="sessionName surveyHeaderLabel"><!--START [sessionName]-->
        <?php echo CHtml::activeLabelEx($model, $model->SurveyRelation->SessionRelation->Description, array('class'=>'label')); ?>
    </div><!--END [sessionName]-->
    <div class="programDate surveyHeaderLabel"><!--START [programDate]-->
        <?php echo CHtml::activeLabelEx($model, Localization::ToClientDate($model->CreateDate) , array('class'=>'label')); ?>
    </div><!--END [programDate]-->
    <div class="clear"></div>
    <div class="firstLastName surveyHeaderLabel"><!--START [firstLastName]-->
        <?php echo CHtml::activeLabelEx($model, $model->PersonRelation->FirstName.' '.$model->PersonRelation->LastName, array('class'=>'label')); ?>
    </div><!--END [firstLastName]-->           
    <div class="surveyReplyName surveyHeaderLabel"><!--START [surveyReplyName]-->
        <?php echo CHtml::activeLabelEx($model, $model->SurveyRelation->Title, array('class'=>'label')); ?>
    </div><!--END [surveyReplyName]-->
    <div class="clear"></div>
    <div class="horisontalDivider"></div>
    <div class="clear"></div>

</div><!--END [surveyReplyHeader]-->
<div class="clear"></div>
<!--END HEADER-->


<div class="surveysWithVideo">
<?php if ($questions_number <= 0):?>
        <h1> No answers to evaluate. </h1>
<?php else: ?>
    
    <?php foreach ($items as $key => $item) {
            	  echo '<div class="showcase-slide"><div class="showcase-content"><div class="showcase-content-wrapper">';
                  echo $this->renderPartial('_item', array('item'=>$item, 'questions_number' => $questions_number, 'item_id' => $key)); 
                  echo '</div></div></div>';
              }  
        ?>
    
<?php endif; ?>
</div>


<script type="text/javascript">
        function saveEvaluation() {     
            $('.showcase-arrow-next, #finishQuestionLink').click(function() {
                var regEx=/[0-9]|\./;
                if ($(this).is('#finishQuestionLink')) {
                     is_finish = 1;
                } else {
                    is_finish = 0;
                }                 
                elementValue = $('input[name^=points]').val().toString(); 
                        elementId = $(this).attr('id').split('_')[1]-1;
                        
                        
                        if($('#myArrayDisplay').children().length > 0){
                            var attrID = $('.scoreQuestionsCount .short-input input#points_' + (elementId + 1));
                            var attrIDCheck = $(attrID).attr('id');
                            var attrClass = $('#myArrayDisplay .points_' + (elementId + 1));
                            var attrClassCheck = $(attrClass).attr('class');
                            var classData = $(attrClass).text();
                            if(attrIDCheck == attrClassCheck && $(attrClass).text().length > 0){
                                $('.scoreQuestionsCount .short-input input#points_' + (elementId + 1)).val(classData);
                            }
                        }
                        
                        
                        if (!isNaN(elementValue)) {
                            $.ajax({
                                    type: 'POST',
                                    url: '<?php print CController::createUrl('SaveEvaluation') ?>',
                                    datatype: 'json',
                                    data: {question_id: $('#question_id').val(),
                                           surveyreply_id: $('#surveyreply_id').val(),
                                           points: $('input[name^=points]').val(),
                                           is_finish: is_finish},

                                    success: function(data) {
                                        if (data.finish == true){
                                            window.location = '<?php print CController::createUrl('index') ?>';
                                          //  $('input[name=points_'+id+']').val(data.points);
                                          //   console.log($('#points_'+id));
                                        } 

                                    },
                                    complete: function(jsonData){
                                        $("#myArrayDisplay .points_"+ elementId).html(elementValue);
                                    }

                            });
                        } 
                        
                        
         });
         
         
         
    }
    
    $(document).ready(function(){
        $('.contentQuestionWithVideoImage').has('object').addClass('videoCentered');
        var data = $('.surveysWithVideo').children();
        $('.surveysWithVideo').remove();
        $('#showcase').append(data);
        
        $('.surveyReplyInside').show();
        
        jQuery("#showcase").awShowcase({
            fit_to_parent:		false,
            content_height:             400,
            auto:			false,
            interval:			0,
            continuous:			false,
            loading:			true,
            tooltip_width:              200,
            tooltip_icon_width:		32,
            tooltip_icon_height:        32,
            tooltip_offsetx:		18,
            tooltip_offsety:		0,
            arrows:			true,
            buttons:			true,
            btn_numbers:		true,
            keybord_keys:		false,
            mousetrace:			false, /* Trace x and y coordinates for the mouse */
            pauseonover:		false,
            stoponclick:		false,
            transition:			'vslide', /* hslide / vslide / fade */
            transition_delay:		0,
            transition_speed:		0,
            show_caption:		'onload', /* onload / onhover / show */
            thumbnails:			false,
            thumbnails_position:        'outside-last', /* outside-last / outside-first / inside-last / inside-first */
            thumbnails_direction:       'vertical', /* vertical / horizontal */
            thumbnails_slidex:		0, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
            dynamic_height:		true, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
            speed_change:		false, /* This prevents user from swithing more then one slide at once */
            viewline:			false, /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
            fullscreen_width_x:		15,
            custom_function:		null
        });
        
        $('.showcase-arrow-next').attr('id','questionCssId_'+1);
        hidePrevLink();
        hideNextLink();
        
        var lengthItems = $('.showcase-button-wrapper').children('span').length;
        var elems = '';
        for (var i=1; i<lengthItems+1; i++){
            elems += '<div class="points_' + i + '"></div>';
        }
        $("#myArrayDisplay").append(elems);
        
        if (lengthItems == 0) {
            addFinishButton();
        }
        
    });
</script>


<div id="myArrayDisplay" style="display: none;">
</div>
<div class="surveyReplyContent">
    
    <div id="showcase" class="showcase" >
    </div>
</div><!--END [surveyReplyContent]-->

</div><!--END [surveyReplyInside]-->