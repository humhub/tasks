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

$color = $task->getColor() ? $task->getColor() : $this->theme->variable('info');

?>
<div class="wall-entry-task task">

    <h1>
        <?= Icon::get('clock-o')->color($color) ?> <?= $task->schedule->getFormattedDateTime() ?>
    </h1>

    <?php if($task->getItems()->count()) : ?>
       <?= TaskPercentageBar::widget(['task' => $task, 'filterResult' => false]) ?>
    <?php endif; ?>

    <?php if(!empty($task->description)) : ?>
        <div data-ui-show-more style="margin-bottom:10px">
            <?= RichText::output($task->description) ?>
        </div>
    <?php endif; ?>

    <div class="crearfix">
        <?= TaskRoleInfoBox::widget(['task' => $task, 'iconColor' => $color]) ?>
    </div>

    <br>

    <?php if ($task->canView()) : ?>
        <?= ModalButton::primary(Yii::t('TasksModule.widgets_views_wallentry', 'Open Task'))->icon('fa-eye')->close()->link($task->getUrl())->sm() ?>
    <?php endif; ?>

</div>


