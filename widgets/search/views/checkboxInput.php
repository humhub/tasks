<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use yii\helpers\Html;

/* @var $options array */
/* @var $title string */
/* @var $checked boolean */
/* @var $iconInActive boolean */
/* @var $iconActive boolean */
?>
<?= isset($options['label']) ? Html::label($options['label']) : '' ?>
<?= Html::beginTag('a', $options) ?>
<i class="fa  <?= ($checked) ? $iconActive : $iconInActive ?>"></i> <?= $title ?>
<?= Html::endTag('a') ?>