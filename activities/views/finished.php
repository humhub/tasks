<?php

use yii\helpers\Html;

echo Yii::t('TasksModule.base', '{userName} finished task {task}.', array(
    '{userName}' => '<strong>' . Html::encode($originator->displayName) . '</strong>',
    '{task}' => Html::encode($source->getContentDescription())
));
?>
