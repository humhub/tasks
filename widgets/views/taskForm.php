<?php
// load js for datepicker component
Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/js/bootstrap-datepicker.js', CClientScript::POS_BEGIN
);
?>

<?php echo CHtml::textArea("title", "", array('id' => 'contentForm_title', 'class' => 'form-control autosize contentForm', 'rows' => '1', "tabindex" => "1", "placeholder" => Yii::t('TasksModule.base', "What to do?"))); ?>
<div class="contentForm_options">
    <?php echo CHtml::textField('preassignedUsers', '', array('placeholder' =>  Yii::t('TaskModule.base', 'Preassign user(s) for this task.'))); ?>
    <?php
    $this->widget('application.modules_core.user.widgets.UserPickerWidget', array(
        'inputId' => 'preassignedUsers',
        'userSearchUrl' => $this->createUrl('//space/space/searchMemberJson', array('sguid' => $contentContainer->guid, 'keyword' => '-keywordPlaceholder-')),
        'maxUsers' => 10,
    ));
    ?>
    <?php echo HHtml::dateTimeField('deathline', '', array('class' => 'form-control', 'placeholder' => Yii::t('TaskModule.base', 'Deadline for this task?'))); ?>

</div>
