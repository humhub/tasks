<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models\scheduling;

use DateTime;
use DateTimeZone;
use humhub\modules\tasks\CalendarUtils;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\integration\calendar\TaskCalendar;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\notifications\ChangedDateTimeNotification;
use humhub\modules\tasks\notifications\ExtensionRequestNotification;
use Yii;
use yii\base\Component;
use yii\helpers\Html;

/**
 * Class TaskScheduling
 *
 * @todo change base class back to BaseObject after v1.3 stable
 * @package humhub\modules\tasks\models\scheduling
 */
class TaskScheduling extends Component
{

    /**
     * Cal Modes
     */
    const CAL_MODE_NONE = 0;
    const CAL_MODE_SPACE = 1;

    /**
     * @var array all given cal modes as array
     */
    public static $calModes = [
        self::CAL_MODE_NONE,
        self::CAL_MODE_SPACE
    ];

    /**
     * @var Task
     */
    public $task;

    /**
     * @return boolean weather or not this item spans exactly over a whole day
     */
    public function isAllDay()
    {
        if ($this->task->all_day === null) {
            return true;
        }

        return (boolean) $this->task->all_day;
    }

    public function isOverdue()
    {
        if (!$this->task->scheduling) {
            return false;
        }

        return !$this->task->isCompleted() && (strtotime($this->task->end_datetime) < time());
    }

    public function getStartDateTime()
    {
        return new DateTime($this->task->start_datetime, new DateTimeZone(Yii::$app->timeZone));
    }

    public function getEndDateTime()
    {
        return new DateTime($this->task->end_datetime, new DateTimeZone(Yii::$app->timeZone));
    }

