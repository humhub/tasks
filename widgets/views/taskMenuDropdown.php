<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
?>

<?php

use humhub\widgets\Button;
use humhub\widgets\Link;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\MoveContentLink;

?>

<div class="pull-right">
    <ul class="nav nav-pills preferences">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-cog"></i>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu pull-right">

                <li>
                    <?= ModalButton::asLink(Yii::t('TasksModule.base', 'Edit'))->post($editUrl)->icon('fa-edit')->visible($canEdit) ?>
                </li>
                <li>
                    <?= MoveContentLink::widget(['model' => $task]) ?>
                </li>
                <li>
                    <?= Link::asLink(Yii::t('TasksModule.base', 'Delete'))->action('ui.modal.post', $deleteUrl)->icon('fa-trash')
                        ->confirm(Yii::t('TasksModule.views_index_edit', '<strong>Confirm</strong> task deletion'),
                            Yii::t('TasksModule.views_index_edit', 'Do you really want to delete this task?'),
                            Yii::t('TasksModule.base', 'Delete'))->visible($canEdit); ?>
                </li>
                <li>
                    <?= Button::asLink(Yii::t('TasksModule.views_index_index', 'Reset Task'), $resetUrl)->icon('fa-undo')->visible($canReset);?>
                </li>
            </ul>
        </li>
    </ul>
</div>