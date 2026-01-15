<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\comment\services\CommentListService;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\lists\TaskListDetails;
use humhub\modules\tasks\widgets\TaskBadge;
use humhub\modules\tasks\widgets\TaskContextMenu;
use humhub\modules\tasks\widgets\TaskUserList;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $this View */
/* @var $task Task */
/* @var $options array */
/* @var $details boolean */
/* @var $canManage boolean */

$checkUrl = $task->state->getCheckUrl();

?>

<?= Html::beginTag('div', $options) ?>

    <div class="task-list-task-title-bar d-flex justify-content-between">
        <div class="task-list-item-title flex-grow-1">

             <?php if ($canManage && !$task->isCompleted()) : ?>
                <?= Icon::get('arrows')->class('task-moving-handler') ?>
             <?php endif; ?>

            <?php // We use an extra label in order to prevent click events on the actual label otherwise tasks could be accidentally finished ?>
            <?= Html::checkBox('item[' . $task->id . ']', $task->isCompleted(), [
                'label' => '&nbsp;',
                'data-action-change' => 'changeState',
                'data-action-url' => $checkUrl,
                'disabled' => empty($checkUrl),
            ]); ?>

            <span class="toggleTaskDetails"><?= Html::encode($task->title) ?></span>
            <span style="margin-left:10px"><strong>ID:</strong> <?= $task->id ?></span>

            <?= TaskBadge::widget(['task' => $task, 'includePending' => false, 'includeCompleted' => false]) ?>
        </div>

        <?php if ($task->hasTaskAssigned() || $task->hasTaskResponsible()) : ?>
            <div class="task-controls assigned-users d-none d-sm-block">
                <?= TaskUserList::widget(['users' => [$task->createdBy], 'style' => 'border:2px solid var(--primary)', 'type' => Task::USER_AUTHOR]) ?>
                <?= TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'style' => 'border:2px solid var(--accent)', 'type' => Task::USER_RESPONSIBLE]) ?>
                <?= TaskUserList::widget(['users' => $task->taskAssignedUsers]) ?>
            </div>
        <?php endif; ?>

        <?php if ($task->review) : ?>
            <div class="task-controls toggleTaskDetails">
                <?= Icon::get('eye')
                    ->class('d-none d-sm-inline')
                    ->tooltip(Yii::t('TasksModule.base', 'This task requires to be reviewed by a responsible')) ?>
            </div>
        <?php endif; ?>

        <?php if ($task->scheduling) : ?>
            <?php
            if ($task->isCompleted()) {
                $schedulingTitle = Yii::t('TasksModule.base', 'Completed');
                $schedulingColor = 'colorSuccess';
            } elseif ($task->schedule->isOverdue()) {
                $schedulingTitle = Yii::t('TasksModule.base', 'Overdue');
                $schedulingColor = 'colorDanger';
            } elseif ($task->schedule->isDeadlineToday()) {
                $schedulingTitle = Yii::t('TasksModule.base', 'Today');
                $schedulingColor = 'colorWarning';
            } else {
                $daysRemaining = $task->schedule->getDaysRemaining();
                $schedulingTitle = Yii::t('TasksModule.base', '{count,plural,=1{# day} other{# days}} remaining', ['count' => $daysRemaining]);
                $schedulingColor = $daysRemaining > 1 ? '' : 'colorWarning';
            }
            ?>
            <div class="task-controls toggleTaskDetails d-none d-sm-block">
                <?= Icon::get('clock-o')->tooltip($schedulingTitle)->class($schedulingColor) ?>
            </div>
        <?php endif; ?>

        <div class="task-controls toggleTaskDetails d-none d-sm-block"
             style="<?= (!$task->content->canEdit()) ? 'border-right:0;margin-right:0' : '' ?>">
            <?= Icon::get('comment-o') ?> <?= CommentListService::create($task)->getCount() ?>
        </div>

        <div class="task-controls end">
            <?= TaskContextMenu::widget(['task' => $task]) ?>
        </div>
    </div>

    <?php if ($details) : ?>
        <?= TaskListDetails::widget(['task' => $task]) ?>
    <?php endif; ?>

<?= Html::endTag('div') ?>
