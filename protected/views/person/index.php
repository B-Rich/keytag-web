<?php

Yii::app()->clientScript->registerScript('SearchPerson', "

$(document).ready(function(){
    $('#searchButton').click(function(){
        return Submit();
    });
    
    $('#filter').enterKey(function(){
        return Submit();
    });
});

function Submit() {
    var filter = $('#filter').val();
    
    $.fn.yiiGridView.update('personsGrid', {
        data: {filter : filter}
    });
    return false;
}
");
?>

<div style="margin: 10px 10px 10px 10px;">
    <div>
        <span class='long-input left left-5'><?php echo CHtml::textField('filter', ''); ?></span>
        <span class="styled-bttn right right-5 styled-bttn-long"><?php echo CHtml::button('GO', array ('id'=>'searchButton')); ?></span>
        <div class="clear"></div>
    </div>
    
    
    <?php $this->widget('application.extensions.KeyTagGridView.KeyTagGridView', array(
        'id'=>'personsGrid',
        'dataProvider'=>$model->search(),
        'ajaxVar'=>true,
        'ajaxUpdate'=>true,
        'afterAjaxUpdate'=>'getPaddingToImg',
        'template'=>'{items}{pager}{summary}',
        'title'=>'Participants',
        'addActionUrl'=>$this->createUrl('edit'),
        'editActionUrl'=>$this->createUrl('edit'),
        'deleteActionUrl'=>$this->createUrl('delete'),
        'deleteButtonVisible'=>Yii::app()->user->checkAccess(UserRoles::SuperAdmin),
        'exportButtonVisisble'=>true,
        'exportActionUrl'=>$this->createUrl('export'),
        'idParameterName'=>'personId',
        'columns'=>array(
            'BarcodeID',
            'FirstName',
            'LastName',
            array(
               'name' => 'Household', 
               'header'=>'Household',
               'value'=>'$data->HouseholdRelation->Name'
             ),
            array(
               'name' => 'HomePhone',
               'value' => 'Localization::FormatPhone($data->HomePhone)'
            ),
            'EmailAddress',
            array(
               'name' => 'Type', 
               'header'=>'Role',
               'value'=>'$data->Role->Name'
             ),
            array(
                'name'=>'PicasaLink',
                'type'=>'raw',
                'value'=>'!empty($data->PicasaLink)? CHtml::link(CHtml::image("images/logo_picasa.png", "Picasa", array("class"=>"header-image")), $data->PicasaLink, array(\'target\'=>"_blank")):""',
            ),
            array(
                'name'=>'GDocSurvey',
                'type'=>'raw',
                'value'=>'!empty($data->GDocSurvey)? CHtml::link(CHtml::image("images/logo_gdoc.png", "GoogleDoc Link", array("class"=>"header-image")), $data->GDocSurvey, array(\'target\'=>"_blank")):""',
            ),
            array(
                'name'=>'GDocApplication',
                'type'=>'raw',
                'value'=>'!empty($data->GDocApplication)? CHtml::link(CHtml::image("images/logo_gdoc.png", "GoogleDoc Link", array("class"=>"header-image")), $data->GDocApplication, array(\'target\'=>"_blank")):""',
            ),
            )
          
    )); ?>
    
</div>
