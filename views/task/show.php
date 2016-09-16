<?php

use yii\helpers\Html;

humhub\modules\tasks\Assets::register($this);

$this->registerJsVar('tasksStatusUpdateUrl', $this->context->contentContainer->createUrl('/tasks/task/change-status'));
$this->registerJsVar('tasksReloadUrl', $this->context->contentContainer->createUrl('/tasks/task/show'));
        
?>

<div class="panel panel-default">
    <div class="panel-body">
        <div>
            <?php
            $canCreateNewTasks = $this->context->contentContainer->permissionManager->can(new \humhub\modules\tasks\permissions\CreateTask());
            ?>
            <?php if ($canCreateNewTasks): ?>
                <a href="<?php echo $this->context->contentContainer->createUrl('edit'); ?>" class="btn btn-success pull-right"
                   data-target="#globalModal"><i
                        class="fa fa-plus"></i> <?php echo Yii::t('TasksModule.views_task_show', 'Add Task'); ?></a>
                <?php endif; ?>  
        </div>
        <br />
        <br />
        <br />

        <?= \humhub\widgets\LoaderWidget::widget(['id' => 'tasksLoader', 'cssClass' => 'hidden']); ?>

        <div id="noTasksFoundMessage" class="hidden">
            <center>
                <h2><i class="fa fa-check-square"></i></h2>
                <p>No matches with your selected filters!</p>
            </center>
        </div>

        <div id="tasksList">
            <?php foreach ($tasks as $task) : ?>
                <?= humhub\modules\tasks\widgets\Task::widget(['model' => $task]); ?>
            <?php endforeach; ?>

            <?= \humhub\modules\tasks\widgets\MoreButton::widget(['pagination' => $pagination]); ?>
        </div>

    </div>
</div>