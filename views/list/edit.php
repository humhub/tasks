<?php
/* @var $this \humhub\components\View */
/* @var $model \humhub\modules\tasks\models\lists\TaskList */

use humhub\modules\ui\form\widgets\ColorPicker;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\bootstrap\ActiveForm;

$header = $model->isNewRecord ? Yii::t('TasksModule.base', '<strong>Create</strong> task list') : Yii::t('TasksModule.base', '<strong>Edit</strong> task list')

?>

<?php ModalDialog::begin(['header' => $header])?>
    <?php $form = ActiveForm::begin() ?>
        <div class="modal-body">
            <div id="event-color-field" class="form-group space-color-chooser-edit">
                <?= $form->field($model, 'color')->widget(ColorPicker::class, ['container' => 'event-color-field'])->label(Yii::t('TasksModule.views_index_edit', 'Title and Color')); ?>

                <?= $form->field($model, 'name', ['template' => '
                                    {label}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i></i>
                                        </span>
                                        {input}
                                    </div>
                                    {error}{hint}'
                ])->textInput(['placeholder' => Yii::t('TasksModule.views_index_edit', 'Title of your task'), 'maxlength' => true])->label(false) ?>

                <?= $form->field($model->addition, 'hide_if_completed')->checkbox() ?>
            </div>
        </div>
        <div class="modal-footer">
            <?= ModalButton::submitModal() ?>
            <?= ModalButton::cancel() ?>
        </div>
    <?php ActiveForm::end() ?>
<?php ModalDialog::end() ?>