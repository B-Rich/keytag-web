<div style="margin: 10px;">

    <?php $this->widget('application.extensions.KeyTagGridView.KeyTagGridView', array(
        'id'=>'personsGrid',
        'dataProvider'=>$model->search(),
        'ajaxVar'=>true,
        'ajaxUpdate'=>true,
        'template'=>'{items}{pager}{summary}',
        'title'=>'Participants',
        'addButtonVisible'=>false,
        'editButtonVisible'=>false,
        'deleteButtonVisible'=>false,
        'columns'=>array(
            array(
               'name' => 'FirstName', 
               'header'=>'First Name',
               'value'=>'$data->PersonRelation->FirstName'
            ),
            array(
               'name' => 'LastName', 
               'header'=>'Last Name',
               'value'=>'$data->PersonRelation->LastName'
            ),
            array(
               'name' => 'IsSucceed',
               'value'=>'$data->IsSucceed ? "Yes" : "No"',
               'htmlOptions'=>array('style' => 'width: 150px;'),
            ),
            'CompleteDate',
            'Details',
        )
          
    )); ?>
    
</div>
