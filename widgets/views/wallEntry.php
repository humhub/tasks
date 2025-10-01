<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\components\View;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\widgets\modal\ModalButton;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\TaskPercentageBar;

/* @var $this View */
/* @var $task Task */

$color = 'var(--text-color-main)';
?>
<div class="wall-entry-task task">

    <?php if($task->getItems()->count()) : ?>
       <?= TaskPercentageBar::widget(['task' => $task, 'filterResult' => false]) ?>
    <?php endif; ?>

    <?php if(!empty($task->description)) : ?>
        <div data-ui-markdown data-ui-show-more style="margin-bottom:10px">
            <?= RichText::output($task->description) ?>
        </div>
    <?php endif; ?>

    <?= $this->render('@tasks/widgets/views/taskInfos', ['task' => $task]) ?>

    <br>

    <?php if ($task->canView()) : ?>
        <?= ModalButton::primary(Yii::t('TasksModule.base', 'Open Task'))->icon('eye')->close()->link($task->getUrl())->sm() ?>
    <?php endif; ?>

</div>
