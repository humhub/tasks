<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $task \humhub\modules\tasks\models\Task */

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\tasks\widgets\TaskRoleInfoBox;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\ModalButton;
use humhub\modules\tasks\widgets\TaskPercentageBar;
use humhub\modules\tasks\widgets\checklist\TaskChecklist;

$color = Yii::$app->view->theme->variable('text-color-main');
$hasItems = $task->hasItems()

?>
<div class="wall-entry-task task">

    <h1>
        <?= Icon::get('clock-o')->color($color) ?> <?= $task->schedule->getFormattedDateTime() ?>
    </h1>

    <?php if($task->getItems()->count()) : ?>
       <?= TaskPercentageBar::widget(['task' => $task, 'filterResult' => false]) ?>
    <?php endif; ?>

    <?php if(!empty($task->description) || $hasItems) : ?>
        <div data-ui-markdown data-ui-show-more>
            <?php if(!empty($task->description)) : ?>
                <div style="margin-bottom:10px">
                    <?= RichText::output($task->description) ?>
                </div>
            <?php endif; ?>
            <?php if($hasItems) : ?>
                <div style="overflow-x:hidden">
                    <?= TaskChecklist::widget(['task' => $task]) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="crearfix" style="margin-top:10px">
        <?= TaskRoleInfoBox::widget(['task' => $task, 'iconColor' => $color]) ?>
    </div>

    <br>

    <?php if ($task->canView()) : ?>
        <?= ModalButton::primary(Yii::t('TasksModule.base', 'Open Task'))->icon('fa-eye')->close()->link($task->getUrl())->sm() ?>
    <?php endif; ?>

</div>
