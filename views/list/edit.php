<?php
/* @var $this \humhub\components\View */
/* @var $model \humhub\modules\tasks\models\lists\TaskList */

use humhub\modules\ui\form\widgets\ColorPicker;
use humhub\widgets\modal\ModalButton;
use humhub\widgets\modal\Modal;

$title = $model->isNewRecord ? Yii::t('TasksModule.base', '<strong>Create</strong> task list') : Yii::t('TasksModule.base', '<strong>Edit</strong> task list')

?>

<?php $form = Modal::beginFormDialog([
        'title' => $title,
        'footer' => ModalButton::cancel() . ModalButton::save(),
    ])?>
    <div id="event-color-field" class="mb-3 space-color-chooser-edit">
        <?= $form->field($model, 'color')->colorInput()->label(Yii::t('TasksModule.base', 'Title and Color')); ?>

        <?= $form->field($model, 'name', ['template' => '
                            {label}
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i></i>
                                </span>
                                {input}
                            </div>
                            {error}{hint}'
        ])->textInput(['placeholder' => Yii::t('TasksModule.base', 'Title of your task list'), 'maxlength' => true])->label(false) ?>

        <?= $form->field($model->addition, 'hide_if_completed')->checkbox() ?>
    </div>
<?php Modal::endFormDialog() ?>