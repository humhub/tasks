<?php
humhub\modules\tasks\Assets::register($this);
$this->registerJsVar('tasksStatusUpdateUrl', $this->context->contentContainer->createUrl('/tasks/task/change-status'));
$this->registerJsVar('tasksReloadUrl', $this->context->contentContainer->createUrl('/tasks/task/show'));
?>
<div class="row">
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <div>
                    <?php if ($canCreateNewTasks): ?>
                        <a href="<?php echo $this->context->contentContainer->createUrl('edit'); ?>" class="btn btn-success pull-right"
                           data-target="#globalModal"><i
                                class="fa fa-plus"></i> <?= Yii::t('TasksModule.base', 'Add Task'); ?></a>
                        <?php endif; ?>  
                </div>
                <br />
                <br />
                <br />

                <?= \humhub\widgets\LoaderWidget::widget(['id' => 'tasksLoader', 'cssClass' => 'hidden']); ?>

                <div id="noTasksFoundMessage" class="hidden">
                    <center>
                        <h2><i class="fa fa-check-square"></i></h2>
                        <p><?= Yii::t('TasksModule.base', 'No matches with your selected filters!'); ?></p>
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
    </div>

    <div class="col-md-3">          
        <?= \humhub\modules\tasks\widgets\FilterSnippet::widget(); ?>
    </div>
</div>