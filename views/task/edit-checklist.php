<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use yii\bootstrap\Html;
use humhub\modules\tasks\widgets\AddItemsInput;


/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */
/* @var $item \humhub\modules\tasks\models\checklist\TaskItem */

?>

<div class="modal-body">
    <?php foreach ($taskForm->task->items as $item) : ?>
        <div class="form-group">
            <div class="input-group">
                <?= Html::textInput($taskForm->formName() . '[editItems][' . $item->id . ']', $item->title, [
                    'class' => 'form-control task_item_old_input',
                    'placeholder' => Yii::t('TasksModule.views_index_edit', 'Edit item (empty field will be removed)...')]) ?>
                <div class="input-group-addon" style="cursor:pointer;" data-action-click="removeTaskItem">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?= AddItemsInput::widget(['name' => $taskForm->formName() . '[newItems][]']); ?>
</div>