<?php

use yii\helpers\Html;
use humhub\modules\comment\models\Comment;
use humhub\modules\tasks\models\Task;

humhub\modules\tasks\Assets::register($this);
$this->registerJsVar('tasksStatusUpdateUrl', $task->content->container->createUrl('/tasks/task/change-status'));
?>

<div class="media task" 
     id="task_<?php echo $task->id; ?>" data-task-id="<?= $task->id; ?>" 
     data-task-status='<?= $statusFlags[$task->status]; ?>' 
     data-task-status-id='<?= $task->status; ?>' 
     data-task-title='<?= Html::encode($task->title); ?>' 
     data-task-start-date='<?= $task->start_date; ?>'>

    <div class="open-check task-status-check">
        <div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t("TasksModule.base", "Click, to finish this task"); ?>"><i class="fa fa-square-o task-check"> </i></div>
    </div>

    <div class="completed-check task-status-check">
        <div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t("TasksModule.base", "This task is already done. Click to reopen.") ?>"><i class="fa fa-check-square-o task-check"> </i></div>
    </div>

    <div class="media-body">
        <span class="task-title pull-left"><?php echo $task->title; ?></span>

        <?php if ($task->hasDeadline()) : ?>
            <?php $labelClass = ($task->isOverdue()) ? 'danger' : 'default'; ?>

            <?php if ($task->duration_days == 1): ?>
                <span class="label label-<?= $labelClass ?>"><?= Yii::$app->formatter->asDate($task->deadline, 'short'); ?></span>
            <?php else: ?>
                <span class="label label-<?= $labelClass ?>">
                    <?= Yii::$app->formatter->asDate($task->start_date, 'short'); ?> - 
                    <?= Yii::$app->formatter->asDate($task->deadline, 'short'); ?>
                </span>
            <?php endif; ?>
        <?php endif; ?>


        <div class="task-controls end edit-link pull-right">
            <a href="<?php echo $task->content->container->createUrl('/tasks/task/edit', ['id' => $task->id]); ?>"
               class="tt"
               data-target="#globalModal" data-toggle="tooltip"
               data-placement="top" data-original-title="<?= Yii::t('TasksModule.base', 'Edit Task'); ?>"><i class="fa fa-pencil"></i></a>
        </div>

        <div class="task-controls assigned-space pull-right hidden" style="display: inline;">
            <?=
            \humhub\modules\space\widgets\Image::widget([
                'space' => $task->content->container,
                'height' => 24,
                'width' => 24,
                'link' => true,
                'linkOptions' => [
                    'href' => $task->content->container->createUrl('/tasks/task/show')
                ],
            ]);
            ?>
        </div>                

        <?php if ($showCommentsColumn) : ?>
            <div class="task-controls comments-link pull-right">
                <a data-toggle="collapse"
                   href=".task[data-task-id=<?= $task->id; ?>] .comments"
                   onclick="$('#comment_<?php echo $task->getUniqueId(); ?>').show();return false;"
                   aria-expanded="false"
                   aria-controls="collapseTaskComments"><i
                        class="fa fa-comment-o"></i> <?php echo Comment::GetCommentCount($task->className(), $task->id); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="task-controls assigned-users pull-right">
            <!-- Show assigned user -->
            <?php foreach ($task->assignedUsers as $user): ?>
                <a href="<?php echo $user->getUrl(); ?>" id="user_<?php echo $task->id; ?>">
                    <img src="<?php echo $user->getProfileImage()->getUrl(); ?>" class="img-rounded tt"
                         height="24" width="24" alt="24x24"
                         style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                         title=""
                         data-original-title="<?php echo Html::encode($user->displayName); ?>">
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($task->status !== Task::STATUS_ACTIVE && $task->status !== Task::STATUS_COMPLETED): ?>
            <div class="pull-right task-controls"><span class='label label-default'><?php echo $statusTexts[$task->status]; ?></span></div>
        <?php endif; ?>

        <div class="clearfix"></div>
    </div>

    <?php if ($showCommentsColumn) : ?>
        <div class="collapse comments">
            <?php echo \humhub\modules\comment\widgets\Comments::widget(array('object' => $task)); ?>
        </div>
    <?php endif; ?>
</div>
