<?php

use yii\helpers\Json;
use humhub\modules\tasks\Assets;

Assets::register($this);

$this->registerJsVar('tasksStatusUpdateUrl', $tasks[0]->content->container->createUrl('/tasks/task/change-status'));
?>

<div class="panel panel-default panel-mytasks" style="display:none;">
    <div
        class="panel-heading"><?php echo Yii::t('TasksModule.widgets_views_mytasks', '<strong>My</strong> tasks'); ?></div>
    <div class="panel-body">
        
        <div id="tasksList" class="myTasksSnippet">
            <?php foreach ($tasks as $task): ?>
                <?= \humhub\modules\tasks\widgets\Task::widget(['model' => $task, 'showCommentsColumn' => false]); ?>
            <?php endforeach; ?>

        </div>
        <br />
        <div class="pull-right"><a href="<?= $showAllUrl ?>">Show all</a></div>
    </div>
</div>

<script>
    $('#tasksList').data('filters', <?= Json::encode($filters); ?>);
</script>