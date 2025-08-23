<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $options array */
?>
<div class="mb-3">
    <?= isset($options['label']) ? Html::label($options['label']) : '' ?>
    <?= Html::textInput(null,null, $options) ?>
</div>
