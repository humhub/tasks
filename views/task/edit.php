<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\assets\Assets;
use humhub\widgets\bootstrap\FormTabs;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */

Assets::register($this);
?>

<?php $form = Modal::beginFormDialog([
        'title' => $taskForm->getTitle(),
        'closable' => false,
        'closeButton' => false,
        'form' => ['enableClientValidation' => false, 'acknowledge' => true],
        'footer' => ModalButton::cancel() . ModalButton::save(null, $taskForm->getSubmitUrl()),
    ]) ?>
    <div id="task-form" data-ui-widget="task.Form" data-ui-init>

        <?= FormTabs::widget([
            'viewPath' => '@tasks/views/task',
            'params' => ['form' => $form, 'taskForm' => $taskForm],
            'form' => $taskForm,
        ]); ?>

    </div>
<?php Modal::endFormDialog() ?>