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

    public $isAssigned;
    public $isResponsible;
    public $canBeProcessed;

    public function init()
    {
        $this->isAssigned = $this->task->isTaskAssigned();
        $this->isResponsible = $this->task->isTaskResponsible();
        $this->canBeProcessed = $this->task->canAnyoneProcessTask();

        if(!$this->isAssigned && !$this->isResponsible && !$this->canBeProcessed) {
            $this->render = false;
        }

        parent::init();
    }

    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Assignments:');
    }

    public function getValue()
    {
         if ($this->isResponsible) {
             return '<i class="fa fa-check"></i>'.Yii::t('TasksModule.base', 'You are responsible!');
         } else if($this->isAssigned) {
             return '<i class="fa fa-check"></i>'.Yii::t('TasksModule.base', 'You are assigned!');
         } else if($this->canBeProcessed) {
            return  '<i class="fa fa-times"></i>'.Yii::t('TasksModule.widgets_views_wallentry', 'Anyone can work on this task!');
         }
    }


}