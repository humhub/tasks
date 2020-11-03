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
use humhub\modules\tasks\widgets\TaskUserList;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $task \humhub\modules\tasks\models\Task */
/* @var $canEdit boolean */
/* @var $collapse boolean */

$icon = 'fa-tasks';
$participantStyle = 'display:inline-block;';
$color = $task->getColor() ? $task->getColor() : $this->theme->variable('info');

?>
<div class="panel-heading clearfix">
    <div class="task-head">
        <div>
            <strong><?= Icon::get($icon)->color($color)?> <?= Html::encode($task->title) ?></strong>
        </div>
    </div>

    <?= TaskContextMenu::widget(['task' => $task, 'contentContainer' => $contentContainer]) ?>

    <div class="row clearfix">
        <div class="col-sm-12 media">
            <div class="media-body clearfix">
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
                    &middot <span class="tt updated"
                            title="<?= Yii::$app->formatter->asDateTime($task->content->updated_at) ?>">
                        <?= Yii::t('ContentModule.base', 'Updated') ?>
                    </span>
                <?php endif; ?>

                <div class="pull-right">
                    <?= TaskBadge::widget(['task' => $task]) ?>

                    <?php if ($task->content->isPublic()) : ?>
                        <span class="label label-info"><?= Yii::t('SpaceModule.base', 'Public') ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <div class="task-header-panel-container clearfix">
                <!--        Responsible Task User-->
                <?php if ($task->hasTaskResponsible()) : ?>
                    <div class="task-header-panel">
                        <div style="<?= $participantStyle ?>">
                            <em><strong><?= Yii::t('TasksModule.views_index_index', 'Responsible') ?>:</strong></em><br>
                            <?= TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'type' => Task::USER_RESPONSIBLE])?>
                        </div>
                    </div>
                <?php endif ?>

                <!--        Assigned Task User-->
                <?php if ($task->hasTaskAssigned()) : ?>
                    <div class="task-header-panel">
                        <div>
                            <em><strong><?= Yii::t('TasksModule.views_index_index', 'Assigned') ?>:</strong></em><br>
                            <?= TaskUserList::widget(['users' => $task->taskAssignedUsers])?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="task-header-panel">
                        <div style="<?= $participantStyle ?>">
                            <em><strong><?= Yii::t('TasksModule.views_index_index', 'Assigned') ?>:</strong></em><br>
                            <div class="assigned-anyone">
                                <?= Yii::t('TasksModule.views_index_index', 'Any user with a "Process unassigned tasks" permission can work on this task') ?>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

            </div>
        </div>
    </div>
</div>