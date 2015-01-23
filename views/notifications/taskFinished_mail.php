<?php $this->beginContent('application.modules_core.notification.views.notificationLayoutMail', array('notification' => $notification, 'showSpace' => true)); ?>
<?php

echo Yii::t('TasksModule.views_notifications_taskFinished', '{userName} finished task {task}.', array(
    '{userName}' => '<strong>' . CHtml::encode($creator->displayName) . '</strong>',
    '{task}' => '<strong>' . NotificationModule::formatOutput($targetObject->getContentTitle()) . '</strong>'
));
?>
<?php $this->endContent(); ?>