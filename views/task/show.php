<?php

use yii\helpers\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\comment\models\Comment;

humhub\modules\tasks\Assets::register($this);
?>

<div class="panel panel-default">
    <div class="panel-body">


        <?php foreach ($tasks as $task) : ?>
            <div class="media task" id="task_<?php echo $task->id; ?>">

                <?php
                $currentUserAssigned = false;

                // Check if current user is assigned to this task
                foreach ($task->assignedUsers as $au) {
                    if ($au->id == Yii::$app->user->id) {
                        $currentUserAssigned = true;
                        break;
                    }
                }
                ?>


                <?php if ($task->status == Task::STATUS_OPEN) : ?>

                    <?php if ($currentUserAssigned || (count($task->$assignedUsers) < $task->max_users)) : ?>
                        <?php
                        echo \humhub\widgets\AjaxButton::widget([
                            'label' => '<div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="' . Yii::t("TasksModule.widgets_views_entry", "Click, to finish this task") . '"><i class="fa fa-square-o task-check"> </i></div>',
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
                    <?php else : ?>
                        <a id="TaskFinishLink_<?php echo $task->id; ?>" href="#">
                            <div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip"
                                 data-placement="top" data-original-title="Click, to finish this task"><i
                                    class="fa fa-square-o task-check"> </i></div>
                        </a>
                    <?php endif; ?>

                <?php elseif ($task->status == Task::STATUS_FINISHED) : ?>
                    <?php if ($currentUserAssigned || (count($assignedUsers) < $task->max_users)) : ?>
                        <?php
                        echo \humhub\widgets\AjaxButton::widget([
                            'label' => '<div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="' . Yii::t("TasksModule.widgets_views_entry", "This task is already done. Click to reopen.") . '"><i class="fa fa-check-square-o task-check"> </i></div>',
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
                        <div class="tasks-check disabled tt pull-left" style="margin-right: 0;" data-toggle="tooltip"
                             data-placement="top"
                             data-original-title="<?php echo Yii::t("TasksModule.widgets_views_entry", "This task is already done"); ?>">
                            <i
                                class="fa fa-check-square-o task-check"> </i></div>
                    <?php endif; ?>
                <?php endif; ?>




                <div class="media-body">
                    <span class="task-title pull-left"><?php echo $task->title; ?></span>


                    <div class="task-controls end pull-right">

                        <a href="<?php echo $contentContainer->createUrl('edit', ['id' => $task->id]); ?>"
                           class="tt"
                           data-target="#globalModal" data-toggle="tooltip"
                           data-placement="top" data-original-title="Edit Task"><i class="fa fa-pencil"></i></a>


                        <?php
                        echo humhub\widgets\ModalConfirm::widget(array(
                            'uniqueID' => 'modal_delete_task_' . $task->id,
                            'linkOutput' => 'a',
                            'title' => Yii::t('TasksModule.views_task_show', '<strong>Confirm</strong> deleting'),
                            'message' => Yii::t('TasksModule.views_task_show', 'Do you really want to delete this task?'),
                            'buttonTrue' => Yii::t('TasksModule.views_task_show', 'Delete'),
                            'buttonFalse' => Yii::t('TasksModule.views_task_show', 'Cancel'),
                            'linkContent' => '<i class="fa fa-times-circle-o colorDanger"></i>',
                            'linkHref' => $contentContainer->createUrl('delete', array('id' => $task->id)),
                            'confirmJS' => "$('#task_" . $task->id . "').fadeOut('fast')",
                        ));
                        ?>

                    </div>

                    <div class="task-controls pull-right" style="display: inline;">
                        <!-- Show assigned user -->
                        <?php foreach ($task->assignedUsers as $user): ?>
                            <a href="<?php echo $user->getUrl(); ?>" id="user_<?php echo $task->id; ?>">
                                <img src="<?php echo $user->getProfileImage()->getUrl(); ?>" class="img-rounded tt"
                                     height="24" width="24" alt="24x24"
                                     style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                                     title=""
                                     data-original-title="<strong><?php echo Html::encode($user->displayName); ?></strong><br><?php echo Html::encode($user->profile->title); ?>">
                            </a>

                        <?php endforeach; ?>
                    </div>

                    <?php if ($task->hasDeadline()) : ?>
                        <?php
                        $timestamp = strtotime($task->deadline);
                        ?>
                        <div class="task-controls pull-right">
                            <span
                                class="<?php if (date("d.m.yy", $timestamp) <= date("d.m.yy", time())) : ?>colorDanger<?php endif; ?>"><i
                                    class="fa fa-calendar"></i> <?php echo date("d. M", $timestamp); ?></span>
                        </div>
                    <?php endif; ?>



                    <div class="task-controls pull-right">

                        <a data-toggle="collapse"
                           href="#task-comment-<?php echo $task->id; ?>"
                           onclick="$('#comment_humhubmodulestasksmodelsTask_<?php echo $task->id; ?>').show();return false;"
                           aria-expanded="false"
                           aria-controls="collapseTaskComments"><i
                                class="fa fa-comment-o"></i> <?php echo $count = Comment::GetCommentCount($task->className(), $task->id); ?>
                        </a>

                    </div>
                    <div class="clearfix"></div>

                </div>

                <div class="wall-entry collapse" id="task-comment-<?php echo $task->id; ?>">
                    <div class="wall-entry-controls">
                        <?php //echo \humhub\modules\comment\widgets\CommentLink::widget(array('object' => $task)); ?>
                    </div>
                    <?php echo \humhub\modules\comment\widgets\Comments::widget(array('object' => $task)); ?>
                </div>

                <script type="text/javascript">
                    $('#task-comment-<?php echo $task->id; ?>').on('shown.bs.collapse', function () {
                        $('#newCommentForm_humhubmodulestasksmodelsTask_<?php echo $task->id; ?>_contenteditable').focus();
                    })
                </script>


            </div>
        <?php endforeach; ?>

        <br>
        <a href="<?php echo $contentContainer->createUrl('edit'); ?>" class="btn btn-primary"
           data-target="#globalModal"><i
                class="fa fa-plus"></i> <?php echo Yii::t('TasksModule.views_task_show', 'Add Task'); ?></a>
    </div>
</div>

<script type="text/javascript">

    var _id = <?php echo (int) Yii::$app->request->get('id'); ?>;

    //$( document ).ready(function() {
    if (_id > 0) {
        // $('#task-comment-<?php echo $task->id; ?>').collapse('show');
        $('#task_' + _id).addClass('highlight');
        $('#task_' + _id).animate({
            backgroundColor: "#fff"
        }, 2000);
    }
    //});
</script>




