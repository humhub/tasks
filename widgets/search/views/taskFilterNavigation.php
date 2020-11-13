<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\admin\widgets\ExportButton;
use humhub\modules\tasks\widgets\search\TaskFilterNavigation;
use yii\helpers\Html;
use humhub\modules\ui\filter\widgets\FilterPanel;
use humhub\modules\tasks\widgets\search\TextFilterInput;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $panels [] */
/* @var $options [] */

$title = $panels[TaskFilterNavigation::PANEL_POSITION_TOP][0]['filters'];
$checkboxes = $panels[TaskFilterNavigation::PANEL_POSITION_BOTTOM_LEFT];
$picker = $panels[TaskFilterNavigation::PANEL_POSITION_BOTTOM_CENTER];
$datePicker = $panels[TaskFilterNavigation::PANEL_POSITION_BOTTOM_RIGHT];

$titleFilter = $title[0];
?>

<?= Html::beginTag('div', $options) ?>

    <div class="row">
        <div class="col-md-12">
            <?= TextFilterInput::widget($title[0]) ?>
        </div>

    </div>

<div class="filter-root clearfix" style="position:relative;padding-top:0;padding-bottom:0">
    <div class="row task-bottom-panel">
        <?= FilterPanel::widget(['blocks' => $checkboxes, 'span' => 3]) ?>
        <?= FilterPanel::widget(['blocks' => $datePicker, 'span' => 3]) ?>
        <?= FilterPanel::widget(['blocks' => $picker, 'span' => 3]) ?>
    </div>
  
    <div id="task-export-button">
        <?= ExportButton::widget(['filter' => 'TaskFilter']) ?>
    </div>

</div>



<?= Html::endTag('div') ?>