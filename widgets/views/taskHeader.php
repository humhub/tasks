<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\widgets\Button;

/* @var string $addTaskUrl */
?>
<div class="panel panel-default task-list-header">
    <div class="panel-body clearfix">
        <?php if ($addTaskUrl) : ?>
            <?= Button::success(Yii::t('TasksModule.base', 'Add'))
                ->action('ui.modal.load', $addTaskUrl)
                ->icon('fa-plus')
                ->right()
                ->style('margin:0 0 20px 20px')
                ->loader(false) ?>
        <?php endif; ?>
        <h4><?= Yii::t('TasksModule.base', 'Tasks') ?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'Create and assign tasks - organize and schedule individual and collaborative projects.') ?></div>
    </div>
</div>