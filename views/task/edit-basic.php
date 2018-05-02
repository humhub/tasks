<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\lists\TaskList;
use humhub\widgets\ContentTagDropDown;
use humhub\widgets\MarkdownField;

/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */


if ($taskForm->task->color == null && isset($taskForm->contentContainer->color)) {
    $taskForm->task->color = $taskForm->contentContainer->color;
} elseif ($taskForm->task->color == null) {
    $taskForm->tasks->color = '#d1d1d1';
}

?>

<div class="modal-body">

    <?= $form->field($taskForm->task, 'title')->textInput(); ?>

    <?= $form->field($taskForm->task, 'task_list_id')->widget(ContentTagDropDown::class, [
        'prompt' => Yii::t('TasksModule.base', 'Unsorted'),
        'contentContainer' => $taskForm->contentContainer,
        'options' => ['data-ui-select2' => true],
        'tagClass' => TaskList::class
    ]); ?>

    <?= $form->field($taskForm->task, 'description')->widget(MarkdownField::class, ['fileModel' => $taskForm->task, 'fileAttribute' => 'files']) ?>

    <?= $form->field($taskForm, 'is_public')->checkbox() ?>
    <?= $form->field($taskForm->task, 'scheduling')->checkbox(['data-action-change' => 'toggleScheduling']) ?>



</div>