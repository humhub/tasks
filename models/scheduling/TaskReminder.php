<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models\scheduling;

use Yii;
use DateTime;
use humhub\components\ActiveRecord;
use humhub\modules\notification\models\Notification;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\notifications\RemindEnd;
use humhub\modules\tasks\notifications\RemindStart;

/**
 * This is the model class for table "task_reminder".
 *
 * The followings are the available columns in table 'task_reminder':
 * @property int $id
 * @property int $task_id
 * @property int $remind_mode
 * @property int $start_reminder_sent
 * @property int $end_reminder_sent
 * @property Task $task
 */
class TaskReminder extends ActiveRecord
{
    /**
     * @inheritdocs
     */
    protected $streamChannel = null;

    /**
     * Remind Mode
     */
    public const REMIND_NONE = 0;
    public const REMIND_ONE_HOUR = 1;
    public const REMIND_TWO_HOURS = 2;
    public const REMIND_ONE_DAY = 3;
    public const REMIND_TWO_DAYS = 4;
    public const REMIND_ONE_WEEK = 5;
    public const REMIND_TWO_WEEKS = 6;
    public const REMIND_THREE_WEEKS = 7;
    public const REMIND_ONE_MONTH = 8;

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'task_reminder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'remind_mode'], 'required'],
            [['task_id', 'start_reminder_sent', 'end_reminder_sent'], 'integer'],
            [['remind_mode'], 'in', 'range' => array_keys(self::getRemindModeItems())],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => Yii::t('TasksModule.base', 'Task'),
            'remind_mode' => Yii::t('TasksModule.base', 'Remind Mode'),
            'remind_sent' => Yii::t('TasksModule.base', 'Reminder sent'),
        ];
    }

    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    public static function getRemindModeItems()
    {
        return [
            self::REMIND_NONE => Yii::t('TasksModule.base', 'Do not remind'),
            self::REMIND_ONE_HOUR => Yii::t('TasksModule.base', 'At least 1 Hour before'),
            self::REMIND_TWO_HOURS => Yii::t('TasksModule.base', 'At least 2 Hours before'),
            self::REMIND_ONE_DAY => Yii::t('TasksModule.base', '1 Day before'),
            self::REMIND_TWO_DAYS => Yii::t('TasksModule.base', '2 Days before'),
            self::REMIND_ONE_WEEK => Yii::t('TasksModule.base', '1 Week before'),
            self::REMIND_TWO_WEEKS => Yii::t('TasksModule.base', '2 Weeks before'),
            self::REMIND_THREE_WEEKS => Yii::t('TasksModule.base', '3 Weeks before'),
            self::REMIND_ONE_MONTH => Yii::t('TasksModule.base', '1 Month before'),
        ];
    }

    public function canSendRemind(DateTime $now, DateTime $dateTime)
    {
        if ($now === '' || $dateTime === '') {
            return false;
        }

        $modifiedStart = clone $dateTime;
        $modifiedEnd = clone $dateTime;

        switch ($this->remind_mode) {
            case self::REMIND_ONE_HOUR :
                $modifiedStart = $modifiedStart->modify('-2 hours');
                $modifiedEnd = $modifiedEnd->modify('-1 hour');
                break;
            case self::REMIND_TWO_HOURS :
                $modifiedStart = $modifiedStart->modify('-3 hours');
                $modifiedEnd = $modifiedEnd->modify('-2 hours');
                break;
            case self::REMIND_ONE_DAY :
                $modifiedStart = $modifiedStart->modify('-1 day');
                $modifiedStart = $modifiedStart->setTime('00', '00', '00');
                $modifiedEnd = $modifiedEnd->modify('-1 day');
                $modifiedEnd = $modifiedEnd->setTime('23', '59', '59');
                break;
            case self::REMIND_TWO_DAYS :
                $modifiedStart = $modifiedStart->modify('-2 days');
                $modifiedStart = $modifiedStart->setTime('00', '00', '00');
                $modifiedEnd = $modifiedEnd->modify('-2 days');
                $modifiedEnd = $modifiedEnd->setTime('23', '59', '59');
                break;
            case self::REMIND_ONE_WEEK :
                $modifiedStart = $modifiedStart->modify('-1 week');
                $modifiedStart = $modifiedStart->setTime('00', '00', '00');
                $modifiedEnd = $modifiedEnd->modify('-1 week');
                $modifiedEnd = $modifiedEnd->setTime('23', '59', '59');
                break;
            case self::REMIND_TWO_WEEKS :
                $modifiedStart = $modifiedStart->modify('-2 weeks');
                $modifiedStart = $modifiedStart->setTime('00', '00', '00');
                $modifiedEnd = $modifiedEnd->modify('-2 weeks');
                $modifiedEnd = $modifiedEnd->setTime('23', '59', '59');
                break;
            case self::REMIND_THREE_WEEKS :
                $modifiedStart = $modifiedStart->modify('-3 weeks');
                $modifiedStart = $modifiedStart->setTime('00', '00', '00');
                $modifiedEnd = $modifiedEnd->modify('-3 weeks');
                $modifiedEnd = $modifiedEnd->setTime('23', '59', '59');
                break;
            case self::REMIND_ONE_MONTH :
                $modifiedStart = $modifiedStart->modify('-1 month');
                $modifiedStart = $modifiedStart->setTime('00', '00', '00');
                $modifiedEnd = $modifiedEnd->modify('-1 month');
                $modifiedEnd = $modifiedEnd->setTime('23', '59', '59');
                break;
            default:
                return false;
        }

        return $modifiedEnd > $now && $modifiedStart <= $now;
    }

    /**
     * Remind users
     */
    public function remindUserOfStart()
    {
        $this->deleteOldReminder();
        RemindStart::instance()->from($this->task->content->createdBy)->about($this->task)->sendBulk($this->task->users);
    }

    /**
     * Remind users
     */
    public function remindUserOfEnd()
    {
        $this->deleteOldReminder();
        RemindEnd::instance()->from($this->task->content->createdBy)->about($this->task)->sendBulk($this->task->users);
    }

    public function deleteOldReminder()
    {
        // delete old reminder
        $startNotifications = Notification::find()->where(['class' => RemindStart::class, 'source_class' => Task::class, 'source_pk' => $this->task->id, 'space_id' => $this->task->content->container->id])->all();
        foreach ($startNotifications as $notification) {
            $notification->delete();
        }

        $endNotifications = Notification::find()->where(['class' => RemindEnd::class, 'source_class' => Task::class, 'source_pk' => $this->task->id, 'space_id' => $this->task->content->container->id])->all();
        foreach ($endNotifications as $notification) {
            $notification->delete();
        }
    }

    public function handleRemind(DateTime $now)
    {
        $task = $this->task;

        if (!$this->start_reminder_sent) {
            if (self::canSendRemind($now, $task->schedule->getStartDateTime())) {
                $this->remindUserOfStart();
                $this->updateAttributes(['start_reminder_sent' => 1]);
                return true;
            }
        }

        if ($task->schedule->getStartDateTime() < $now) {
            //$this->updateAttributes(['start_reminder_sent' => 1]);
        }

        if (!$this->end_reminder_sent) {
            if (self::canSendRemind($now, $task->schedule->getEndDateTime())) {
                $this->remindUserOfEnd();
                $this->updateAttributes(['end_reminder_sent' => 1]);
                return true;
            }
        }

        if ($task->schedule->getEndDateTime() < $now) {
            //$this->updateAttributes(['end_reminder_sent' => 1]);
        }

        return false;
    }
}
