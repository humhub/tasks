<?php
/* @var $title string */
/* @var $value array|string */
/* @var $icon string */
/* @var $iconColor string */
/* @var $cssClass string */
/* @var $textClass string */

$value = is_array($value) ? $value : [$value];

use humhub\helpers\Html;
use humhub\modules\ui\icon\widgets\Icon;
?>

<div class="task-info <?= $cssClass ?>">
    <strong><?= Icon::get($icon)->color($iconColor)?> <?= Html::encode($title) ?></strong>
    <span class="task-info-text <?= $textClass ?>">
        <?php foreach ($value as $val) : ?>
            <?php if (!empty($value)) : ?>
                <?= $val ?><br>
            <?php endif; ?>
        <?php endforeach; ?>
    </span>
</div>
