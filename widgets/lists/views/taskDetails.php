<?php
/* @var $this \humhub\components\View */

use humhub\modules\comment\widgets\Comments;
use humhub\modules\tasks\widgets\ChangeStatusButton;
use humhub\modules\tasks\widgets\TaskInfoBox;
use humhub\modules\tasks\widgets\TaskItemList;
use humhub\modules\tasks\widgets\TaskRoleInfoBox;
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

            <?= ChangeStatusButton::widget(['task' => $task])?>
        </div>

        <?= MarkdownView::widget(['markdown' => $task->description]); ?>

        <?= TaskItemList::widget(['task' => $task]) ?>

    </div>

    <?= Comments::widget(['object' => $task]); ?>

</div>
