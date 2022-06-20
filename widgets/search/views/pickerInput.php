<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

/* @var $pickerClass string */
/* @var $pickerOptions array */
/* @var $options array */

use humhub\libs\Html;

?>
<?= isset($options['label']) ? Html::label($options['label']) : '' ?>
<?= call_user_func($pickerClass.'::widget', $pickerOptions) ?>