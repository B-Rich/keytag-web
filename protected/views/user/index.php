<?php

Yii::app()->clientScript->registerScript('SearchUser', "

$(document).ready(function(){
    $('#searchButton').click(function(){
        return Submit();
    });
    
    $('#filter').enterKey(function(){
        return Submit();
    });
});

function Submit(){
    var filter = $('#filter').val();
    
    $.fn.yiiGridView.update('userGrid', {
        data: {filter : filter}
    });
    return false;
}
");
?>

<div style="margin: 10px;">
    <div>
        <span class='long-input left left-5'><?php echo CHtml::textField('filter', ''); ?></span>
        <span class="styled-bttn right right-5 styled-bttn-long"><?php echo CHtml::button('GO', array ('id'=>'searchButton')); ?></span>
        <div class="clear"></div>
    </div>

    <?php $this->widget('application.extensions.KeyTagGridView.KeyTagGridView', array(
        'id'=>'userGrid',
        'dataProvider'=>$model->search(),
        'ajaxVar'=>true,
        'ajaxUpdate'=>true,
        'template'=>'{items}{pager}{summary}',
        'title'=>'Users',
        'addActionUrl'=>$this->createUrl('edit'),
        'editActionUrl'=>$this->createUrl('edit'),
        'deleteActionUrl'=>$this->createUrl('delete'),
        'deleteButtonVisible'=>Yii::app()->user->checkAccess(UserRoles::SuperAdmin),
        'idParameterName'=>'userId',
        'columns'=>array(
            'Login',
            'FirstName',
            'LastName',
            array(
               'name' => 'Role', 
               'header'=>'Role',
               'value'=>'$data->RoleRelation->Name'
             ),
            ),
    )); ?>

</div>
