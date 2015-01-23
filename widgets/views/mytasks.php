<?php if (count($tasks) > 0) : ?>
    <div class="panel panel-default panel-mytasks" style="display:none;">
        <div
            class="panel-heading"><?php echo Yii::t('TasksModule.widgets_views_mytasks', '<strong>My</strong> tasks'); ?></div>
        <div class="panel-body">
            <?php foreach ($tasks as $task): ?>

                <div class="media task" id="task_<?php echo $task->id; ?>">
                    <?php

                    echo HHtml::ajaxLink(
                        '<div class="tasks-check tt pull-left" data-toggle="tooltip" data-placement="top" data-original-title="' . Yii::t("TasksModule.widgets_views_entry", "Click, to finish this task") . '"><i class="fa fa-square-o"> </i></div>', CHtml::normalizeUrl(array('/tasks/task/changeStatus', 'taskId' => $task->id, 'status' => Task::STATUS_FINISHED)), array(
                            'dataType' => "json",
                            'success' => "function(json) {  $('#wallEntry_'+json.wallEntryId).html(parseHtml(json.output)); $('#task_" . $task->id . " .task-title').addClass('task-completed'); $('#task_" . $task->id . " .label').css('opacity', '0.3'); $('#task_" . $task->id . " .tasks-check .fa').removeClass('fa-square-o'); $('#task_" . $task->id . " .tasks-check .fa').addClass('fa-check-square-o');}",
                        ), array('id' => "TaskFinishLink_" . $task->id)
                    );
                    ?>
                    <div class="media-body">
                        <span class="task-title pull-left"><?php echo CHtml::encode($task->title); ?></span>
                        <small >
                            <!-- Show deadline -->

                            <?php if ($task->deadline != '0000-00-00 00:00:00') : ?>
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
                            <!-- Show space  -->
                            <a href="<?php echo $task->content->container->getUrl(); ?>">
                                <img src="<?php echo $task->content->container->getProfileImage()->getUrl(); ?>"
                                     class="img-rounded tt"
                                     height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                     style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                                     title=""
                                     data-original-title="<?php echo Yii::t('TasksModule.widgets_views_mytasks', 'From space: ')?><br><strong><?php echo CHtml::encode($task->content->container->name); ?></strong>">
                            </a>

                        </div>

                        <div class="clearfix"></div>

                    </div>
                </div>


            <?php endforeach; ?>

        </div>
    </div>
<?php endif; ?>
