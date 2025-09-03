<?php
/* @var $this \humhub\components\View */
/* @var $taskList \humhub\modules\tasks\models\lists\TaskList */
/* @var $canEdit boolean */
/* @var $options array */

use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\widgets\bootstrap\Button;
use humhub\helpers\Html;

?>

<?= Html::beginTag('div', $options)?>

    <div class="flex-grow-1">
        <span class="task-list-title">
            <?= Html::encode($taskList->title); ?>
        </span>
        <div class="float-end task-controls end">
            <?= Button::asLink()
                ->tooltip(Yii::t('TasksModule.base', 'Delete'))
                ->icon('trash')
                ->right()->sm()
                ->action('deleteList', TaskListUrl::deleteTaskList($taskList))
                ->loader(false)
                ->visible($canEdit)
                ->confirm() ?>
            <?= Button::asLink()
                ->tooltip(Yii::t('TasksModule.base', 'Edit'))
                ->icon('pencil')
                ->right()->sm()->cssClass('me-2')
                ->action('task.list.edit', TaskListUrl::editTaskList($taskList))
                ->loader(false)
                ->visible($canEdit) ?>
        </div>
    </div>

<?= Html::endTag('div') ?>
