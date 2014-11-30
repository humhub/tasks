<?php
// load js for datepicker component
Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/js/bootstrap-datepicker.js', CClientScript::POS_BEGIN
);
?>

<?php echo CHtml::textArea("title", "", array('id' => 'contentForm_title', 'class' => 'form-control autosize contentForm', 'rows' => '1', "placeholder" => Yii::t('TasksModule.widgets_views_taskForm', "What to do?"))); ?>
<div class="contentForm_options">
    <?php echo CHtml::textField('preassignedUsers', '', array('placeholder' =>  Yii::t('TasksModule.widgets_views_taskForm', 'Preassign user(s) for this task.'))); ?>
    <?php
    $this->widget('application.modules_core.user.widgets.UserPickerWidget', array(
        'inputId' => 'preassignedUsers',
        'userSearchUrl' => $this->createUrl('//space/space/searchMemberJson', array('sguid' => $contentContainer->guid, 'keyword' => '-keywordPlaceholder-')),
        'maxUsers' => 10,
        'placeholderText' => Yii::t('TasksModule.widgets_views_taskForm', 'Assign users to this task')
    ));
    ?>
    <?php echo HHtml::dateTimeField('deadline', '', array('class' => 'form-control', 'id' => 'deadline', 'placeholder' => Yii::t('TasksModule.widgets_views_taskForm', 'Deadline for this task?'))); ?>

</div>
