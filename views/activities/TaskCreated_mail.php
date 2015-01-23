<?php $this->beginContent('application.modules_core.activity.views.activityLayoutMail', array('activity' => $activity, 'showSpace' => true)); ?>
<?php

echo Yii::t('TasksModule.views_activities_TaskCreated', '{userName} created task {task}.', array(
    '{userName}' => '<strong>' . CHtml::encode($user->displayName) . '</strong>',
    '{task}' => '<strong>' . ActivityModule::formatOutput($target->getContentTitle()) . '</strong>'
));
?>
<?php $this->endContent(); ?>
