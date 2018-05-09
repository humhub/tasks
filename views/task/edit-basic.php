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

?>

<div class="modal-body">

    <?= $form->field($taskForm->task, 'title')->textInput(); ?>

    <?php if(TaskList::findByContainer($taskForm->contentContainer)->count()) : ?>
        <?= $form->field($taskForm->task, 'task_list_id')->widget(ContentTagDropDown::class, [
            'prompt' => Yii::t('TasksModule.base', 'Unsorted'),
            'contentContainer' => $taskForm->contentContainer,
            'options' => ['data-ui-select2' => true],
            'tagClass' => TaskList::class
        ]); ?>
    <?php endif; ?>

    <?= $form->field($taskForm->task, 'description')->widget(MarkdownField::class, ['fileModel' => $taskForm->task, 'fileAttribute' => 'files']) ?>

    <?= $form->field($taskForm, 'is_public')->checkbox() ?>
    <?= $form->field($taskForm->task, 'scheduling')->checkbox(['data-action-change' => 'toggleScheduling']) ?>



</div>