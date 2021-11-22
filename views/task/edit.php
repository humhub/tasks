<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\assets\Assets;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\widgets\ActiveForm;
use humhub\widgets\Tabs;

/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */

Assets::register($this);
?>

<?php ModalDialog::begin(['header' => $taskForm->getTitle(), 'closable' => false]) ?>

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'acknowledge' => true]); ?>

        <div id="task-form" data-ui-widget="task.Form" data-ui-init>

            <?= Tabs::widget([
                'viewPath' => '@tasks/views/task',
                'params' => ['form' => $form, 'taskForm' => $taskForm],
                'items' => $taskForm->getTabs(),
            ]); ?>

        </div>

        <hr>

        <div class="modal-footer">
            <div class="col-md-12 text-center">
                <?= ModalButton::submitModal($taskForm->getSubmitUrl()); ?>
                <?= ModalButton::cancel(); ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

<?php ModalDialog::end() ?>