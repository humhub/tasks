<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\modules\tasks\widgets\lists\CompletedTaskListView;
use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\widgets\lists\TaskListWidget;
use humhub\modules\tasks\widgets\lists\UnsortedTaskListWidget;
use humhub\modules\tasks\widgets\TaskSubMenu;
use humhub\widgets\Button;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $canManage boolean */
/* @var $canCreate boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $taskLists \humhub\modules\tasks\models\lists\TaskList[] */

Assets::register($this);
?>
<div class="panel panel-default task-list-header">
    <div class="panel-body clearfix">
        <?= Button::success(Yii::t('TasksModule.base', 'Add task'))
            ->action('task.list.editTask', TaskListUrl::addTaskListTask(null, $contentContainer))
            ->icon('fa-plus')
            ->right()
            ->loader(false)
            ->visible($canCreate) ?>
        <h4><?= Yii::t('TasksModule.base', 'Tasks') ?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci animi esse obcaecati unde voluptatem! Assumenda, sed.') ?></div>
    </div>
</div>

<div class="task-list" data-ui-widget="task.list.Root" data-ui-init="1" data-drop-list-url="<?= TaskListUrl::dropTaskList($contentContainer) ?>">
    <div class="task-list-tabs">
        <?= Button::defaultType(Yii::t('TasksModule.base', 'Toggle lists'))
            ->icon('fa-toggle-on')
            ->id('toggle-lists')
            ->action('collapseAll')
            ->loader(false)
            ->right() ?>
        <?= TaskSubMenu::widget() ?>
    </div>

    <div class="task-list-ul">
        <?php foreach ($taskLists as $taskList) : ?>
            <div class="task-list-li">
                <?= TaskListWidget::widget(['list' => $taskList, 'canManage' => $canManage, 'canCreate' => $canCreate]) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="task-list-ul task-list-unsorted">
        <div class="task-list-li">
            <?= UnsortedTaskListWidget::widget(['hasOtherLists' => !empty($taskLists), 'canManage' => $canManage, 'canCreate' => $canCreate]) ?>
        </div>
    </div>

    <div class="task-list-ul">
        <div class="task-list-li">
            <div class="closed-task-lists-container task-list-container" data-ui-widget="task.list.CompletedTaskListView" data-ui-init="1">
                <div class="task-list-title-bar clearfix">
                    <div>
                        <i class="fa fa-check-square-o"></i> <span class="task-list-title-text"><?= Yii::t('TasksModule.base', 'Finished Lists')?></span>
                    </div>
                </div>
                <div class="closed-task-list-view">
                    <?= CompletedTaskListView::widget(['contentContainer' => $contentContainer]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="task-list-empty empty" style="display:none">
        <?= Yii::t('TasksModule.base','No pending tasks found') ?>
    </div>
</div>