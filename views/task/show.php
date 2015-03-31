<?php $this->widget('application.modules.tasks.widgets.TaskFormWidget', array('contentContainer' => $this->contentContainer)); ?>
<?php
$this->widget('application.modules.tasks.widgets.TasksStreamWidget', array(
    'contentContainer' => $this->contentContainer,
    'streamAction' => '//tasks/task/stream',
    'messageStreamEmpty' => ($this->contentContainer->canWrite()) ?
            Yii::t('TasksModule.widgets_views_stream', '<b>There are no tasks yet!</b><br>Be the first and create one...') :
            Yii::t('TasksModule.widgets_views_stream', '<b>There are no tasks yet!</b>'),
    'messageStreamEmptyCss' => ($this->contentContainer->canWrite()) ?
            'placeholder-empty-stream' :
            '',
));
?>
