<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\libs\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\TaskBadge;
use humhub\modules\tasks\widgets\TaskContextMenu;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $task Task */

$participantStyle = 'display:inline-block;';
?>
<div class="panel-heading clearfix">
    <div class="pull-right">
        <?= TaskContextMenu::widget(['task' => $task]) ?>
    </div>

    <div class="task-head">
        <div class="task-list-item-title">
            <strong><?= Icon::get('fa-tasks')->color($task->getColor('var(--info)'))?> <?= Html::encode($task->title) ?></strong>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-12 media">
            <div class="media-body clearfix">
                <span class="small">
                    <?= Html::containerLink($task->content->createdBy) ?>

                    <?php if ($task->content->updated_at !== null) : ?>
                        &middot;
                        <span class="tt"
                            title="<?= Html::encode(Yii::t('ContentModule.base', 'Updated') . ': '
                                . Yii::$app->formatter->asDateTime($task->content->updated_at)) ?>">
                            <?= Yii::$app->formatter->asDate($task->content->updated_at, 'medium') ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($task->scheduling || !$task->content->isPublic()) : ?>
                        &middot;
                        <span class="text-muted">
                        <?php if ($task->scheduling) : ?>
                            <?= Icon::get('clock-o')->tooltip($task->schedule->getFormattedStartDateTime()
                                . ' - ' . $task->schedule->getFormattedEndDateTime()) ?>
                        <?php endif; ?>

                        <?php if (!$task->content->isPublic()) : ?>
                            <?= Icon::get('lock')->tooltip(Yii::t('SpaceModule.base', 'Private')) ?>
                        <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </span>

                <div class="pull-right">
                    <?= TaskBadge::widget(['task' => $task]) ?>

                    <?php if ($task->content->isPublic()) : ?>
                        <span class="label label-info"><?= Yii::t('SpaceModule.base', 'Public') ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
