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
use humhub\modules\tasks\widgets\lists\TaskListDetails;
use humhub\modules\tasks\widgets\TaskSubMenu;
use humhub\widgets\MarkdownView;
use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\tasks\widgets\checklist\TaskChecklist;

\humhub\modules\tasks\assets\Assets::register($this);

$canEdit = $task->content->canEdit();

$collapse = true;

$this->registerJsConfig('task', [
    'text' => [
        'success.notification' => Yii::t('TasksModule.views_index_task', 'Task Users have been notified')
    ]
]);

?>
<?= TaskSubMenu::widget() ?>
<div id="task-container" class="panel panel-default task-details">

    <?= $this->render('task_header', [
        'canEdit' => $canEdit,
        'contentContainer' => $contentContainer,
        'task' => $task
    ]); ?>

    <div class="panel-body task-list-items">
        <div class="cleafix task-list-item">
            <?= TaskListDetails::widget(['task' => $task])?>
        </div>
    </div>
</div>


