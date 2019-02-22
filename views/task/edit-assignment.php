<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\scheduling\TaskScheduling;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\Button;
use humhub\widgets\Link;

/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */
/* @var $responsible [] \humhub\modules\user\models\User */

$responsible = $taskForm->task->taskResponsibleUsers;

//array_push($responsible, Yii::$app->user->getIdentity()); // add creator to responsible users
?>

<div class="modal-body">


    <?= $form->field($taskForm->task, 'assignedUsers')->widget(UserPickerField::class, [
        'id' => 'taskAssignedUserPicker',
        'selection' => $taskForm->task->taskAssignedUsers,
        'url' => $taskForm->getTaskAssignedPickerUrl(),
        'placeholder' => Yii::t('TasksModule.views_index_edit', 'Assign users')
    ])->hint(Yii::t('TasksModule.views_index_edit', 'Leave empty to let anyone work on this task.'),[]) ?>

    <?= Link::userPickerSelfSelect('#taskAssignedUserPicker'); ?>

    <br>

    <?= $form->field($taskForm->task, 'responsibleUsers')->widget(UserPickerField::class, [
        'id' => 'taskResponsibleUserPicker',
        'selection' => $responsible,
        'url' => $taskForm->getTaskResponsiblePickerUrl(),
        'placeholder' => Yii::t('TasksModule.views_index_edit', 'Add responsible users'),
    ]) ?>

    <?= Link::userPickerSelfSelect('#taskResponsibleUserPicker'); ?>

    <br>
    <?= $form->field($taskForm->task, 'review')->checkbox() ?>

    <div class="clearfix">
        <?= Button::info()->icon('fa-info-circle')->xs()->right()->options(['data-toggle' => 'collapse', 'data-target' => '#task-assignment-info'])->loader(false) ?>
    </div>

    <div id="task-assignment-info" class="alert alert-default collapse">
        <?= Yii::t('TasksModule.base', '<strong>Assigned users</strong> are allowed to process this task.') ?>
        <?= Yii::t('TasksModule.base', 'If no assigned user is selected, every space member with the permission to process unassigned tasks can process the task.') ?>
        <?= Yii::t('TasksModule.base', 'In case the review option is active, a <strong>responsible user</strong> will have to review and either reject or confirm this task before completion.') ?>
    </div>


</div>