<?php


namespace humhub\modules\tasks\widgets;


use humhub\modules\tasks\models\Task;
use humhub\modules\ui\icon\widgets\Icon;
use Yii;

class TaskRoleInfoBox extends TaskInfoBox
{
    /**
     * @var Task
     */
    public $task;

    public $icon = 'fa-users';

    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Assignments:');
    }

    public function getValue()
    {
        if ($this->task->hasTaskAssigned()) {
            return TaskUserList::widget(['users' => $this->task->taskAssignedUsers]);
        }

        $color = Yii::$app->view->theme->variable('text-color-main');

        if ($this->task->isTaskResponsible()) {
            return Icon::get('check')->color($color).' '.Yii::t('TasksModule.base', 'You are responsible!');
        } else if($this->task->isTaskAssigned()) {
            return Icon::get('check')->color($color).' '.Yii::t('TasksModule.base', 'You are assigned!');
        } else if($this->task->canProcess()) {
            return  Icon::get('times')->color($color).' '.Yii::t('TasksModule.base', 'Anyone can work on this task!');
        } else {
            return  Icon::get('times')->color($color).' '.Yii::t('TasksModule.base', 'This task can only be processed by assigned and responsible users.');
        }
    }
}
