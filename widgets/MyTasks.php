<?php

namespace humhub\modules\tasks\widgets;

use Yii;
use humhub\components\Widget;
use humhub\modules\tasks\models\Task;

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