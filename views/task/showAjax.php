<?php foreach ($tasks as $task) : ?>
    <?= humhub\modules\tasks\widgets\Task::widget(['model' => $task]); ?>
<?php endforeach; ?>

<?= \humhub\modules\tasks\widgets\MoreButton::widget(['pagination' => $pagination]); ?>
