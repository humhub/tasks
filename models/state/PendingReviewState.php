<?php


namespace humhub\modules\tasks\models\state;


use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\notifications\PendingReviewNotification;
use Yii;

class PendingReviewState extends TaskState
{
    public static $status = Task::STATUS_PENDING_REVIEW;
    public static $defaultProceedStatus = Task::STATUS_COMPLETED;
    public static $defaultRevertStatus = Task::STATUS_IN_PROGRESS;

    protected function proceedConfig($user = null)
    {
        return [
            Task::STATUS_COMPLETED => [
                'label' => Yii::t('TasksModule.base', 'Accept Task'),
                'icon' => 'fa-check-square-o'
            ]
        ];
    }

    protected function revertConfig($user = null)
    {
        return [
            Task::STATUS_IN_PROGRESS => [
                'label' => Yii::t('TasksModule.base', 'Reject Task'),
                'icon' => 'fa-times-circle-o'
            ]
        ];
    }

    public function checkProceedRules($newStatus = null, $user = null)
    {
        return $this->task->isTaskResponsible($user);
    }

    public function checkRevertRules($newStatus = null, $user = null)
    {
        return $this->task->isTaskResponsible($user);
    }

    public function afterRevert(TaskState $oldState)
    {
        $this->notifyPendingReview();
    }

    public function afterProceed(TaskState $oldState)
    {
        $this->notifyPendingReview();
        $this->task->checklist->checkAll();
        $this->task->updateAttributes(['request_sent' => 0]);
    }

    /**
     * Notify users about status change
     * @throws \yii\base\InvalidConfigException
     */
    public function notifyPendingReview()
    {
        if ($this->task->review && $this->task->hasTaskResponsible()) {
            // remove old notifications
            $this->task->deleteOldNotifications(PendingReviewNotification::class);
            PendingReviewNotification::instance()->from(Yii::$app->user->getIdentity())->about($this->task)->sendBulk($this->task->taskResponsibleUsers);
        }
    }
}