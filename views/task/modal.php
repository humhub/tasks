<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
use humhub\widgets\modal\ModalButton;
use humhub\widgets\modal\Modal;

/* @var $this \humhub\components\View */
/* @var $task \humhub\modules\tasks\models\Task  */
/* @var $canManageEntries boolean  */
/* @var $editUrl string  */

?>

<?php Modal::beginDialog([
        'size' => Modal::SIZE_LARGE,
        'closable' => true,
        'closeButton' => true,
        'footer' => ModalButton::cancel(Yii::t('TasksModule.base', 'Close'))
            . ($canManageEntries) ? ModalButton::primary(Yii::t('TasksModule.base', 'Edit'))->load($editUrl)->loader(true) : '';
    ]); ?>
    <?= $this->renderAjax('modal_entry', ['task' => $task])?>
<?php Modal::endDialog(); ?>