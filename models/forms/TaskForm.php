<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
/**
 * Created by PhpStorm.
 * User: davidborn
 */

namespace humhub\modules\tasks\models\forms;

use humhub\libs\DbDateValidator;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\topic\models\Topic;
use Yii;
use yii\base\Model;
use DateTime;
use DateTimeZone;
use humhub\modules\tasks\models\scheduling\TaskReminder;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\tasks\models\Task;
use yii\web\HttpException;

class TaskForm extends Model
{

    /**
     * @var integer Content visibility
     */
    public $is_public;

    /**
     * @var Task
     */
    public $task;

    /**
     * @var string Format to validate a date field
     */
    public $dateFormat;

    /**
     * @var string Format to validate a date field
     */
    public $timeFormat;

    /**
     * @var string start date submitted by user will be converted to db date format and timezone after validation
     */
    public $start_date;

    /**
     * @var string start time string
     */
    public $start_time;

    /**
     * @var string end date submitted by user will be converted to db date format and timezone after validation
     */
    public $end_date;

    /**
     * @var string end time string
     */
    public $end_time;

    /**
     * @var string time zone of the task
     */
    public $timeZone;

    /**
     * @var boolean defines if the request came from a calendar
     */
    public $cal;

    /**
     * @var boolean defines if the request should be redirected after success
     */
    public $redirect;

    /**
     * @var integer
     */
    public $taskListId;

    /**
     * @var array
     */
    public $newItems;

    /**
     * @var array
     */
    public $editItems;

    /**
     * @var
     */
    public $reloadListId;

    /**
     * @var string
     */
    public $submitUrl;

    /**
     * @var array
     */
    public $topics = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->timeZone = empty($this->timeZone) ? Yii::$app->formatter->timeZone : $this->timeZone;

