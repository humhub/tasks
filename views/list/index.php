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
/* @var $canCreate boolean*/
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $taskLists \humhub\modules\tasks\models\lists\TaskList[] */
/* @var $unsortedTasks \humhub\modules\tasks\models\Task[] */

Assets::register($this);

?>

<div class="panel panel-default task-list">
    <?= TaskSubMenu::widget() ?>

    <div class="panel-body clearfix">
        <?= Button::success(Yii::t('TasksModule.base', 'Add Task List'))
            ->action('task.list.create', TaskListUrl::createTaskList($contentContainer))->sm()->icon('fa-plus')->right()->loader(false)->visible($canManage); ?>
        <h4><?= Yii::t('TasksModule.base', 'Task Lists')?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'Here you can manage your task lists in order to categorize and sort tasks.') ?></div>
    </div>

    <div class="panel-body">
        <div data-ui-widget="task.list.Root" data-ui-init="1" data-drop-list-url="<?= TaskListUrl::dropTaskList($contentContainer) ?>">
            <div class="clearfix">
                <?= Button::defaultType(Yii::t('TasksModule.base', 'Toggle lists'))->id('toggle-lists')
                    ->action('collapseAll')->loader(false)->right()->xs()->icon('fa-toggle-on'); ?>
                <br><br>
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
                    <?= UnsortedTaskListWidget::widget(['hasOtherLists' => !empty($taskLists), 'canManage' => $canManage,  'canCreate' => $canCreate]) ?>
                </div>
            </div>

            <div class="closed-task-lists-container task-list-container" data-ui-widget="task.list.CompletedTaskListView" data-ui-init="1">
                <div class="task-list-title-bar clearfix">
                    <i class="fa fa-check-square-o"></i> <a href="#"><?= Yii::t('TasksModule.base', 'Finished Lists')?></a>
                </div>
                <div class="closed-task-list-view">
                    <?= CompletedTaskListView::widget(['contentContainer' => $contentContainer]) ?>
                </div>
            </div>

            <div class="task-list-empty empty" style="display:none">
                <?= Yii::t('TasksModule.base','No pending tasks found') ?>
            </div>
        </div>
    </div>

</div>
