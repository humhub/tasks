<?php
/* @var $this \humhub\components\View */

use humhub\modules\comment\widgets\Comments;
use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\widgets\ChangeStatusButton;
use humhub\modules\tasks\widgets\TaskInfoBox;
use humhub\modules\tasks\widgets\checklist\TaskChecklist;
use humhub\modules\tasks\widgets\TaskRoleInfoBox;
use humhub\widgets\Button;
use humhub\widgets\MarkdownView;

/* @var $task \humhub\modules\tasks\models\Task */

$scheduleTextClass = '';

if (($task->schedule->isOverdue())) {
    $scheduleTextClass = 'colorDanger';
}
?>

<div class="task-list-task-details">

    <div class="task-list-task-details-body clearfix">
        <div class="task-list-task-infos">
            <?= TaskRoleInfoBox::widget(['task' => $task]) ?>
            <?= TaskInfoBox::widget([
                'title' => Yii::t('TasksModule.base', 'Scheduling'),
                'value' => $task->schedule->getFormattedDateTime(),
                'icon' => 'fa-clock-o',
                'textClass' => $scheduleTextClass]) ?>

            <?php if($task->schedule->canRequestExtension()): ?>
                <div style="display:inline-block;vertical-align:bottom;">
                    <?= Button::primary()->icon('fa-calendar-plus-o')->xs()->cssClass('tt')->link(TaskUrl::requestExtension($task))->options(['title' => Yii::t('TasksModule.base', 'Request extension')])?>
                </div>
            <?php endif; ?>

            <?= ChangeStatusButton::widget(['task' => $task])?>

        </div>

        <?= MarkdownView::widget(['markdown' => $task->description]); ?>

        <?= TaskChecklist::widget(['task' => $task]) ?>

    </div>

    <?= WallEntryAddons::widget(['object' => $task]); ?>
</div>
