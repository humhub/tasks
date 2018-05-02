<?php
/* @var $this \humhub\components\View */

use humhub\widgets\Button;


/* @var $task \humhub\modules\tasks\models\Task */
/* @var $primaryStateConfig array */
/* @var $primaryUrl string */
/* @var $proceedConfig array */
/* @var $revertConfig array */

?>

<div class="btn-group pull-right task-change-state-button">
    <?= Button::primary($primaryStateConfig['label'])->action('task.changeState', $primaryUrl)->sm()->icon($primaryStateConfig['icon'])->loader(true);?>

    <?php if(!empty($proceedConfig) || !empty($revertConfig)) : ?>
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <?php foreach($proceedConfig as $status => $config) : ?>
                <?php $status = $task->state->getStateInstance($status) ?>
                <li>
                    <?= Button::asLink($config['label'])->action('task.changeState', $status->getProceedUrl())->icon($config['icon']);?>
                </li>
            <?php endforeach; ?>

            <?php if(!empty($proceedConfig) && !empty($revertConfig)) : ?>
                <li role="separator" class="divider"></li>
            <?php endif; ?>

            <?php foreach($revertConfig as $status => $config) : ?>
                <?php $status = $task->state->getStateInstance($status) ?>
                <li>
                    <?= Button::asLink($config['label'])->action('task.changeState', $status->getRevertUrl())->icon($config['icon']);?>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>
</div>
