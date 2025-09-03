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
use humhub\modules\tasks\widgets\TaskUserList;
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
            <strong><?= Icon::get('tasks')->color($task->getColor('var(--info)'))?> <?= Html::encode($task->title) ?></strong>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="clearfix">
                <?php if ($task->scheduling) : ?>
                    <h2 style="margin:5px 0 0 0;">
                        <?= $task->schedule->getFormattedStartDateTime() ?>
                        -
                        <?= $task->schedule->getFormattedEndDateTime() ?>
                    </h2>
                <?php endif; ?>
                <span class="author">
                    <?= Html::containerLink($task->content->createdBy) ?>
                </span>
                <?php if ($task->content->updated_at !== null) : ?>
                    &middot
                    <?= Html::tag('span', Yii::t('ContentModule.base', 'Updated'), [
                        'class' => 'tt updated',
                        'title' => Yii::$app->formatter->asDateTime($task->content->updated_at),
                        'data-bs-toggle' => 'tooltip',
                    ]) ?>
                <?php endif; ?>

                <div class="float-end">
                    <?= TaskBadge::widget(['task' => $task]) ?>

                    <?php if ($task->content->isPublic()) : ?>
                        <?= Badge::accent(Yii::t('SpaceModule.base', 'Public')) ?>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <div class="task-header-panel-container">
                <!--        Responsible Task User-->
                <?php if ($task->hasTaskResponsible()) : ?>
                    <div class="task-header-panel">
                        <div style="<?= $participantStyle ?>">
                            <em><strong><?= Yii::t('TasksModule.base', 'Responsible') ?>:</strong></em><br>
                            <?= TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'type' => Task::USER_RESPONSIBLE])?>
                        </div>
                    </div>
                <?php endif ?>

                <!--        Assigned Task User-->
                <?php if ($task->hasTaskAssigned()) : ?>
                    <div class="task-header-panel">
                        <div>
                            <em><strong><?= Yii::t('TasksModule.base', 'Assigned') ?>:</strong></em><br>
                            <?= TaskUserList::widget(['users' => $task->taskAssignedUsers])?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="task-header-panel">
                        <div style="<?= $participantStyle ?>">
                            <em><strong><?= Yii::t('TasksModule.base', 'Assigned') ?>:</strong></em><br>
                            <div class="assigned-anyone">
                                <?= Yii::t('TasksModule.base', 'Any user with a "Process unassigned tasks" permission can work on this task') ?>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

            </div>
        </div>
    </div>
</div>
