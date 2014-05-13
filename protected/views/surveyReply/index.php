<?php

    Yii::app()->clientScript->registerScript('SearchParticipant', "

    function Submit() {
        var filter = $('#filter').val();
        
        $.fn.yiiGridView.update('surveyReplyGrid', {
            data: {filter : filter}
        });
        return false;
    }

    $(document).ready(function(){
        sessionList = $('#session');
        
        $('#searchButton').click(function(){ 
            return Submit();
        });

        $('#filter').enterKey(function(){
            return Submit();
        });
        
        $('#session').change(function(){
            var filter = $('#filter').val();
            
            $.fn.yiiGridView.update('surveyReplyGrid', {
                data: {filter : filter}
            });
            return false;
        });

        $('#program').change(function(){
        
            $.ajax({
                    type: 'POST',
                    url: '".CController::createUrl('SessionsByProgram')."',
                    traditional: true,
                    data: {program_id: $('#program').val()},
                    success: function(data) {
                        if (data){
                            sessionList.empty();
                            sessionList.append(data);
                            
                            $.fn.yiiGridView.update('surveyReplyGrid', {
                                 data: {session: $('#session option:selected').val(),
                                        program: $('#program').val()
                                       }
                            });
                        }
                    }
            });
            return false;
        });
    });
");
?>
<div class="horisontalDivider"></div>
<div class="clear"></div>
<div class="surveyReplyProgramSessionDropDown"><!--START [surveyReplyProgramSessionDropDown]-->
    <h2>Select Survey</h2>
    <?php
        /* Programs section */
        echo CHtml::Label('Programs', 'Programs', array('class'=>'label title')); 
        $programList = CHtml::listData(QueryProgram::model()->findAll(array('order' => 'Description')), 'ID', 'Description');
        echo '<span class="short-input-select select-surveyReply">';
        echo CHtml::dropDownList('program', '', $programList, array('empty' => 'Select a program..'));
        echo '</span>';
        echo '<span class="short-input-select select-surveyReply-300 right">';
        echo CHtml::dropDownList('session', 'any', array(), array('empty' => 'any'));
        echo '</span>';
        echo CHtml::Label('Sessions', 'Sessions', array('class'=>'label title right')); 
        echo '<div class="clear"></div>';
    ?>
</div><!--END [surveyReplyProgramSessionDropDown]-->
<div class="clear"></div>

<div style="margin: 10px;">
    <div class="searchSurveyReply"><!--START [searchSurveyReply]-->
        <h2 class="left-5">Search Survey</h2>
        <div class="clear"></div>
        <span class='long-input left left-5'><?php echo CHtml::textField('filter', ''); ?></span>
        <span class="styled-bttn right right-5 styled-bttn-long"><?php echo CHtml::button('GO', array ('id'=>'searchButton')); ?></span>
        <div class="clear"></div>
    </div><!--END [searchSurveyReply]-->

    <?php 

        $this->widget('application.extensions.KeyTagGridView.KeyTagGridView', array(
            'id'=>'surveyReplyGrid',
            'dataProvider'=> $model->search(), 
            'ajaxVar'=>true,
            'ajaxUpdate'=>true,
            'template'=>'{items}{pager}{summary}',
            'title'=>'Answers',
            'editActionUrl'=>$this->createUrl('evaluate'),
            'editButtonText' => 'Evaluate',
            'addButtonVisible' => false,
            'deleteButtonVisible' => false,            
            //'deleteButtonVisible'=>Yii::app()->user->checkAccess(UserRoles::SuperAdmin),
            'idParameterName'=>'surveyReplyId',
            'columns'=>array(
                array(
                    'name' => 'Name', 
                    'header'=>'Participant Name',
                    'value'=>'$data->PersonRelation->FirstName." ".$data->PersonRelation->LastName',
                    'htmlOptions'=>array('width'=>'140'),
                 ),
                array(
                    'name' => 'Household', 
                    'header'=>'Household',
                    'value'=>'$data->HouseholdRelation->Name'
                 ),
                array(
                    'name' => 'Role', 
                    'header'=>'Role',
                    'value'=>'$data->RoleRelation->Name',
                 ),                        
                array(
                    'name' => 'Survey', 
                    'header'=>'Survey Name',
                    'value'=>'$data->SurveyRelation->Title'
                 ),

                array(
                    'name' => 'Score', 
                    'header'=>'Score',
                    'value'=>'$data->score',
                 ),
                array (
                    'name' => 'CreateDate', 
                    'header'=>'Date of creation',
                    'value'=>'$data->CreateDate',
                    'htmlOptions'=>array('width'=>'140'),
                ),
            ),
        )); 
?>

</div>
