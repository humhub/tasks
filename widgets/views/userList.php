<?php

use humhub\modules\tasks\models\Task;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image;
use yii\helpers\Html;

/* @var $users User[] */
/* @var $style string */
/* @var $type int */
?>
<?php foreach ($users as $user): ?>
    <?= Image::widget([
        'user' => $user,
        'width' => '24',
        'showTooltip' => true,
        'imageOptions' => ['style' => $style],
        'tooltipText' => match ($type) {
            Task::USER_RESPONSIBLE => Yii::t('TasksModule.base', '{displayName} is responsible for this task', ['displayName' => Html::encode($user->displayName)]),
            Task::USER_AUTHOR => Yii::t('TasksModule.base', '{displayName} is task creator', ['displayName' => Html::encode($user->displayName)]),
            default => Yii::t('TasksModule.base', '{displayName} is assigned to this task', ['displayName' => Html::encode($user->displayName)]),
        },
    ]) ?>
<?php endforeach; ?>
