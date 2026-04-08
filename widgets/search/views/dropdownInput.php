<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2020 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\helpers\Html;

/* @var $this View */
/* @var $options [] */
/* @var $selection [] */
/* @var $items [] */
?>
<?= isset($options['label']) ? Html::label($options['label']) : '' ?>
<?= Html::dropDownList(null, $selection, $items, $options) ?>
