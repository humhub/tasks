<div class="container space-layout-container">
    <div class="row">
        <div class="col-md-12">
            <?php echo humhub\modules\space\widgets\Header::widget(['space' => $this->context->contentContainer]); ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-2 layout-nav-container">
            <?php echo \humhub\modules\space\widgets\Menu::widget(['space' => $this->context->contentContainer]); ?>
            <br>
        </div>

        <div class="col-md-7 layout-content-container">
            <!--
            <p><small>
                    <strong>View mode: </strong> <a href="#" style="text-decoration:underline">List</a> | Gantt | Workload<br />
                    <strong>Order: </strong> <a href="#" style="text-decoration:underline">Time</a> | Status | User<br />
                </small></p>
            -->
            <?php echo $content; ?>
        </div>
        <div class="col-md-3 layout-sidebar-container">
            <!--
                        <div class="panel panel-default">
                            <div class="panel-body">
            <?php
            $canCreateNewTasks = $this->context->contentContainer->permissionManager->can(new \humhub\modules\tasks\permissions\CreateTask());
            ?>
            <?php if ($canCreateNewTasks): ?>
                                        <a href="<?php echo $this->context->contentContainer->createUrl('edit'); ?>" class="btn btn-success pull-right"
                                           data-target="#globalModal"><i
                                                class="fa fa-plus"></i> <?php echo Yii::t('TasksModule.views_task_show', 'Add Task'); ?></a>
            <?php endif; ?>                    
                            </div>
                        </div>
            -->
            <?= \humhub\modules\tasks\widgets\FilterSnippet::widget(); ?>
        </div>
    </div>
