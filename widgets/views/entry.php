<?php

use yii\helpers\Html;
use humhub\modules\tasks\models\Task;

humhub\modules\tasks\Assets::register($this);
?>




<div class="media task" id="task_<?php echo $task->id; ?>" style="margin-top: -8px !important;">
    <?php if ($task->status == Task::STATUS_OPEN) : ?>
    <a id="TaskFinishLink_<?php echo $task->id; ?>" href="<?php echo $task->getUrl(); ?>">
        <div class="tasks-check tt pull-left" style="margin-right: 0;"><i class="fa fa-square-o task-check"> </i></div>
    </a>
    <?php elseif ($task->status == Task::STATUS_FINISHED) : ?>
        <a id="TaskFinishLink_<?php echo $task->id; ?>" href="<?php echo $task->getUrl(); ?>">
            <div class="tasks-check tt pull-left" style="margin-right: 0;"><i class="fa fa-check-square-o task-check"> </i></div>
        </a>
    <?php endif; ?>






    <div class="media-body">
        <span class="task-title <?php if ($task->status == Task::STATUS_FINISHED): ?>task-completed<?php endif; ?> pull-left"> <a href="<?php echo $task->getUrl(); ?>" class="colorFont3"><?php echo Html::encode($task->title); ?></a></span>


        <div class="task-controls pull-right" style="display: inline;">
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

        <?php if ($task->hasDeadline()) : ?>
            <?php
            $timestamp = strtotime($task->deadline);
            ?>
            <div class="task-controls pull-right">
                <span class="<?php if (date("d.m.yy", $timestamp) <= date("d.m.yy", time())) : ?>colorDanger<?php endif; ?>"><i class="fa fa-calendar"></i> <?php echo date("d. M", $timestamp); ?></span>
            </div>
        <?php endif; ?>
        <div class="clearfix"></div>

    </div>
</div>