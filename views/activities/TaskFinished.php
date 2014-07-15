<?php $this->beginContent('application.modules_core.activity.views.activityLayout', array('activity' => $activity)); ?>
<?php echo Yii::t('TasksModule.views_activities_TaskFinished', '{userName} finished task {task}.', array(
    '{userName}' => '<strong>' . $user->displayName . '</strong>',
    '{task}' => '<strong>' . $target->getContentTitle() . '</strong>'
)); ?>
<?php $this->endContent(); ?>


