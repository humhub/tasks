<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\content\widgets\stream\StreamEntryWidget;
use humhub\modules\content\widgets\stream\WallStreamEntryOptions;
use humhub\modules\stream\assets\StreamAsset;
use humhub\modules\tasks\models\Task;

/* @var $task Task */

StreamAsset::register($this);
?>
<div data-action-component="stream.SimpleStream">
    <?= StreamEntryWidget::renderStreamEntry($task, (new WallStreamEntryOptions())->disableControlsEntryPin()) ?>
</div>

