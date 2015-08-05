<?php

use yii\helpers\Html;

echo Yii::t('TasksModule.views_notifications_taskAssigned', '{userName} assigned you to the task {task}.', array(
    '{userName}' => '<strong>' . Html::encode($originator->displayName) . '</strong>',
    '{task}' => '"'.Html::encode($source->getContentDescription()).'"'
));
?>





