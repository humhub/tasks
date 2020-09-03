<?php
/* @var $this \humhub\modules\ui\view\components\View */
/* @var $taskList \humhub\modules\tasks\models\lists\TaskList */
/* @var $canEdit boolean */
/* @var $options array */

use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\widgets\Button;
use yii\helpers\Html;

?>

<?= Html::beginTag('div', $options)?>

    <div class="media-body">
        <span class="task-list-title">
            <?= Html::encode($taskList->title); ?>
        </span>
        <div class="pull-right task-controls end">
            <?= Button::asLink()
                ->options(['class' => 'tt', 'title' => Yii::t('TasksModule.base', 'Delete')]
                )->icon('fa-trash')->right()->xs()->action('deleteList', TaskListUrl::deleteTaskList($taskList))->loader(false)->visible($canEdit)->confirm() ?>
            <?= Button::asLink()
                ->options(['class' => 'tt', 'title' => Yii::t('TasksModule.views_index_index', 'Edit'), 'style' => 'margin-left:2px']
                )->icon('fa-pencil')->right()->xs()->action('task.list.edit', TaskListUrl::editTaskList($taskList))->loader(false)->visible($canEdit) ?>
        </div>
    </div>

<?= Html::endTag('div') ?>
