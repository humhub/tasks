<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use yii\bootstrap\Html;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $options array */
?>
<div class="form-group">
    <?= isset($options['label']) ? Html::label($options['label']) : '' ?>
    <?= Html::textInput(null,null, $options) ?>
</div>
