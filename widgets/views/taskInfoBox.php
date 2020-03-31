<?php
/* @var $this \humhub\components\View */
/* @var $title string */
/* @var $value string */
/* @var $icon string */
/* @var $cssClass string */
/* @var $textClass string */

$value = is_array($value) ? $value : [$value];
?>

<div class="task-info <?= $cssClass ?>">
    <strong><i class="fa <?= $icon ?>"></i> <?= $title ?>
    </strong><br>
    <span class="task-info-text <?= $textClass ?>">
        <?php foreach ($value as $val) : ?>
            <?php if (!empty($value)) : ?>
                <small><?= $val ?></small><br>
            <?php endif; ?>
        <?php endforeach; ?>
    </span>
</div>


