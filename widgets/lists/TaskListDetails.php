<?php


namespace humhub\modules\tasks\widgets\lists;


use humhub\components\Widget;
use humhub\modules\tasks\models\Task;

class TaskListDetails extends Widget
{
    /**
     * @var Task
     */
    public $task;

    public function run()
    {
        return $this->render('taskDetails', ['task' => $this->task]);
    }

}