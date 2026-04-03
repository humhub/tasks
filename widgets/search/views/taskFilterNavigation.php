<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\modules\admin\widgets\ExportButton;
use humhub\modules\tasks\widgets\search\TaskFilterNavigation;
use humhub\modules\ui\filter\widgets\FilterPanel;
use humhub\modules\tasks\widgets\search\TextFilterInput;

/* @var array $panels */
/* @var array $options */
?>
<?= Html::beginTag('div', $options) ?>
<div class="row">
    <div class="col-lg-12">
        <?= TextFilterInput::widget($panels[TaskFilterNavigation::PANEL_POSITION_TOP][0]['filters'][0]) ?>
    </div>
</div>
<div class="filter-root clearfix">
    <div class="row task-bottom-panel">
        <?= FilterPanel::widget(['blocks' => $panels[TaskFilterNavigation::PANEL_POSITION_COL1]]) ?>
        <?= FilterPanel::widget(['blocks' => $panels[TaskFilterNavigation::PANEL_POSITION_COL2]]) ?>
        <?= FilterPanel::widget(['blocks' => $panels[TaskFilterNavigation::PANEL_POSITION_COL3]]) ?>
        <?= FilterPanel::widget(['blocks' => $panels[TaskFilterNavigation::PANEL_POSITION_COL4]]) ?>
    </div>
    <div id="task-export-button">
        <?= ExportButton::widget(['filter' => 'TaskFilter']) ?>
    </div>
</div>
<?= Html::endTag('div') ?>