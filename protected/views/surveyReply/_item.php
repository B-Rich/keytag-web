<?php echo CHtml::hiddenField('question_id', $item->QuestionId); ?>
<?php echo CHtml::hiddenField('item_id', $item_id); ?>

<?php 
    echo '<div class="questionCount">';
    echo CHtml::label(Yii::t('application', 'Question '), '', array('class'=>'label'));
    echo '<span class="itemNumber">';
    echo CHtml::label((string)$item_id+1, '', array('class'=>'label'));
    echo '</span>';
    echo CHtml::label(Yii::t('application', ' of '), '', array('class'=>'label'));
    echo '<span class="countOfQuestion">';
    echo CHtml::label((string)$questions_number, '', array('class'=>'label'));
    echo '</span>';
    echo '</div>';
    
    $labelOf = '';

    echo '<div class="contentQuestionWithVideoImage"><!--START [contentQuestionWithVideoImage]-->';
    if (((int)$item->QuestionType === QuestionType::TEXT_AREA || (int)$item->QuestionType === QuestionType::TEXT_INPUT) && trim($item->Text) != '') {
        $labelOf = ' of 20';
        echo '<p>'.$item->Text.'</p>';

    }    
    
    if ((int)$item->QuestionType === QuestionType::IMAGE && $item->ImageFileId != NULL) {
        $labelOf = ' of 30';
        
        echo CHtml::image(Yii::app()->getBaseUrl().File::model()->getFileInfo($item->ImageFileId)->OriginalPath, '');
    }
    
    if ((int)$item->QuestionType === QuestionType::VIDEO && $item->VideoFileId != NULL) {
        $labelOf = ' of 50';
        if (File::model()->getFileInfo($item->VideoFileId)->AmazonPath) {
            $filePath = File::model()->getFileInfo($item->VideoFileId)->AmazonPath;
        } else {
            $filePath = Yii::app()->getBaseUrl().File::model()->getFileInfo($item->VideoFileId)->ConvertedPath;
        } 
        $this->widget('application.extensions.jwplayer.JwPlayer',
                    array(
                        'flvName'=>$filePath,
                        'autoStart'=>false,
                        'id' => 'media_'.$item->ID
                
                ));
    }
    
    echo '</div><!--END [contentQuestionWithVideoImage]-->';
?>
        <div class="clear"></div>  
<!-- Footer -->

        <div class="left scoreQuestionsCount"><!--START [scoreQuestionsCount]-->
            <span class="left right-5"><?php echo CHtml::label(Yii::t('application', 'Score: '), 'Points', array('class'=>'label')); ?></span>
            <span class="short-input right-5"><?php echo CHtml::activeTextField($item, 'Points', array('name'=> 'points_'.$item->QuestionId, 'id' => 'points_'.$item->QuestionId)); ?></span>
            <span class="left"><?php //echo CHtml::label(Yii::t('application', $labelOf), 'Points', array('class'=>'label')); ?></span>
        </div><!--END [scoreQuestionsCount]-->
        <div class="clear"></div>
        
