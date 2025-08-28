<?php

use humhub\components\View;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\ChangeStatusButton;
use humhub\modules\tasks\widgets\checklist\TaskChecklist;
use humhub\modules\tasks\widgets\TaskInfoBox;
use humhub\modules\tasks\widgets\TaskRoleInfoBox;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicBadge;
use humhub\widgets\bootstrap\Button;


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
            <?= TaskRoleInfoBox::widget(['task' => $task, 'iconColor' => $color]) ?>
            <?= TaskInfoBox::widget([
                'title' => Yii::t('TasksModule.base', 'Scheduling'),
                'value' => $task->schedule->getFormattedDateTime(),
                'icon' => 'clock-o',
                'iconColor' => $color,
                'textClass' => $scheduleTextClass]) ?>

            <?php if ($task->schedule->canRequestExtension()): ?>
                <div style="display:inline-block;vertical-align:bottom;">
                    <?= Button::primary()
                        ->icon('calendar-plus-o')
                        ->sm()
                        ->link(TaskUrl::requestExtension($task))
                        ->tooltip(Yii::t('TasksModule.base', 'Request extension')) ?>
                </div>
            <?php endif; ?>

            <?= ChangeStatusButton::widget(['task' => $task]) ?>
        </div>

        <div class="task-list-task-topics">
            <?php foreach (Topic::findByContent($task->content)->all() as $topic) : ?>
                <?= TopicBadge::forTopic($topic) ?>
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
