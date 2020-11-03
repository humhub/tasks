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
         if ( $this->task->isTaskResponsible()) {
             return Icon::get('check')->color($this->view->theme->variable('success')).' '.Yii::t('TasksModule.base', 'You are responsible!');
         } else if($this->task->isTaskAssigned()) {
             return Icon::get('check')->color($this->view->theme->variable('success')).' '.Yii::t('TasksModule.base', 'You are assigned!');
         } else if($this->task->canProcess()) {
            return  Icon::get('times')->color($this->view->theme->variable('danger')).' '.Yii::t('TasksModule.widgets_views_wallentry', 'Anyone can work on this task!');
         } else {
             return  Icon::get('times')->color($this->view->theme->variable('danger')).' '.Yii::t('TasksModule.widgets_views_wallentry', 'This task can only be processed by assigned and responsible users.');
         }
    }


}