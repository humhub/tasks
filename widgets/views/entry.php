<?php

use yii\helpers\Html;
use humhub\modules\tasks\models\Task;

humhub\modules\tasks\Assets::register($this);
?>

<?php $this->beginContent('@humhub/modules/content/views/layouts/wallLayout.php', array('object' => $task)); ?>

<div class="media task" id="task_<?php echo $task->id; ?>">
    <?php if (Yii::$app->user->isGuest): ?>

    <?php else: ?>
        <?php if ($task->status == Task::STATUS_OPEN) : ?>
            <?php if ($currentUserAssigned || (count($assignedUsers) < $task->max_users)) : ?>
                <?php
                echo \humhub\widgets\AjaxButton::widget([
                    'label' => '<div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="' . Yii::t("TasksModule.widgets_views_entry", "Click, to finish this task") . '"><i class="fa fa-square-o"> </i></div>',
                    'tag' => 'a',
                    'ajaxOptions' => [
                        'dataType' => "json",
                        'success' => "function(json) {  $('#wallEntry_'+json.wallEntryId).html(parseHtml(json.output)); $('#task_" . $task->id . " .task-title').addClass('task-completed'); $('#task_" . $task->id . " .label').css('opacity', '0.3'); $('#task_" . $task->id . " .tasks-check .fa').removeClass('fa-square-o'); $('#task_" . $task->id . " .tasks-check .fa').addClass('fa-check-square-o'); $('.panel-mytasks #task_" . $task->id . "').delay(500).fadeOut('slow');}",
                        'url' => $contentContainer->createUrl('/tasks/task/change-status', array('taskId' => $task->id, 'status' => Task::STATUS_FINISHED)),
                    ],
                    'htmlOptions' => [
                        'id' => "TaskFinishLink_" . $task->id
                    ]
                ]);
                ?>
            <?php else: ?>
                <div class="tasks-check disabled tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top"
                     data-original-title="<?php echo Yii::t("TasksModule.widgets_views_entry", "You're not assigned to this task"); ?>">
                    <i
                        class="fa fa-square-o"> </i></div>
                <?php endif; ?>
            <?php elseif ($task->status == Task::STATUS_FINISHED) : ?>
                <?php if ($currentUserAssigned || (count($assignedUsers) < $task->max_users)) : ?>
                    <?php
                    echo \humhub\widgets\AjaxButton::widget([
                        'label' => '<div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="' . Yii::t("TasksModule.widgets_views_entry", "This task is already done. Click to reopen.") . '"><i class="fa fa-check-square-o"> </i></div>',
                        'tag' => 'a',
                        'ajaxOptions' => [
                            'dataType' => "json",
                            'success' => "function(json) {  $('#wallEntry_'+json.wallEntryId).html(parseHtml(json.output));}",
                            'url' => $contentContainer->createUrl('/tasks/task/change-status', array('taskId' => $task->id, 'status' => Task::STATUS_OPEN)),
                        ],
                        'htmlOptions' => [
                            'id' => "TaskOpenLink_" . $task->id
                        ]
                    ]);
                    ?>
                <?php else: ?>
                <div class="tasks-check disabled tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top"
                     data-original-title="<?php echo Yii::t("TasksModule.widgets_views_entry", "This task is already done"); ?>">
                    <i
                        class="fa fa-check-square-o"> </i></div>
                <?php endif; ?>

        <?php endif; ?>
    <?php endif; ?>
    <div class="media-body">
        <span class="task-title <?php if ($task->status == Task::STATUS_FINISHED): ?>task-completed<?php endif; ?>pull-left"><?php echo Html::encode($task->title); ?></span>
        <small>
            <!-- Show deadline -->

            <?php if ($task->hasDeadline()) : ?>
                <?php
                $timestamp = strtotime($task->deadline);
                $class = "label label-default";

                if (date("d.m.yy", $timestamp) <= date("d.m.yy", time())) {
                    $class = "label label-danger";
                }
                ?>
                <span class="<?php echo $class; ?>"
                      style="<?php if ($task->status == Task::STATUS_FINISHED): ?>opacity: 0.3;<?php endif; ?>"><?php echo date("d. M", $timestamp); ?></span>
                  <?php endif; ?>

        </small>

        <div class="user pull-right" style="display: inline;">
            <!-- Show assigned user -->
            <?php foreach ($assignedUsers as $user): ?>
                <a href="<?php echo $user->getUrl(); ?>" id="user_<?php echo $task->id; ?>">
                    <img src="<?php echo $user->getProfileImage()->getUrl(); ?>" class="img-rounded tt"
                         height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                         style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                         title=""
                         data-original-title="<strong><?php echo Html::encode($user->displayName); ?></strong><br><?php echo Html::encode($user->profile->title); ?>">
                </a>

            <?php endforeach; ?>

        </div>
        <div class="clearfix"></div>

    </div>
</div>

<?php $this->endContent(); ?>