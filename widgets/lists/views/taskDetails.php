<?php
/* @var $this \humhub\modules\ui\view\components\View */

use humhub\modules\comment\widgets\Comments;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\widgets\ChangeStatusButton;
use humhub\modules\tasks\widgets\TaskInfoBox;
use humhub\modules\tasks\widgets\checklist\TaskChecklist;
use humhub\modules\tasks\widgets\TaskRoleInfoBox;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicLabel;
use humhub\widgets\Button;

/* @var $task \humhub\modules\tasks\models\Task */

$scheduleTextClass = '';

if (($task->schedule->isOverdue())) {
    $scheduleTextClass = 'colorDanger';
}

$color = $task->getColor() ? $task->getColor() : $this->theme->variable('info');
?>

<div class="task-list-task-details">

    <div class="task-list-task-details-body clearfix">


        <div class="task-list-task-infos">
            <?= TaskRoleInfoBox::widget(['task' => $task, 'iconColor' => $color]) ?>
            <?= TaskInfoBox::widget([
                'title' => Yii::t('TasksModule.base', 'Scheduling'),
                'value' => $task->schedule->getFormattedDateTime(),
                'icon' => 'fa-clock-o',
                'iconColor' => $color,
                'textClass' => $scheduleTextClass]) ?>

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
                <?= RichText::output($task->description)?>
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
