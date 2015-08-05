<?php

use yii\helpers\Html;

echo Yii::t('TasksModule.views_activities_TaskFinished', '{userName} finished task {task}.', array(
    '{userName}' => '<strong>' . Html::encode($originator->displayName) . '</strong>',
    '{task}' => Html::encode($source->getContentDescription())
));
?>
