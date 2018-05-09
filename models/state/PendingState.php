<?php


namespace humhub\modules\tasks\models\state;


use humhub\modules\tasks\activities\TaskResetActivity;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\notifications\TaskResetNotification;
use Yii;

class PendingState extends TaskState
{
    public static $status = Task::STATUS_PENDING;
    public static $defaultProceedStatus = Task::STATUS_IN_PROGRESS;

    protected function proceedConfig($user = null)
    {
        return [
            Task::STATUS_IN_PROGRESS => [
                'label' => Yii::t('TasksModule.base', 'Begin Task'),
                'icon' => 'fa-play'
            ],
            Task::STATUS_PENDING_REVIEW => [
                'label' => Yii::t('TasksModule.base', 'Ready For Review'),
                'icon' => 'fa-eye'
            ],
            Task::STATUS_COMPLETED => [
                'label' =>  Yii::t('TasksModule.base', 'Finish Task'),
                'icon' => 'fa-check-square-o'
            ],
        ];
    }

    protected function revertConfig($user = null)
    {
        return [];
    }

    public function checkProceedRules($newStatus = null, $user = null)
    {
        return $this->task->isTaskResponsible($user) || $this->task->isTaskAssigned($user) || $this->task->canProcess($user);
    }

    public function afterRevert(TaskState $oldState)
    {
        if ($this->task->hasItems()) {
            $this->task->checklist->resetItems();
        }
        $this->notifyReset();
    }

    public function notifyReset()
    {
        $this->task->deleteOldNotifications();
        $user = Yii::$app->user->getIdentity();

        TaskResetNotification::instance()->from($user)->about($this->task)->sendBulk($this->task->users);
        TaskResetActivity::instance()->from($user)->about($this->task)->create();
    }

    public function checkRevertRules($newStatus = null, $user = null)
    {
        return false;
    }

    public function afterProceed(TaskState $oldState)
    {
        // We can't proceed to a pending state (only revert)
    }


}