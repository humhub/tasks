<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\libs\Html;

/* @var $task \humhub\modules\tasks\models\Task */
/* @var $url string */
/* @var $canEdit boolean */
/* @var $filterResult boolean */

$color = $task->getColor() ? $task->getColor() : $this->theme->variable('info');
?>

<a href="<?= $url ?>">
    <div class="media task">
        <div class="task-head" style="padding-left:10px; border-left: 3px solid <?= $color ?>">
        <div class="media-body clearfix">
            <?= \humhub\modules\tasks\widgets\TaskBadge::widget(['task' => $task, 'right' => true])?>

            <h4 class="media-heading">
                <b><?= Html::encode($task->title); ?></b>
            </h4>

            <h5>
                <?= $task->schedule->getFormattedDateTime() ?>
                <?= \humhub\widgets\Button::primary()
                ->options(['class' => 'tt', 'title' => Yii::t('TasksModule.views_index_index', 'Edit'), 'style' => 'margin-left:2px']
                )->icon('fa-pencil')->right()->xs()->action('ui.modal.load', $editUrl)->loader(false)->visible($canEdit) ?>
            </h5>
                <?= \humhub\modules\tasks\widgets\TaskPercentageBar::widget(['task' => $task, 'filterResult' => $filterResult])?>
        </div>
        </div>
    </div>
</a>