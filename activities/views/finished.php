<?php

use humhub\helpers\Html;

echo Yii::t('TasksModule.base', '{userName} finished task {task}.', [
    '{userName}' => '<strong>' . Html::encode($originator->displayName) . '</strong>',
    '{task}' => Html::encode($source->getContentDescription())
]);
?>
