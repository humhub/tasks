<?php

use yii\helpers\Html;
use humhub\modules\tasks\models\Task;

humhub\modules\tasks\Assets::register($this);
?>




<div class="media task task-entry" id="task_<?php echo $task->id; ?>">
    <?php if ($task->status == Task::STATUS_OPEN) : ?>
        <a id="TaskFinishLink_<?php echo $task->id; ?>" href="<?php echo $task->getUrl(); ?><?php if ($task->status == Task::STATUS_FINISHED): ?>&completed=true<?php endif; ?>">
            <div class="tasks-check tt pull-left" style="margin-right: 0;"><i class="fa fa-square-o task-check"> </i>
            </div>
        </a>
    <?php elseif ($task->status == Task::STATUS_FINISHED) : ?>
        <a id="TaskFinishLink_<?php echo $task->id; ?>" href="<?php echo $task->getUrl(); ?><?php if ($task->status == Task::STATUS_FINISHED): ?>&completed=true<?php endif; ?>">
            <div class="tasks-check tt pull-left" style="margin-right: 0;"><i
                    class="fa fa-check-square-o task-check"> </i></div>
        </a>
    <?php endif; ?>






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
            <span class="<?php echo $class; ?> <?php if ($task->status == Task::STATUS_FINISHED): ?>task-completed-controls<?php endif; ?>"><?php echo date("d. M", $timestamp); ?></span>
        <?php endif; ?>


        <div class="pull-right <?php if ($task->status == Task::STATUS_FINISHED): ?>task-completed-controls<?php endif; ?>" style="display: inline; margin-right: 8px;">
            <!-- Show assigned user -->
            <?php foreach ($assignedUsers as $user): ?>
                <a href="<?php echo $user->getUrl(); ?>" id="user_<?php echo $task->id; ?>">
                    <img src="<?php echo $user->getProfileImage()->getUrl(); ?>" class="img-rounded tt"
                         height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                         style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                         title=""
                         data-original-title="<?php echo Html::encode($user->displayName); ?>">
                </a>

            <?php endforeach; ?>

        </div>

        <div class="clearfix"></div>

    </div>
</div>