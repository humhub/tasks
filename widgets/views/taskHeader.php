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
            <?= Button::success(Yii::t('TasksModule.base', 'Add task'))
                ->action('task.list.editTask', $addTaskUrl)
                ->icon('fa-plus')
                ->right()
                ->loader(false) ?>
        <?php endif; ?>
        <h4><?= Yii::t('TasksModule.base', 'Tasks') ?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci animi esse obcaecati unde voluptatem! Assumenda, sed.') ?></div>
    </div>
</div>