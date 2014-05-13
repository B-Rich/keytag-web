<?php
    $div_class = 'reports-programs-row';
    if ($flag) { 
        $div_class .= ' under-toggle';
    }
    
    echo '<div class="'. $div_class . '">';
    if ($flag) { 
        echo '<span class="space-for-checkbox"></span>';
    }

    echo CHtml::Label('Survey', 'Surveys', array('class'=>'label')); 
    echo '<span class="select-reports multi-select">';
    $this->widget('ext.multiselect.JMultiSelect',array(
        'name' => 'surveys[]',  
        'data'=> $data,
        'options'=>array(
            'header'=>true,
            'height'=>245,
            'minWidth'=>225,
            'checkAllText'=>Yii::t('application','Check all'),
            'uncheckAllText'=>Yii::t('application','Uncheck all'),
            'noneSelectedText'=>Yii::t('application','any'),
            'selectedText'=>Yii::t('application','# selected'),
            'selectedList'=>true,
            'show'=>'',
            'hide'=>'',
            'autoOpen'=>false,
            'multiple'=>true,
            'classes'=>'',
            'position'=>array(),
            'filter'=>true,
        )
    ));  
    echo '</span>';
    echo '</div>'; 
?>