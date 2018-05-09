<?php


namespace humhub\modules\tasks\widgets;


use humhub\components\Widget;
use humhub\modules\tasks\models\forms\TaskForm;

class TaskEditModal extends Widget
{
    /**
     * @var TaskForm
     */
    public $taskForm;


    public function run()
    {
        return $this->render('@tasks/views/task/edit', ['taskForm' => $this->taskForm]);
    }

}