<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $task \humhub\modules\tasks\models\Task */

use humhub\libs\Html;
use humhub\modules\tasks\widgets\TaskRoleInfoBox;
use humhub\widgets\ModalButton;
use humhub\modules\tasks\widgets\TaskPercentageBar;

$color = $task->getColor() ? $task->getColor() : $this->theme->variable('info');
?>
<div class="media task">
    <div class="task-head" style="padding-left:10px; border-left: 3px solid <?= $color ?>">
        <div class="media-body clearfix">
            <a href="<?= $task->getUrl(); ?>" class="pull-left" style="margin-right: 10px">
                <i class="fa fa-tasks meeting-wall-icon colorDefault" style="font-size: 38px;"></i>
            </a>

            <h4 class="media-heading">
                <a href="<?= $task->getUrl(); ?>">
                    <b><?= Html::encode($task->title); ?></b>
                </a>
            </h4>
            <h5>
                <?= $task->schedule->getFormattedDateTime() ?>
            </h5>
            <?= TaskPercentageBar::widget(['task' => $task, 'filterResult' => false]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-top: 10px;">
            <?= TaskRoleInfoBox::widget(['task' => $task]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-top: 10px;">
            <?= ModalButton::primary(Yii::t('TasksModule.widgets_views_wallentry', 'Open Task'))->icon('fa-eye')->close()->link($task->getUrl())->sm() ?>
        </div>
    </div>

</div>


