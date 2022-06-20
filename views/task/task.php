<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\widgets\lists\TaskListDetails;
use humhub\modules\tasks\widgets\TaskHeader;
use humhub\modules\tasks\widgets\TaskSubMenu;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $task \humhub\modules\tasks\models\Task */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

Assets::register($this);

$this->registerJsConfig('task', [
    'text' => [
        'success.notification' => Yii::t('TasksModule.base', 'Task Users have been notified')
    ]
]);
?>
<?= TaskHeader::widget() ?>
<?= TaskSubMenu::widget() ?>
<div id="task-container" class="panel panel-default task-details">

    <?= $this->render('task_header', [
        'canEdit' => $task->content->canEdit(),
        'contentContainer' => $contentContainer,
        'task' => $task
    ]); ?>

    <div class="panel-body task-list-items">
        <div class="cleafix task-list-item">
            <?= TaskListDetails::widget(['task' => $task])?>
        </div>
    </div>
</div>