<div id="tasksList" class="myTasksSnippet">
    <?php foreach ($tasks as $task): ?>
        <?= \humhub\modules\tasks\widgets\Task::widget(['model' => $task, 'showCommentsColumn' => false]); ?>
    <?php endforeach; ?>

</div>
<br />
<div class="pull-right"><a href="<?= $showAllUrl ?>"><?= Yii::t('TasksModule.base', 'Show all'); ?></a></div>