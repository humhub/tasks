<?php
/* @var $this \humhub\components\View */
/* @var $title string */
/* @var $value string */
/* @var $icon string */
/* @var $cssClass string */
/* @var $textClass string */
?>

<div class="task-info <?= $cssClass ?>">
    <strong><i class="fa <?= $icon ?>"></i> <?= $title ?>
    </strong><br>
    <span class="task-info-text <?= $textClass ?>">
       <?= $value ?>
    </span>
</div>


