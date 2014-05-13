<?php $this->beginContent('//layouts/layout_reporting'); ?>

<div id="programs" class="single-left-tab" style="display: block;">
    <div class="reports-programs-page" >
        <div class="data-inside-reports">
            <?php echo CHtml::beginForm($this->createUrl('surveyReport'), 'post', array('target' => '_blank')) ?>
                                <span class="bttn-get-repord">
                                            <?php  
                                            echo CHtml::openTag('button', array('id'=>'bttn-get-report', 'onclick'=>'js: if (!$("#surveys").val()) {alert ("You should select a Survey"); return false;};' ));
                                            echo Yii::t('Yii', 'GET<br>REPORT');
                                            echo CHtml::closeTag('button');
                                            ?>                                    
                                    </span>

                                    <?php echo $content; ?>

            <?php echo CHtml::endForm() ?>
        </div> <!-- data-inside-reports -->                
    </div><!-- reports-program-page -->
</div>

<?php $this->endContent(); ?>