<?php

namespace module\tasks\widgets;

use Yii;
use humhub\components\Widget;
use module\tasks\models\Task;

class MyTasks extends Widget
{

    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $tasks = Task::GetUsersOpenTasks();

        if (count($tasks) === 0) {
            return;
        }

        return $this->render('mytasks', array('tasks' => $tasks));
    }

}

?>