<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\ui\menu\MenuEntry;
use humhub\widgets\Link;

/* @var MenuEntry[] $entries */
/* @var array $options */
/* @var Link $toggler */
/* @var Task $task */
?>
<div data-ui-widget="stream.StreamEntry" data-content-key="<?= $task->content->id ?>">
    <div class="stream-entry-loader"></div>
    <?= Html::beginTag('ul', $options) ?>
    <li class="dropdown">
        <?= $toggler->cssClass('dropdown-toggle')->options(['data-toggle' => 'dropdown']) ?>
        <ul class="dropdown-menu pull-right">
            <?php foreach ($entries as $entry) : ?>
                <li><?= $entry->render() ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?= Html::endTag('ul') ?>
</div>
