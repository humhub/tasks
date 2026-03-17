<?php

use humhub\modules\tasks\models\Task;
use humhub\widgets\bootstrap\Button;
use humhub\modules\tasks\helpers\TaskUrl;

/* @var $task Task */
/* @var $primaryStateConfig array */
/* @var $primaryUrl string */
/* @var $proceedConfig array */
/* @var $revertConfig array */

?>

<div class="btn-group float-end task-change-state-button">
    <?= Button::primary($primaryStateConfig['label'])->action('task.changeState', $primaryUrl)->sm()->icon($primaryStateConfig['icon'])->loader(true);?>

    <?php if(!empty($proceedConfig) || !empty($revertConfig)) : ?>
        <?= Button::primary()
            ->options(['data-bs-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'false'])
            ->cssClass('dropdown-toggle')->sm()
            ->loader(false) ?>
        <ul class="dropdown-menu">
            <?php foreach($proceedConfig as $status => $config) : ?>
                <?php $status = $task->state->getStateInstance($status) ?>
                <li class="dropdown-item">
                    <?= Button::asLink($config['label'])->action('task.changeState', $status->getProceedUrl())->icon($config['icon']);?>
                </li>
            <?php endforeach; ?>

            <?php if(!empty($proceedConfig) && !empty($revertConfig)) : ?>
                <li><hr class="dropdown-divider"></li>
            <?php endif; ?>

            <?php foreach($revertConfig as $status => $config) : ?>
                <?php $status = $task->state->getStateInstance($status) ?>
                <li class="dropdown-item">
                    <?= Button::asLink($config['label'])->action('task.changeState', $status->getRevertUrl())->icon($config['icon']);?>
                </li>
            <?php endforeach; ?>

            <?php if ($task->schedule->canRequestExtension()): ?>
                <?php if(!empty($proceedConfig) || !empty($revertConfig)) : ?>
                    <li role="separator" class="divider"></li>
                <?php endif ?>
                <li>
                    <?= Button::asLink(Yii::t('TasksModule.base', 'Request extension'))
                        ->icon('calendar-o')
                        ->link(TaskUrl::requestExtension($task)) ?>
                </li>
            <?php endif ?>
        </ul>

    <?php endif; ?>
</div>
