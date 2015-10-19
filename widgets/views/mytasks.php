<?php

use yii\helpers\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\Assets;

Assets::register($this);
?>


<div class="panel panel-default panel-mytasks" style="display:none;">
    <div
        class="panel-heading"><?php echo Yii::t('TasksModule.widgets_views_mytasks', '<strong>My</strong> tasks'); ?></div>
    <div class="panel-body">
        <?php foreach ($tasks as $task): ?>

            <div class="media task" id="task_<?php echo $task->id; ?>">
                <a id="TaskFinishLink_<?php echo $task->id; ?>" href="<?php echo $task->getUrl(); ?><?php if ($task->status == Task::STATUS_FINISHED): ?>&completed=true<?php endif; ?>">
                    <div class="tasks-check tt pull-left" style="margin-right: 0;"><i class="fa fa-square-o task-check"> </i>
                    </div>
                </a>
                <div class="media-body">
                    <span
                        class="task-title <?php if ($task->status == Task::STATUS_FINISHED): ?>task-completed<?php endif; ?> pull-left"> <a
                            href="<?php echo $task->getUrl(); ?><?php if ($task->status == Task::STATUS_FINISHED): ?>&completed=true<?php endif; ?>"
                            class="colorFont3"><?php echo Html::encode($task->title); ?></a></span>


                        <?php if ($task->hasDeadline()) : ?>
                            <?php
                            $timestamp = strtotime($task->deadline);
                            $class = "label label-default";
                            if (date("d.m.yy", $timestamp) <= date("d.m.yy", time())) {
                                $class = "label label-danger";
                            }
                            ?>
                            <span class="<?php echo $class; ?>"><?php echo date("d. M", $timestamp); ?></span>
                        <?php endif; ?>


                    <div class="user pull-right" style="display: inline;">
                        <!-- Show space  -->
                        <a href="<?php echo $task->content->container->getUrl(); ?>">
                            <img src="<?php echo $task->content->container->getProfileImage()->getUrl(); ?>"
                                 class="img-rounded tt"
                                 height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                 style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                                 title=""
                                 data-original-title="<?php echo Yii::t('TasksModule.widgets_views_mytasks', 'From space: ') ?> <?php echo Html::encode($task->content->container->name); ?>">
                        </a>

                    </div>

                    <div class="clearfix"></div>

                </div>
            </div>


        <?php endforeach; ?>

    </div>
</div>
