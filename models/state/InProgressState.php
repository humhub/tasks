<?php


namespace humhub\modules\tasks\models\state;


use humhub\modules\tasks\activities\TaskStartActivity;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\notifications\InProgressNotification;
use humhub\modules\tasks\notifications\ReviewRejectedNotification;
use Yii;

class InProgressState extends TaskState
{
    public static $status = Task::STATUS_IN_PROGRESS;
    public static $defaultRevertStatus = Task::STATUS_PENDING;

    protected function getDefaultProceedStatusId()
    {
        return $this->task->review ? Task::STATUS_PENDING_REVIEW : Task::STATUS_COMPLETED;
    }

    protected function proceedConfig($user = null)
    {
        return [
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
        return [
            Task::STATUS_PENDING => ['label' => Yii::t('TasksModule.base', 'Reset Task'), 'icon' => 'fa-undo']
        ];
    }

    public function checkProceedRules($newStatus = null, $user = null)
    {
        return $this->task->isTaskResponsible($user) || $this->task->isTaskAssigned($user) || $this->task->canProcess($user);
    }

    public function checkRevertRules($newStatus = null, $user = null)
    {
        return $this->task->isTaskResponsible($user) || $this->task->isTaskAssigned($user) || $this->task->canProcess($user);
    }

    public function afterProceed(TaskState $oldState)
    {
        $this->notifyInProgress();
    }

    public function notifyInProgress()
    {
        $user = Yii::$app->user->getIdentity();

        if ($this->task->hasTaskResponsible()) {
            InProgressNotification::instance()->from($user)->about($this->task)->sendBulk($this->task->taskResponsibleUsers);
        }

        TaskStartActivity::instance()->from($user)->about($this->task)->create();
    }

    public function afterRevert(TaskState $oldState)
    {
        $this->notifyRejectedReview();
    }

    public function notifyRejectedReview()
    {
        if ($this->task->review && $this->task->hasTaskAssigned()) {
            $this->task->deleteOldNotifications(ReviewRejectedNotification::class);
            ReviewRejectedNotification::instance()->from(Yii::$app->user->getIdentity())->about($this->task)->sendBulk($this->task->filterResponsibleAssigned());
        } else {
            $this->notifyInProgress();
        }
    }
}