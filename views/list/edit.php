<?php

use humhub\helpers\Html;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $model TaskList */
?>
<?php $form = Modal::beginFormDialog([
    'title' => $model->isNewRecord
        ? Yii::t('TasksModule.base', '<strong>Create</strong> task list')
        : Yii::t('TasksModule.base', '<strong>Edit</strong> task list'),
    'footer' => ModalButton::cancel() . ModalButton::save()->submit(),
])?>
    <?= Html::activeLabel($model, 'color') ?>
    <div id="event-color-field" class="input-group input-color-group">
        <?= $form->field($model, 'color')->colorInput() ?>
        <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('TasksModule.base', 'Title of your task list')]) ?>
    </div>
    <?= $form->field($model->addition, 'hide_if_completed')->checkbox() ?>
<?php Modal::endFormDialog() ?>
