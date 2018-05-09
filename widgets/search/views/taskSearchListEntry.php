<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\libs\Html;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\widgets\TaskBadge;
use humhub\modules\tasks\widgets\TaskPercentageBar;
use humhub\widgets\Button;

/* @var $task \humhub\modules\tasks\models\Task */
/* @var $canEdit boolean */
/* @var $filterResult boolean */

$color = $task->getColor() ? $task->getColor() : $this->theme->variable('info');
?>

<a href="<?= TaskUrl::viewTask($task) ?>">
    <div class="media task">
        <div class="task-head" style="padding-left:10px; border-left: 3px solid <?= $color ?>">
        <div class="media-body clearfix">
            <?= TaskBadge::widget(['task' => $task, 'right' => true])?>

            <h4 class="media-heading">
                <b><?= Html::encode($task->title); ?></b>
            </h4>

            <h5>
                <?= $task->schedule->getFormattedDateTime() ?>
            </h5>
                <?= TaskPercentageBar::widget(['task' => $task, 'filterResult' => $filterResult])?>
        </div>
        </div>
    </div>
</a>