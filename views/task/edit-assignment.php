<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\scheduling\TaskScheduling;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\Link;

/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */
/* @var $responsible [] \humhub\modules\user\models\User */

$responsible = $taskForm->task->taskResponsibleUsers;
array_push($responsible, Yii::$app->user->getIdentity()); // add creator to responsible users
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

</div>