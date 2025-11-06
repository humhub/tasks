<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\TaskInfoBox;
use humhub\modules\tasks\widgets\TaskUserList;

/* @var $task Task */

$color = $task->getColor('var(--accent)');
?>
<div class="task-infos">
    <?= TaskInfoBox::widget([
        'title' => Yii::t('TasksModule.base', 'ID'),
        'value' => $task->id,
        'icon' => 'info-circle',
        'cssClass' => 'task-info-id',
    ]) ?>

    <?php if ($task->hasTaskResponsible()) : ?>
        <?= TaskInfoBox::widget([
            'title' => Yii::t('TasksModule.base', 'Responsible'),
            'value' => TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'type' => Task::USER_RESPONSIBLE]),
            'icon' => 'user',
            'cssClass' => 'task-info-responsible',
        ]) ?>
    <?php endif ?>

    <?php if ($task->hasTaskAssigned()) : ?>
        <?= TaskInfoBox::widget([
            'title' => Yii::t('TasksModule.base', 'Assignments'),
            'value' => TaskUserList::widget(['users' => $task->taskAssignedUsers]),
            'icon' => 'users',
            'cssClass' => 'task-info-assignments',
        ]) ?>
    <?php endif ?>

    <?= $task->scheduling ? TaskInfoBox::widget([
        'title' => Yii::t('TasksModule.base', 'Scheduling'),
        'value' => ($task->scheduling
            ? Yii::t('TasksModule.base', 'Start') . ' ' . $task->schedule->getFormattedStartDateTime()
            . '<br>' : '')
            . Html::tag('span', $task->schedule->getFormattedDateTime(), [
                'class' => $task->schedule->isOverdue() ? 'colorDanger' : '',
            ]),
        'icon' => 'clock-o',
        'cssClass' => 'task-info-scheduling',
    ]) : '' ?>
</div>
