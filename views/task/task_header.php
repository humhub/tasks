<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\TaskBadge;
use humhub\modules\tasks\widgets\TaskContextMenu;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Badge;

/* @var $task Task */

$participantStyle = 'display:inline-block;';
?>

<div class="panel-heading container">
    <div class="task-head">
        <div class="float-end">
            <?= TaskContextMenu::widget(['task' => $task]) ?>
        </div>
        <div class="task-list-item-title">
            <strong><?= Icon::get('tasks')->color($task->getColor('var(--accent)'))?> <?= Html::encode($task->title) ?></strong>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="clearfix">
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

                <div class="float-end">
                    <?= TaskBadge::widget(['task' => $task]) ?>

                    <?php if ($task->content->isPublic()) : ?>
                        <?= Badge::accent(Yii::t('SpaceModule.base', 'Public')) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
