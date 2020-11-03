<?php
/* @var $this \humhub\modules\ui\view\components\View */
/* @var $title string */
/* @var $value string */
/* @var $icon string */
/* @var $iconColor string */
/* @var $cssClass string */
/* @var $textClass string */

$value = is_array($value) ? $value : [$value];

use humhub\libs\Html;
use humhub\modules\ui\icon\widgets\Icon; ?>

<div class="task-info <?= $cssClass ?>">
    <strong><?= Icon::get($icon)->color($iconColor)?> <?= Html::encode($title) ?>
    </strong><br>
    <span class="task-info-text <?= $textClass ?>">
        <?php foreach ($value as $val) : ?>
            <?php if (!empty($value)) : ?>
                <small><?= $val ?></small><br>
            <?php endif; ?>
        <?php endforeach; ?>
    </span>
</div>


