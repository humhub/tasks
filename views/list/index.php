<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\components\View;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\widgets\lists\CompletedTaskListView;
use humhub\modules\tasks\widgets\lists\TaskListWidget;
use humhub\modules\tasks\widgets\lists\UnsortedTaskListWidget;
use humhub\modules\tasks\widgets\TaskSubMenu;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $this View */
/* @var $canManage boolean */
/* @var $canCreate boolean */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $taskLists TaskList[] */

Assets::register($this);
?>

<div class="task-list" data-ui-widget="task.list.Root" data-ui-init="1" data-drop-list-url="<?= TaskListUrl::dropTaskList($contentContainer) ?>">
    <div class="task-list-tabs">
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

    <?php if (TaskList::hasCompletedLists($contentContainer)): ?>
    <div class="task-list-ul">
        <div class="task-list-li">
            <div class="closed-task-lists-container task-list-container" data-ui-widget="task.list.CompletedTaskListView" data-ui-init="1">
                <div class="task-list-title-bar clearfix">
                    <div>
                        <?= Icon::get('check-square-o') ?> <span class="task-list-title-text"><?= Yii::t('TasksModule.base', 'Completed lists') ?></span>
                    </div>
                </div>
                <div class="closed-task-list-view">
                    <?= CompletedTaskListView::widget(['contentContainer' => $contentContainer]) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="task-list-empty empty d-none">
        <?= Yii::t('TasksModule.base','No pending tasks found') ?>
    </div>
</div>
