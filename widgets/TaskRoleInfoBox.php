<?php


namespace humhub\modules\tasks\widgets;


use humhub\modules\tasks\models\Task;
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
             return '<i class="fa fa-check"></i> '.Yii::t('TasksModule.base', 'You are responsible!');
         } else if($this->task->isTaskAssigned()) {
             return '<i class="fa fa-check"></i> '.Yii::t('TasksModule.base', 'You are assigned!');
         } else if($this->task->canProcess()) {
            return  '<i class="fa fa-times"></i> '.Yii::t('TasksModule.widgets_views_wallentry', 'Anyone can work on this task!');
         } else {
             return  '<i class="fa fa-times"></i> '.Yii::t('TasksModule.widgets_views_wallentry', 'This task can only be processed by assigned and responsible users.');
         }
    }


}