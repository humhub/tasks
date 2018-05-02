<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\widgets\lists\TaskListWidget;
use humhub\modules\tasks\widgets\lists\UnsortedTaskListWidget;
use humhub\modules\tasks\widgets\TaskSubMenu;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;

/* @var $this \humhub\components\View */
/* @var $canEdit string */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $taskLists \humhub\modules\tasks\models\lists\TaskList[] */
/* @var $unsortedTasks \humhub\modules\tasks\models\Task[] */

$createUrl = $contentContainer->createUrl('/tasks/list/edit');
$dropListUrl = $contentContainer->createUrl('/tasks/list/drop-task-list');

\humhub\modules\tasks\assets\Assets::register($this);

?>

<div class="panel panel-default task-list">
    <?= TaskSubMenu::widget() ?>

    <?php if($canEdit) : ?>
        <div class="panel-body clearfix">
            <?= Button::success(Yii::t('TasksModule.base', 'Add Task List'))->action('task.list.create', $createUrl)->sm()->icon('fa-plus')->right()->loader(false); ?>
            <h4>Task Lists</h4>
            <div class="help-block"><?= Yii::t('TasksModule.base', 'Here you can manage your task lists in order to categorize and sort tasks.') ?></div>
        </div>
    <?php endif; ?>

    <div class="panel-body">
        <div data-ui-widget="task.list.Root" data-ui-init="1" data-drop-list-url="<?= $dropListUrl ?>">
            <div class="task-list-ul">
                <?php foreach ($taskLists as $taskList) : ?>
                    <div class="task-list-li">
                        <?= TaskListWidget::widget(['list' => $taskList]) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="task-list-ul task-list-unsorted">
                <div class="task-list-li">
                    <?= UnsortedTaskListWidget::widget(['hasOtherLists' => !empty($taskLists)]) ?>
                </div>
            </div>
        </div>
    </div>

</div>
