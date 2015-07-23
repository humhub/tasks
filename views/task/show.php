<?php

use yii\helpers\Html;
use module\tasks\models\Task;

module\tasks\Assets::register($this);
?>

<?php



//echo \module\tasks\widgets\TaskCreateForm::widget([]);

?>

    <a href="<?php echo $contentContainer->createUrl('edit'); ?>" class="btn btn-primary" data-toggle="modal"
       data-target="#globalModal">New Task</a>
    <a class="btn btn-default" href="#"><i class="fa fa-clock-o"></i> Heute</a>
     <a class="btn btn-default" href="#"><i class="fa fa-user"></i> Nach User</a>
    <a class="btn btn-default" href="#"><i class="fa fa-warning"></i> Delegiert</a>
    <br><br>

<?php foreach ($tasks as $task) : ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="media task" id="task_27">
                <a id="TaskFinishLink_27" href="#">
                    <div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip"
                         data-placement="top" data-original-title="Click, to finish this task"><i
                            class="fa fa-square-o task-check"> </i></div>
                </a>

                <div class="media-body">
                    <span class="task-title pull-left"><?php echo $task->title; ?></span>
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
                        <a href="/humhub/index.php?r=user/profile&amp;uguid=665afdae-dd29-4c9d-8d2c-994694827643"
                           id="user_27">
                            <img
                                src="http://localhost/humhub/uploads/profile_image/665afdae-dd29-4c9d-8d2c-994694827643.jpg?cacheId=0"
                                class="img-rounded tt" height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top" title=""
                                data-original-title="<strong>Peter Steinbach</strong><br>Dipl.-Ing. Tragwerksplanung / Statik">
                        </a>

                        <a href="/humhub/index.php?r=user/profile&amp;uguid=adfc9908-eb94-458c-907e-cfc17cdeabec"
                           id="user_27">
                            <img
                                src="http://localhost/humhub/uploads/profile_image/adfc9908-eb94-458c-907e-cfc17cdeabec.jpg?cacheId=0"
                                class="img-rounded tt" height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top" title=""
                                data-original-title="<strong>Wolfgang Mauer</strong><br>HumHub Tester">
                        </a>


                    </div>

                    <div class="task-controls pull-right">

                        <a href="<?php echo $contentContainer->createUrl('edit', ['id' => $task->id]); ?>" data-toggle="modal"
                           data-target="#globalModal"><i class="fa fa-pencil"></i></a>

                        <a data-toggle="collapse"
                                                                        href="#task-comment-<?php echo $task->id; ?>"
                                                                        aria-expanded="false"
                                                                        aria-controls="collapseTaskComments"><i
                                class="fa fa-comment-o"></i></a>
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
                        // do something…
                    })
                </script>


            </div>
        </div>
    </div>

<?php endforeach; ?>