<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\modules\tasks\widgets\AddItemsInput;


/* @var $form \humhub\widgets\form\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */
/* @var $item \humhub\modules\tasks\models\checklist\TaskItem */

?>

<?php foreach ($taskForm->task->items as $item) : ?>
    <div class="mb-3">
        <div class="input-group">
            <?= Html::textInput($taskForm->formName() . '[editItems][' . $item->id . ']', $item->title, [
                'class' => 'form-control task_item_old_input',
                'placeholder' => Yii::t('TasksModule.base', 'Edit item (empty field will be removed)...')]) ?>
            <div class="input-group-text" style="cursor:pointer;" data-action-click="removeTaskItem">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= AddItemsInput::widget(['name' => $taskForm->formName() . '[newItems][]']); ?>
<div class="form-text">
    <?= Yii::t('TasksModule.base', 'Add checkpoints to the task to highlight the individual steps required to complete it.') ?>
</div>