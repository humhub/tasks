<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $this \humhub\components\View */
/* @var $task \humhub\modules\tasks\models\Task */

/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */


use humhub\modules\tasks\widgets\ChangeStatusButton;
use humhub\widgets\MarkdownView;
use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\tasks\widgets\TaskItemList;

\humhub\modules\tasks\assets\Assets::register($this);

$isResponsible = $task->isTaskResponsible();
$printUrl = $contentContainer->createUrl('print', ['id' => $task->id]);
$shareLink = $contentContainer->createUrl('share', ['id' => $task->id]);
$editUrl = $contentContainer->createUrl('edit', ['id' => $task->id]);

// todo --> change in controller
$actionUrl = '#';

$collapse = true;

$this->registerJsConfig('task', [
    'text' => [
        'success.notification' => Yii::t('TasksModule.views_index_task', 'Task Users have been notified')
    ]
]);

?>

<div id="task-container" class="panel panel-default task-details">

    <?= $this->render('task_header', [
        'canEdit' => $isResponsible,
        'contentContainer' => $contentContainer,
        'task' => $task
    ]); ?>

    <div class="panel-body">

        <div class="cleafix">
            <?php if (!empty($task->description)) : ?>
                <div style="display:inline-block;">
                    <div <?= ($collapse) ? 'data-ui-show-more' : '' ?>
                            data-read-more-text="<?= Yii::t('TasksModule.views_entry_view', 'Read full description...') ?>"
                            style="overflow:hidden">
                        <?= MarkdownView::widget(['markdown' => $task->description]); ?>
                    </div>
                </div>
                <br><br>
            <?php endif; ?>

            <?php if ($task->hasItems()) : ?>
                <div class="">
                    <em><strong><?= Yii::t('TasksModule.views_index_index', 'Checklist') ?>:</strong></em><br><br>
                    <?= TaskItemList::widget(['task' => $task, 'canEdit' => $isResponsible]) ?>
                </div>
                <br>
            <?php endif; ?>



        </div>
        <?php if ($task->content->canView()) : // If the task is private and non space members are invited the task is visible, but not commentable etc. ?>
            <?= WallEntryAddons::widget([
                'object' => $task
            ]); ?>
        <?php else: ?>
            <br>
        <?php endif; ?>
    </div>
</div>


