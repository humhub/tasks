<?php

use yii\helpers\Html;
use humhub\modules\tasks\models\Task;

humhub\modules\tasks\Assets::register($this);

$this->registerJsVar('tasksStatusUpdateUrl', $task->content->container->createUrl('/tasks/task/change-status'));

?>
<?= \humhub\modules\tasks\widgets\Task::widget(['model' => $task, 'showCommentsColumn' => false]); ?>