    public function beforeSave()
    {
        if($this->task->scenario === Task::SCENARIO_EDIT) {
            if (!$this->task->scheduling) {
                $this->task->start_datetime = null;
                $this->task->end_datetime = null;
            }/* else if (CalendarUtils::isFullDaySpan(new DateTime($this->task->start_datetime), new DateTime($this->task->end_datetime))) {
                $this->task->all_day = 1;
            }*/
        }

        // Reset deadline extension requests
        if ($this->task->isAttributeChanged('start_datetime', true) || $this->task->isAttributeChanged('end_datetime', true)) {
            $this->task->request_sent = 0;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($this->task->scenario === Task::SCENARIO_EDIT) {
            TaskReminder::deleteAll(['task_id' => $this->task->id]);

            if (!empty($this->task->selectedReminders)) {
                foreach ($this->task->selectedReminders as $remind_mode) {
                    $this->addTaskReminder($remind_mode);
                }
            }
        }

        if (!$insert && (array_key_exists('start_datetime', $changedAttributes) || array_key_exists('end_datetime', $changedAttributes))) {
            self::notifyDateTimeChanged();
        }
    }

    public function addTaskReminder($remind_mode)
    {
        if (!$remind_mode) {
            return false;
        }

        if (!$this->isTaskReminder($remind_mode)) {
            $taskReminder = new TaskReminder([
                'task_id' => $this->task->id,
                'remind_mode' => $remind_mode,
            ]);
            return $taskReminder->save();
        }

        return false;
    }

    public function isTaskReminder($remind_mode)
    {
        if (!$remind_mode) {
            return false;
        }

        $taskReminder = $this->task->getTaskReminder()->where(['remind_mode' => $remind_mode])->one();

        return !empty($taskReminder);
    }

    public function hasTaskReminder()
    {
        return !empty($this->task->taskReminder);
    }

    public function getFormattedEndDateTime($timeZone = null, $format = 'short')
    {
        if ($timeZone) {
            Yii::$app->formatter->timeZone = $timeZone;
        }

        if ($this->task->all_day) {
            $result = Yii::$app->formatter->asDate($this->getEndDateTime(), $format);
        } else {
            $result = Yii::$app->formatter->asDatetime($this->getEndDateTime(), $format);
        }
        if ($timeZone) {
            Yii::$app->i18n->autosetLocale();
        }

        return $result;
    }

    public function getFormattedStartDateTime($timeZone = null, $format = 'short')
    {
        if ($timeZone) {
            Yii::$app->formatter->timeZone = $timeZone;
        }

        if ($this->task->all_day) {
            $result = Yii::$app->formatter->asDate($this->getStartDateTime(), $format);
        } else {
            $result = Yii::$app->formatter->asDatetime($this->getStartDateTime(), $format);
        }

        if ($timeZone) {
            Yii::$app->i18n->autosetLocale();
        }

        return $result;
    }

    public function getFormattedStartText($timeZone = null, $format = 'short')
    {
        if ($timeZone) {
            Yii::$app->formatter->timeZone = $timeZone;
        }

        if (!$this->task->scheduling)
            $result = '';
        else {
            $result = Yii::t('TasksModule.views_index_index', 'Starting at');
            if ($this->task->all_day) {
                $result .= ' ' . Yii::$app->formatter->asDate($this->getStartDateTime(), $format);
            }
            else {
                $result .= ' ' . Yii::$app->formatter->asDatetime($this->getStartDateTime(), $format);
            }
        }

        if ($timeZone) {
            Yii::$app->i18n->autosetLocale();
        }

        return $result;
    }

    public function getFormattedDateTime($timeZone = null, $format = 'short')
    {
        if ($timeZone) {
            Yii::$app->formatter->timeZone = $timeZone;
        }

        if (!$this->task->scheduling)
            $result = Yii::t('TasksModule.views_index_index', 'No Scheduling set for this Task');
        else {
            $result = Yii::t('TasksModule.views_index_index', 'Deadline at');
            if ($this->task->all_day) {
                $result .= ' ' . Yii::$app->formatter->asDate($this->getEndDateTime(), $format);
            }
            else {
                $result .= ' ' . Yii::$app->formatter->asDatetime($this->getEndDateTime(), $format);
            }
        }

        if ($timeZone) {
            Yii::$app->i18n->autosetLocale();
        }

        return $result;
    }

    /**
     * Request deadline extension
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function sendExtensionRequest()
    {
        if ($this->task->hasTaskResponsible()) {
            $this->task->deleteOldNotifications(ExtensionRequestNotification::className());
            ExtensionRequestNotification::instance()->from(Yii::$app->user->getIdentity())->about($this->task)->sendBulk($this->task->taskResponsibleUsers);
        }
    }

    /**
     * handle task specific permissions
     * @return bool
     */
    public function canRequestExtension()
    {
        if (!$this->task->scheduling || $this->task->isTaskResponsible() || !$this->task->hasTaskResponsible()) {
            return false;
        }

        if($this->task->isCompleted() || $this->task->isPendingReview()) {
            return false;
        }

        if($this->hasRequestedExtension()) {
            return false;
        }

        return $this->task->isTaskAssigned() || $this->task->canProcess();
    }

    public function hasRequestedExtension()
    {
        return (boolean)($this->task->request_sent);
    }

    /**
     * Notify users about status change
     * @throws \yii\base\InvalidConfigException
     */
    public function notifyDateTimeChanged()
    {
        if ($this->task->isCompleted())
            return;
        // remove old notifications
        $this->task->deleteOldNotifications(ChangedDateTimeNotification::className());

        if (!empty($this->taskAssignedUsers)) {
            ChangedDateTimeNotification::instance()->from(Yii::$app->user->getIdentity())->about($this->task)->sendBulk($this->task->filterResponsibleAssigned());
        }

        if (!empty($this->taskResponsibleUsers)) {
            ChangedDateTimeNotification::instance()->from(Yii::$app->user->getIdentity())->about($this->task)->sendBulk($this->taskResponsibleUsers);
        }
    }

    public function getDurationDays()
    {
        $end = $this->getEndDateTime();
        if ($this->isAllDay()) {
            if ($end === $this->getEndDateTime()->setTime('00', '00', '00'))
                $end->modify('-1 day'); // revert modifications for all-day events integrated via interface
        }
        $interval = $this->getStartDateTime()->diff($end, true);
        return $interval->days + 1;
    }

    public function getDaysRemaining()
    {
        $end = $this->getEndDateTime();
        if ($this->isAllDay()) {
            if ($end === $this->getEndDateTime()->setTime('00', '00', '00'))
                $end->modify('-1 day'); // revert modifications for all-day events integrated via interface
        }
        $interval = (new DateTime())->diff($end, true);
        return $interval->days + 1;
    }

    public function isDeadlineToday()
    {
        $today = new DateTime("now", new DateTimeZone(Yii::$app->formatter->timeZone));
        return Yii::$app->formatter->asDate($this->getEndDateTime(), "php:dmY") == $today->format('dmY');
    }

    /**
     * Returns an array of calendarModes.
     *
     * @return array
     */
    public static function getCalModeItems()
    {
        return [
            self::CAL_MODE_NONE => Yii::t('TasksModule.models_task', 'Don\'t add to calendar'),
            self::CAL_MODE_SPACE => Yii::t('TasksModule.models_task', 'Add Deadline to space calendar'),
        ];
    }

    public function getCalMode()
    {
        switch ($this->cal_mode) {
            case (self::CAL_MODE_NONE):
                return Yii::t('TasksModule.models_task', 'Don\'t add to calendar');
                break;
            case (self::CAL_MODE_SPACE):
                return Yii::t('TasksModule.models_task', 'Add to space calendar');
                break;
            default:
                return;
        }
    }

    /**
     * @inheritdoc
     */
    public function getFullCalendarArray()
    {
        $title = Yii::t('TasksModule.models_task', 'Deadline: ') . Html::encode($this->task->title);


        $result = [
//            'id' => $this->id,
            'title' => $title,
//            'editable' => ($this->content->canEdit() || self::isTaskResponsible()),
            'editable' => false,
            //'color' => $color ? $color : TaskCalendar::DEFAULT_COLOR,
            'allDay' => $this->task->all_day,
            //'updateUrl' => $this->task->content->container->createUrl('/tasks/task/edit-ajax', ['id' => $this->task->id]),
            'viewUrl' => TaskUrl::viewTaskModal($this->task, 1),
            'openUrl' => TaskUrl::viewTask($this->task),
            'start' => $this->getStartDateTime(),
            'end' => $this->getEndDateTime(),
        ];

        $color = Html::encode($this->task->getColor());

        if($color) {
            $result['color'] = $color;
        }

        return $result;
    }
}