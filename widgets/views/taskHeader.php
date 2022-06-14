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
                ->action('task.list.editTask', $addTaskUrl)
                ->icon('fa-plus')
                ->right()
                ->loader(false) ?>
        <?php endif; ?>
        <h4><?= Yii::t('TasksModule.base', 'Tasks') ?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'Create tasks, schedule individual and collaborative projects and assign other users in your network to tasks and activities. Use the various filters to sort and view tasks.') ?></div>
    </div>
</div>