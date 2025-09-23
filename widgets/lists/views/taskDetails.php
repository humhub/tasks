<?php

use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\ChangeStatusButton;
use humhub\modules\tasks\widgets\TaskInfoBox;
use humhub\modules\tasks\widgets\checklist\TaskChecklist;
use humhub\modules\tasks\widgets\TaskUserList;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicLabel;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Button;


/* @var $this View */
/* @var $task Task */

$scheduleTextClass = '';

if (($task->schedule->isOverdue())) {
    $scheduleTextClass = 'colorDanger';
}

$color = $task->getColor('var(--info)');
?>

<div class="task-list-task-details">

    <div class="task-list-task-details-body clearfix">


        <div class="task-list-task-infos">
            <?= TaskInfoBox::widget([
                'title' => Yii::t('TasksModule.base', 'Task ID') . ':',
                'value' => $task->id,
                'icon' => 'info-circle',
                'iconColor' => $color]) ?>
            <?php if ($task->hasTaskResponsible()) : ?>
                <?= TaskInfoBox::widget([
                    'title' => Yii::t('TasksModule.base', 'Responsible') . ':',
                    'value' => TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'type' => Task::USER_RESPONSIBLE]),
                    'icon' => 'user',
                    'iconColor' => $color]) ?>
            <?php endif ?>
            <?php if ($task->hasTaskAssigned()) : ?>
                <?= TaskInfoBox::widget([
                    'title' => Yii::t('TasksModule.base', 'Assignments') . ':',
                    'value' => TaskUserList::widget(['users' => $task->taskAssignedUsers]),
                    'icon' => 'users',
                    'iconColor' => $color]) ?>
            <?php endif ?>
            <?= TaskInfoBox::widget([
                'title' => Yii::t('TasksModule.base', 'Scheduling') . ':',
                'value' => Yii::t('TasksModule.base', 'Start') . ' ' . $task->schedule->getFormattedStartDateTime()
                    . '<br>'
                    . Html::tag('span', $task->schedule->getFormattedDateTime(), ['class' => $scheduleTextClass]),
                'icon' => 'clock-o',
                'iconColor' => $color]) ?>

            <?php if ($task->schedule->canRequestExtension()): ?>
                <div style="display:inline-block;vertical-align:bottom;">
                    <?= Button::primary()->icon('fa-calendar-plus-o')->xs()->cssClass('tt')->link(TaskUrl::requestExtension($task))->options(['title' => Yii::t('TasksModule.base', 'Request extension')]) ?>
                </div>
            <?php endif; ?>

            <?= ChangeStatusButton::widget(['task' => $task]) ?>
        </div>

        <div class="task-list-task-topics">
            <?php foreach (Topic::findByContent($task->content)->all() as $topic) : ?>
                <?= TopicLabel::forTopic($topic) ?>
            <?php endforeach; ?>
        </div>

        <?php if(!empty($task->description)) : ?>
            <div class="task-details-body">
                <div class="markdown-render">
                    <?= RichText::output($task->description)?>
                </div>
            </div>
        <?php endif; ?>

        <?php if($task->hasItems()) : ?>
            <div class="task-details-body">
                <?= TaskChecklist::widget(['task' => $task]) ?>
            </div>
        <?php endif; ?>

    </div>

    <?= WallEntryAddons::widget(['object' => $task]); ?>
</div>
