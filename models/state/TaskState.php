<?php


namespace humhub\modules\tasks\models\state;


use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use Yii;
use yii\base\Component;


/**
 * Class TaskState
 * @todo change base class back to BaseObject after v1.3 stable
 * @package humhub\modules\tasks\models\state
 */
abstract class TaskState extends Component
{
    const STATES = [
        Task::STATUS_PENDING => PendingState::class,
        Task::STATUS_IN_PROGRESS => InProgressState::class,
        Task::STATUS_PENDING_REVIEW => PendingReviewState::class,
        Task::STATUS_COMPLETED => CompletedState::class
    ];

    public static $status;
    public static $defaultProceedStatus;
    public static $defaultRevertStatus;

    /**
     * @var Task
     */
    public $task;

    public static function getState($task)
    {
        $states = static::STATES;
        $stateClass = isset($states[$task->status]) ? $states[$task->status] : $states[Task::STATUS_PENDING];

        return Yii::createObject([
            'class' => $stateClass,
            'task' => $task
        ]);
    }

    /**
     * Returns an array of statusItems.
     * Primary used in TaskFilter
     *
     * @return array
     */
    public static function getStatusItems()
    {
        return [
            Task::STATUS_PENDING => Yii::t('TasksModule.views_index_index', 'Pending'),
            Task::STATUS_IN_PROGRESS => Yii::t('TasksModule.views_index_index', 'In Progress'),
            Task::STATUS_PENDING_REVIEW => Yii::t('TasksModule.views_index_index', 'In Review'),
            Task::STATUS_COMPLETED => Yii::t('TasksModule.views_index_index', 'Completed'),
        ];
    }

    /**
     * send label for change-status button
     * @return string $statusLabel
     */
    public function getStatusLabel()
    {
        switch ($this->status) {
            case Task::STATUS_PENDING:
                $statusLabel = Yii::t('TasksModule.base', 'Begin Task');
                break;
            case Task::STATUS_IN_PROGRESS:
                return $this->review ? Yii::t('TasksModule.base', 'Let Task Review')
                    : Yii::t('TasksModule.base', 'Finish Task');
                break;
            case Task::STATUS_PENDING_REVIEW:
                $statusLabel = Yii::t('TasksModule.base', 'Finish Task');
                break;
            default :
                $statusLabel = '';
        }

        return $statusLabel;
    }

    public function proceed($newStatus = null)
    {
        if($newStatus instanceof TaskState) {
            $newStatus = $newStatus->getStatusId();
        } else {
            $newStatus = ($newStatus) ? $newStatus : $this->getDefaultProceedStatusId();
        }

        if(!array_key_exists($newStatus, static::STATES) || !$this->canProceed($newStatus)) {
            return false;
        }

        $this->changeStatus($newStatus);
        $this->task->state->afterProceed($this);
        return true;
    }

    public function reset()
    {
        if($this->canRevert(Task::STATUS_PENDING)) {
            $this->revert(Task::STATUS_PENDING);
            return true;
        }

        return false;
    }

    public function canProceed($newStatus = null, $user = null) {
        if($newStatus instanceof TaskState) {
            $newStatus = $newStatus->getStatusId();
        } else {
            $newStatus = ($newStatus) ? $newStatus : $this->getDefaultProceedStatusId();
        }

        if(!in_array($newStatus, $this->getProceedStatuses($user))) {
            return false;
        }

        return $this->checkProceedRules($newStatus, $user);
    }

    public function getProceedStatuses($user = null)
    {
        return array_keys($this->getProceedConfig($user));
    }

    public function getProceedConfig($user = null)
    {
        $result = $this->proceedConfig($user);

        if(!$this->task->review) {
            unset($result[Task::STATUS_PENDING_REVIEW]);
        }

        if(!$this->getStateInstance(Task::STATUS_COMPLETED)->canCompleteTask($user)) {
            unset($result[Task::STATUS_COMPLETED]);
        }

        return $result;
    }

    protected function getDefaultProceedStatusId()
    {
        if(static::$defaultProceedStatus) {
            return static::$defaultProceedStatus;
        }

        return null;
    }

    /**
     * @return TaskState
     */
    public function getDefaultProceedState()
    {
        return $this->getStateInstance($this->getDefaultProceedStatusId());
    }

    public function revert($newStatus = null)
    {
        if($newStatus instanceof TaskState) {
            $newStatus = $newStatus->getStatusId();
        } else {
            $newStatus = ($newStatus) ? $newStatus : $this->getDefaultRevertStatusId();
        }

        if(!array_key_exists($newStatus, static::STATES) || !$this->canRevert($newStatus)) {
            return false;
        }

        $this->changeStatus($newStatus);
        $this->task->state->afterRevert($this);
        return true;
    }

    public function canRevert($newStatus = null, $user = null)
    {
        if($newStatus instanceof TaskState) {
            $newStatus = $newStatus->getStatusId();
        } else {
            $newStatus = ($newStatus) ? $newStatus : $this->getDefaultRevertStatusId();
        }

        if(!in_array($newStatus, $this->getRevertStatuses($user))) {
            return false;
        }

        return $this->checkRevertRules($newStatus, $user);
    }

    public function getRevertStatuses($user = null)
    {
        return array_keys($this->getRevertConfig($user));
    }

    public function getRevertConfig($user = null)
    {
        $result = $this->revertConfig($user);

        if(!$this->task->review) {
            unset($result[Task::STATUS_PENDING_REVIEW]);
        }

        return $result;
    }

    protected function getDefaultRevertStatusId()
    {
        if(static::$defaultRevertStatus) {
            return static::$defaultRevertStatus;
        }

        return null;
    }

    public function getDefaultRevertState()
    {
        return $this->getStateInstance($this->getDefaultRevertStatusId());
    }

    protected function changeStatus($status)
    {
        $this->task->status = $status;
        $this->task->save();
    }

    /**
     * @param $newState
     * @return TaskState
     */
    public function getStateInstance($newState)
    {
        if($newState) {
            return Yii::createObject([
                'class' => static::STATES[$newState],
                'task' => $this->task
            ]);
        }

        return null;
    }

    public function getCheckUrl()
    {
        if($this->canProceed(Task::STATUS_COMPLETED)) {
            return $this->getStateInstance(Task::STATUS_COMPLETED)->getProceedUrl();
        } else if ($this->canProceed(Task::STATUS_PENDING_REVIEW)) {
            return $this->getStateInstance(Task::STATUS_PENDING_REVIEW)->getProceedUrl();
        }

        return null;
    }

    public function getProceedUrl()
    {
        return TaskUrl::proceedTask($this->task, static::$status);
    }

    public function getRevertUrl()
    {
        return TaskUrl::revertTask($this->task, static::$status);
    }

    public function isPending()
    {
        return ($this->task->status === Task::STATUS_PENDING);
    }

    public function isInProgress()
    {
        return ($this->task->status === Task::STATUS_IN_PROGRESS);
    }

    public function isPendingReview()
    {
        return ($this->task->status === Task::STATUS_PENDING_REVIEW);
    }

    public function isCompleted()
    {
        return ($this->task->status === Task::STATUS_COMPLETED);
    }

    public function getStatusId()
    {
        return static::$status;
    }

    protected abstract function proceedConfig($user = null);
    protected abstract function revertConfig($user = null);

    public abstract function checkRevertRules($newStatus = null, $user = null);
    public abstract function checkProceedRules($newStatus = null, $user = null);

    public abstract function afterProceed(TaskState $oldState);
    public abstract function afterRevert(TaskState $oldState);
}