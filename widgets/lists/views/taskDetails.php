<?php

use humhub\components\View;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\ChangeStatusButton;
use humhub\modules\tasks\widgets\checklist\TaskChecklist;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicBadge;

/* @var $this View */
/* @var $task Task */
?>
<div class="task-list-task-details">
    <div class="task-list-task-details-body clearfix">
        <?= ChangeStatusButton::widget(['task' => $task]) ?>

        <?= $this->render('@tasks/widgets/views/taskInfos', ['task' => $task]) ?>

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
