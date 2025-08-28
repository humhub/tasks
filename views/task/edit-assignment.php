<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\Link;

/* @var $form \humhub\widgets\form\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */
/* @var $responsible [] \humhub\modules\user\models\User */

$responsible = $taskForm->task->taskResponsibleUsers;

//array_push($responsible, Yii::$app->user->getIdentity()); // add creator to responsible users
?>

<?= $form->field($taskForm->task, 'assignedUsers')->widget(UserPickerField::class, [
    'id' => 'taskAssignedUserPicker',
    'selection' => $taskForm->task->taskAssignedUsers,
    'url' => $taskForm->getTaskAssignedPickerUrl(),
    'placeholder' => Yii::t('TasksModule.base', 'Assign users'),
])->hint(Yii::t('TasksModule.base', 'If empty any user can complete the task.'), []) ?>

<?= Link::userPickerSelfSelect('#taskAssignedUserPicker', Yii::t('TasksModule.base', 'Assign myself')); ?>

<br>

<?= $form->field($taskForm->task, 'responsibleUsers')->widget(UserPickerField::class, [
    'id' => 'taskResponsibleUserPicker',
    'selection' => $responsible,
    'url' => $taskForm->getTaskResponsiblePickerUrl(),
    'placeholder' => Yii::t('TasksModule.base', 'Add responsible users'),
]) ?>

<?= Link::userPickerSelfSelect('#taskResponsibleUserPicker', Yii::t('TasksModule.base', 'Assign myself')); ?>

<br>

<?= $form->field($taskForm->task, 'review')->checkbox() ?>

<div class="clearfix">
    <?= Button::accent()->icon('info-circle')->sm()->right()->options(['data-bs-toggle' => 'collapse', 'data-bs-target' => '#task-assignment-info'])->loader(false) ?>
</div>

<div id="task-assignment-info" class="alert alert-default collapse">
    <?= Yii::t('TasksModule.base', '<strong>Assigned users</strong> are allowed to process this task.') ?>
    <br>
    <?= Yii::t('TasksModule.base', 'If no assigned user is selected, every space member with the permission to process unassigned tasks can process the task.') ?>
    <br>
    <?= Yii::t('TasksModule.base', 'In case the review option is active, a <strong>responsible user</strong> will have to review and either reject or confirm this task before completion.') ?>
</div>
