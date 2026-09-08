<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\components\View;
use humhub\modules\tasks\models\Task;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $this View */
/* @var $task Task */
/* @var $canManageEntries boolean */
/* @var $editUrl string */
?>
<?php Modal::beginDialog([
    'size' => Modal::SIZE_LARGE,
    'footer' => ModalButton::cancel(Yii::t('TasksModule.base', 'Close'))
        . ($canManageEntries ? ModalButton::primary(Yii::t('TasksModule.base', 'Edit'))->load($editUrl) : ''),
]) ?>
    <?= $this->renderAjax('modal_entry', ['task' => $task]) ?>
<?php Modal::endDialog() ?>
