<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\libs\Html;
use humhub\modules\ui\form\widgets\DatePicker;

/* @var $this \humhub\modules\ui\view\components\View */
?>
<?= isset($options['label']) ? Html::label($options['label']) : '' ?>
<?= DatePicker::widget([
    'dateFormat' => Yii::$app->formatter->dateInputFormat,
    'clientOptions' => [],
    'options' => array_merge(['autocomplete' => "on"], $options)
]) ?>