        if ($this->task) {
            $this->task->scenario = Task::SCENARIO_EDIT;
            if($this->task->all_day) {
                $this->timeZone = $this->task->time_zone;
            }

            $this->translateDateTimes($this->task->start_datetime, $this->task->end_datetime, Yii::$app->timeZone, $this->timeZone);
            $this->is_public = $this->task->content->visibility;

            $this->topics = Topic::findByContent($this->task->content);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timeZone'], 'in', 'range' => DateTimeZone::listIdentifiers()],
            [['start_time', 'end_time'], 'date', 'type' => 'time', 'format' => $this->getTimeFormat(), 'locale' => $this->getTimeLocale()],
            [['start_date'], DbDateValidator::class, 'format' => $this->getDateFormat(), 'timeAttribute' => 'start_time', 'timeZone' => $this->timeZone],
            [['end_date'], DbDateValidator::class, 'format' => $this->getDateFormat(), 'timeAttribute' => 'end_time', 'timeZone' => $this->timeZone],
            [['end_date'], 'validateEndTime'],

            [['start_date', 'end_date'], 'required', 'when' => function($model) {
                return $model->task->scheduling == 1;
            }, 'whenClient' => "function (attribute, value) {
                return $('#task-scheduling').val() == 1;
            }"],
            [['start_time', 'end_time'], 'required', 'when' => function($model) {
                return $model->task->all_day == 0;
            }, 'whenClient' => "function (attribute, value) {
                return $('#task-all_day').val() == 0;
            }"],

            [['is_public'], 'integer'],
            [['newItems', 'editItems', 'topics'], 'safe'],
        ];
    }

    public function getDateFormat()
    {
        if (isset($this->dateFormat)) {
            return $this->dateFormat;
        }

        return Yii::$app->formatter->dateInputFormat;
    }

    public function getTimeFormat()
    {
        if (isset($this->timeFormat)) {
            return $this->timeFormat;
        }

        return Yii::$app->formatter->isShowMeridiem() ? 'h:mm a' : 'php:H:i';
    }

    public function getTimeLocale()
    {
        return Yii::$app->formatter->isShowMeridiem() ? 'en-US' : Yii::$app->formatter->locale;
    }

    private function getAsTime($date)
    {
        $locale = Yii::$app->formatter->locale;

        Yii::$app->formatter->locale = $this->getTimeLocale();

        $result = Yii::$app->formatter->asTime($date, $this->getTimeFormat());

        Yii::$app->formatter->locale = $locale;
        return $result;
    }

    public function beforeValidate()
    {
        $this->checkAllDay();
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function checkAllDay()
    {
        Yii::$app->formatter->timeZone = $this->timeZone;
        
        if($this->task->all_day) {
            $date = new DateTime('now', new DateTimeZone($this->timeZone));
            $date->setTime(0,0);

            $this->start_time = $this->getAsTime($date);
            $date->setTime(23, 59);
            $this->end_time = $this->getAsTime($date);
        }
        Yii::$app->i18n->autosetLocale();
    }


    /**
     * Validator for the endtime field.
     * Execute this after DbDateValidator
     *
     * @param string $attribute attribute name
     * @param [] $params parameters
     * @throws \Exception
     */
    public function validateEndTime($attribute, $params)
    {
        if (new DateTime($this->start_date) >= new DateTime($this->end_date)) {
            $this->addError($attribute, Yii::t('TasksModule.models_forms_TaskForm', 'End time must be after start time!'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'start_date' => Yii::t('TasksModule.models_forms_TaskForm', 'Start Date'),
//            'type_id' => Yii::t('TasksModule.models_forms_TaskForm', 'Event Type'),
            'end_date' => Yii::t('TasksModule.models_forms_TaskForm', 'End Date'),
            'start_time' => Yii::t('TasksModule.models_forms_TaskForm', 'Start Time'),
            'end_time' => Yii::t('TasksModule.models_forms_TaskForm', 'End Time'),
            'timeZone' => Yii::t('TasksModule.models_forms_TaskForm', 'Time Zone'),
            'is_public' => Yii::t('TasksModule.models_forms_TaskForm', 'Public'),
        ]);
    }

    public function getTitle()
    {
        if($this->task->isNewRecord) {
           return Yii::t('TasksModule.views_index_edit', '<strong>Create</strong> new task');
        }

        return Yii::t('TasksModule.views_index_edit', '<strong>Edit</strong> task');
    }

    /**
     * Instantiates a new task for the given ContentContainerActiveRecord.
     *
     * @param ContentContainerActiveRecord $contentContainer
     * @throws \yii\base\Exception
     */
    public function createNew(ContentContainerActiveRecord $contentContainer)
    {
        $this->task = new Task($contentContainer, Content::VISIBILITY_PRIVATE, ['task_list_id' => $this->taskListId]);
        $this->task->scenario = Task::SCENARIO_EDIT;
        $this->is_public = ($this->task->content->visibility != null) ? $this->task->content->visibility : Content::VISIBILITY_PRIVATE;
    }

    /**
     * Loads this model and the task model with the given data.
     *
     * @inheritdoc
     *
     * @param array $data
     * @param null $formName
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function load($data, $formName = null)
    {
        // Make sure the timeZone is loaded prior to validation rule built
        if(isset($data[$this->formName()])) {
            $this->timeZone = $data[$this->formName()]['timeZone'] ?? $this->timeZone;
        }

        if(parent::load($data) && !empty($this->timeZone)) {
            $this->task->time_zone = $this->timeZone;
        }

        $this->task->content->visibility = $this->is_public;

        if(!$this->task->load($data)) {
            return false;
        }

        return true;
    }

    /**
     * Validates and saves the task instance.
     * @return bool
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function save()
    {
        $this->task->setEditItems($this->editItems);
        $this->task->setNewItems($this->newItems);

        if(!$this->validate()) {
            return false;
        }

        if(!$this->task->content->canEdit()) {
            throw new HttpException(403);
        }

        // After validation the date was translated to system time zone, which we expect in the database.
        $this->task->start_datetime = $this->start_date;
        $this->task->end_datetime = $this->end_date;

        // The form expects user time zone, so we translate back from app to user timezone
        $this->translateDateTimes($this->task->start_datetime, $this->task->end_datetime, Yii::$app->timeZone, $this->timeZone);

        // We save the list ids to reload in the view this has to be called before $task->save()!
        $this->reloadListId = $this->getListIdsToReload();

        if($this->task->save()) {
            RichText::postProcess($this->task->description, $this->task);
            // Required for attached files
            $this->task->fileManager->attach(Yii::$app->request->post('fileList'));
            // Save topics
            Topic::attach($this->task->content, $this->topics);
            return true;
        }

        return false;
    }

    public function showTimeFields()
    {
        return !$this->task->all_day;
    }

    private function getListIdsToReload()
    {
        $result = false;
        if(!$this->task->isNewRecord && $this->task->isAttributeChanged('task_list_id', false)) {
            $result = [$this->task->task_list_id];
            $result[] = $this->task->getOldAttribute('task_list_id');
        } else if($this->task->isNewRecord) {
            $result = [$this->task->task_list_id];
        }
        return $result;
    }

    /**
     * Translates the given start and end dates from $sourceTimeZone to $targetTimeZone and populates the form start/end time
     * and dates.
     *
     * By default $sourceTimeZone is the forms timeZone e.g user timeZone and $targetTimeZone is the app timeZone.
     *
     * @param string $start start string date in $sourceTimeZone
     * @param string $end end string date in $targetTimeZone
     * @param string $sourceTimeZone
     * @param string $targetTimeZone
     * @throws \yii\base\InvalidConfigException
     */
    public function translateDateTimes($start = null, $end = null, $sourceTimeZone = null, $targetTimeZone = null, $dateFormat = 'php:Y-m-d H:i:s e')
    {
        if(!$start) {
            return;
        }

        $sourceTimeZone = (empty($sourceTimeZone)) ? $this->timeZone : $sourceTimeZone;
        $targetTimeZone = (empty($targetTimeZone)) ? Yii::$app->timeZone : $targetTimeZone;

        $startTime = new DateTime($start, new DateTimeZone($sourceTimeZone));
        $endTime = new DateTime($end, new DateTimeZone($sourceTimeZone));

        Yii::$app->formatter->timeZone = $targetTimeZone;

        // Todo: check if this is really necessary
        // Fix FullCalendar EndTime
        /*if (CalendarUtils::isFullDaySpan($startTime, $endTime, true)) {
            // In Fullcalendar the EndTime is the moment AFTER the event so we substract one second
            $endTime->sub(new DateInterval("PT1S"));
            $this->task->all_day = 1;
        }*/

        $this->start_date = Yii::$app->formatter->asDateTime($startTime, $dateFormat);
        $this->start_time = $this->getAsTime($startTime);

        $this->end_date = Yii::$app->formatter->asDateTime($endTime, $dateFormat);
        $this->end_time = $this->getAsTime($endTime);

        Yii::$app->i18n->autosetLocale();
    }

    public function getSubmitUrl()
    {
        return ($this->submitUrl) ? $this->submitUrl : TaskUrl::editTask($this->task, $this->cal, $this->redirect, $this->taskListId);
    }

    public function getDeleteUrl()
    {
        return TaskUrl::deleteTask($this->task, $this->cal, $this->redirect);
    }

    public function getTaskAssignedPickerUrl()
    {
        return TaskUrl::pickerAssigned($this->task);
    }

    public function getTaskResponsiblePickerUrl()
    {
        return TaskUrl::pickerResponsible($this->task);
    }

    /**
     * @return ContentContainerActiveRecord
     */
    public function getContentContainer()
    {
        return $this->task->content->container;
    }

    public function getRemindModeItems()
    {
        return [
            TaskReminder::REMIND_ONE_HOUR => Yii::t('TasksModule.models_taskReminder', 'At least 1 Hour before'),
            TaskReminder::REMIND_TWO_HOURS => Yii::t('TasksModule.models_taskReminder', 'At least 2 Hours before'),
            TaskReminder::REMIND_ONE_DAY => Yii::t('TasksModule.models_taskReminder', '1 Day before'),
            TaskReminder::REMIND_TWO_DAYS => Yii::t('TasksModule.models_taskReminder', '2 Days before'),
            TaskReminder::REMIND_ONE_WEEK => Yii::t('TasksModule.models_taskReminder', '1 Week before'),
            TaskReminder::REMIND_TWO_WEEKS => Yii::t('TasksModule.models_taskReminder', '2 Weeks before'),
            TaskReminder::REMIND_THREE_WEEKS => Yii::t('TasksModule.models_taskReminder', '3 Weeks before'),
            TaskReminder::REMIND_ONE_MONTH => Yii::t('TasksModule.models_taskReminder', '1 Month before'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            [
                'label' => Yii::t('TasksModule.views_index_edit', 'Basic'),
                'view' => 'edit-basic',
                'linkOptions' => ['class' => 'tab-basic'],
                'fields' => ['title', 'task_list_id', 'description', 'topics', 'is_public', 'scheduling'],
            ],
            [
                'label' => Yii::t('TasksModule.views_index_edit', 'Scheduling'),
                'view' => 'edit-scheduling',
                'linkOptions' => ['class' => 'tab-scheduling'],
                'fields' => ['all_day', 'start_date', 'start_time', 'end_date', 'end_time', 'selectedReminders', 'cal_mode'],
            ]
        ];

        if ($this->getContentContainer() instanceof Space) {
            $tabs[] = [
                'label' => Yii::t('TasksModule.views_index_edit', 'Assignment'),
                'view' => 'edit-assignment',
                'linkOptions' => ['class' => 'tab-assignment'],
                'fields' => ['assignedUsers', 'responsibleUsers', 'review'],
            ];
        }

        $tabs[] = [
            'label' => Yii::t('TasksModule.views_index_edit', 'Checklist'),
            'view' => 'edit-checklist',
            'linkOptions' => ['class' => 'tab-checklist'],
        ];
        $tabs[] = [
            'label' => Yii::t('TasksModule.views_index_edit', 'Files'),
            'view' => 'edit-files',
            'linkOptions' => ['class' => 'tab-files'],
        ];

        // Activate tab with first error field
        if ($this->hasErrors()) {
            $errorFields = array_keys($this->getErrors());
            foreach ($tabs as $t => $tab) {
                if (!empty(array_intersect($tab['fields'], $errorFields))) {
                    $tabs[$t]['active'] = true;
                    break;
                }
            }
        }

        return $tabs;
    }
}
