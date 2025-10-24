<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models;

use Colors\RandomColor;
use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentContainerPermissionManager;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ProcessUnassignedTasks;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use humhub\modules\notification\models\Notification;
use humhub\modules\tasks\models\checklist\TaskCheckList;
use humhub\modules\tasks\models\checklist\TaskItem;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\models\scheduling\TaskReminder;
use humhub\modules\tasks\models\scheduling\TaskScheduling;
use humhub\modules\tasks\models\state\TaskState;
use humhub\modules\tasks\models\user\TaskUser;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\user\models\User;
use humhub\modules\search\interfaces\Searchable;
use humhub\widgets\bootstrap\Badge;
use humhub\modules\tasks\widgets\WallEntry;
use humhub\modules\tasks\permissions\ManageTasks;

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property int $id
 * @property string $title
 * @property string $color
 * @property string $description
 * @property int $review
 * @property int $request_sent
 * @property int $scheduling
 * @property int $all_day
 * @property string $start_datetime
 * @property string $end_datetime
 * @property int $status
 * @property int $cal_mode
 * @property int $task_list_id
 * @property string $time_zone The timeZone this entry was saved, note the dates itself are always saved in app timeZone
 * @property string $uid uid field used by calendar integration
 *
 * @property TaskReminder[] $taskReminder
 * @property TaskItem[] $items
 * @property TaskList $list
 * @property User[] $taskResponsibleUsers
 * @property User[] $taskAssignedUsers
 */
class Task extends ContentActiveRecord implements Searchable
{
    public const SCENARIO_EDIT = 'edit';

    /**
     * @inheritdocs
     */
    public $canMove = true;

    /**
     * @inheritdocs
     */
    public $moduleId = 'tasks';

    /**
     * @inheritdocs
     */
    public $wallEntryClass = WallEntry::class;

    public $assignedUsers;
    public $responsibleUsers;
    public $selectedReminders;
    public $newItems;
    public $editItems;

    /**
     * Status
     */
    public const STATUS_PENDING = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_PENDING_REVIEW = 3;
    public const STATUS_COMPLETED = 5;
    public const STATUS_ALL = 4;

    /**
     * @deprecated
     */
    public const STATUS_OPEN = 1;

    /**
     * @var array all given statuses as array
     */
    public static $statuses = [
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_PENDING_REVIEW,
        self::STATUS_COMPLETED,
    ];

    /**
     * User Types
     */
    public const USER_ASSIGNED = 0;
    public const USER_RESPONSIBLE = 1;
    public const USER_AUTHOR = 2;


    /**
     * @var TaskState
     */
    public $state;

    /**
     * @var TaskScheduling
     */
    public $schedule;

    /**
     * @var TaskCheckList
     */
    public $checklist;

    /**
     * @inheritdoc
     */
    protected $createPermission = CreateTask::class;

    /**
     * @inheritdoc
     */
    protected $managePermission = ManageTasks::class;

    public function init()
    {
        $this->schedule = new TaskScheduling(['task' => $this]);
        $this->checklist = new TaskCheckList(['task' => $this]);

        if ($this->status == null) {
            $this->status = static::STATUS_PENDING;
        }
        $this->updateState();

        if (!$this->all_day) {
            $this->all_day = 1;
        }

        parent::init();

    }

    public function afterFind()
    {
        $this->updateState();
        parent::afterFind();
    }

    protected function updateState()
    {
        $this->state = TaskState::getState($this);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EDIT] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }


    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($name == 'deadline') {
            return parent::__get('end_datetime');
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        parent::__set($name, $value);
        if ($name == 'status') {
            $this->updateState();
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return Yii::t('TasksModule.base', 'Task');
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getIcon()
    {
        return 'tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $dateFormat = 'php:Y-m-d H:i:s';

        return [
            [['title'], 'required'],
            [['color'], 'string', 'max' => 7],
            [['start_datetime', 'end_datetime'], 'required', 'when' => fn($model) => $model->scheduling == 1, 'whenClient' => "function (attribute, value) {
                return $('#task-scheduling').val() == 1;
            }"],
            [['start_datetime'], 'default', 'value' => null],
            [['end_datetime'], 'default', 'value' => null],
            [['start_datetime'], 'date', 'format' => $dateFormat],
            [['end_datetime'], 'date', 'format' => $dateFormat],
            [['all_day', 'scheduling', 'review', 'request_sent'], 'integer'],
            [['cal_mode'], 'in', 'range' => TaskScheduling::$calModes],
            [['assignedUsers', 'description', 'responsibleUsers', 'selectedReminders'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['task_list_id'], 'validateTaskList'],
        ];
    }

    public function validateTaskList()
    {
        if ($this->task_list_id && !$this->hasNewTaskList()) {
            $taskList = TaskList::findByContainer($this->content->container)->where(['id' => $this->task_list_id]);
            if (!$taskList->exists()) {
                $this->addError('task_list_id', Yii::t('TasksModule.base', 'Invalid task list selection.'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Yii::t('TasksModule.base', 'Title'),
            'color' => Yii::t('TasksModule.base', 'Color'),
            'description' => Yii::t('TasksModule.base', 'Description'),
            'review' => Yii::t('TasksModule.base', 'Review by responsible user required'),
            'request_sent' => Yii::t('TasksModule.base', 'Extend deadline request'),
            'scheduling' => Yii::t('TasksModule.base', 'Scheduling'),
            'all_day' => Yii::t('TasksModule.base', 'All Day'),
            'start_datetime' => Yii::t('TasksModule.base', 'Start'),
            'end_datetime' => Yii::t('TasksModule.base', 'End'),
            'status' => Yii::t('TasksModule.base', 'Status'),
            'cal_mode' => Yii::t('TasksModule.base', 'Add schedule to the space calendar'),
            'parent_task_id' => Yii::t('TasksModule.base', 'Parent Task'),
            'newItems' => Yii::t('TasksModule.base', 'Checklist Items'),
            'editItems' => Yii::t('TasksModule.base', 'Checklist Items'),
            'assignedUsers' => Yii::t('TasksModule.base', 'Assigned users'),
            'responsibleUsers' => Yii::t('TasksModule.base', 'Responsible users'),
            'selectedReminders' => Yii::t('TasksModule.base', 'Reminders'),
            'task_list_id' => Yii::t('TasksModule.base', 'Task List'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return TaskUrl::viewTask($this);
    }

    public function getList()
    {
        return $this->hasOne(TaskList::class, ['id' => 'task_list_id']);
    }

    /**
     * @param ContentContainerActiveRecord $contentContainer
     * @return ActiveQueryContent
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public static function findUnsorted(ContentContainerActiveRecord $contentContainer)
    {
        return static::find()->contentContainer($contentContainer)->where('task_list_id IS NULL')->readable();
    }

    /**
     * @param ContentContainerActiveRecord $contentContainer
     * @return ActiveQueryContent
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public static function findUnsortedCompleted(ContentContainerActiveRecord $contentContainer)
    {
        return static::findUnsorted($contentContainer)->andWhere(['task.status' => Task::STATUS_COMPLETED]);
    }

    /**
     * @param User|null $user
     * @param ContentActiveRecord|null $container
     * @param int $limit
     * @return array|Notification[]|Task[]|\yii\db\ActiveRecord[]
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public static function findUserTasks(?User $user = null, $container = null, $limit = 5)
    {
        if (!$user && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
        } elseif (!$user) {
            return [];
        }

        $query = ($container) ? self::find()->contentContainer($container) : self::find();

        return $query
            ->leftJoin('task_user', 'task.id=task_user.task_id', [])
            ->where(['task_user.user_id' => $user->id])
            ->andWhere(['!=', 'task.status', Task::STATUS_COMPLETED])
            ->orderBy([new Expression('-task.end_datetime DESC')])
            ->readable()
            ->limit($limit)
            ->all();
    }

    public static function findPendingTasks(ContentContainerActiveRecord $container)
    {
        return self::find()
            ->contentContainer($container)
            //->orderBy([new Expression('-task.end_datetime DESC')])
            ->readable()
            ->andWhere(['!=', 'task.status', Task::STATUS_COMPLETED]);
    }

    public static function findReadable(ContentContainerActiveRecord $container)
    {
        return self::find()
            ->contentContainer($container)
            ->orderBy(['task.end_datetime' => SORT_DESC])
            ->readable();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->schedule->beforeSave();
        return $this->saveTaskList()
            && parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        foreach (TaskItem::findAll(['task_id' => $this->id]) as $item) {
            $item->delete();
        }

        foreach (TaskUser::findAll(['task_id' => $this->id]) as $taskUser) {
            $taskUser->delete();
        }

        foreach (TaskReminder::findAll(['task_id' => $this->id]) as $taskReminder) {
            $taskReminder->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * Saves new items (if set) and updates items given edititems (if set)
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     * @throws \yii\base\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->scenario === self::SCENARIO_EDIT) {
            $oldTaskUsers = $this->taskUsers;

            TaskUser::deleteAll(['task_id' => $this->id]);

            // Auto assign the user for his own tasks
            if ($this->content->container instanceof User) {
                $this->assignedUsers = [$this->content->container->guid];
            }

            if (!empty($this->assignedUsers)) {
                foreach ($this->assignedUsers as $guid) {
                    $user = User::findOne(['guid' => $guid]);

                    if (!$user) {
                        continue;
                    }

                    $oldAssigned = array_filter($oldTaskUsers, fn($taskUser) =>
                        /** @var $taskUser TaskUser */
                        $taskUser->user_id === $user->id && $taskUser->user_type === Task::USER_ASSIGNED);

                    $this->addTaskAssigned($guid, empty($oldAssigned));
                }
            }

            if (!empty($this->responsibleUsers)) {
                foreach ($this->responsibleUsers as $guid) {
                    $user = User::findOne(['guid' => $guid]);

                    if (!$user) {
                        continue;
                    }

                    $oldResponsible = array_filter($oldTaskUsers, fn($taskUser) =>
                        /** @var $taskUser TaskUser */
                        $taskUser->user_id === $user->id && $taskUser->user_type === Task::USER_RESPONSIBLE);

                    $this->addTaskResponsible($guid, empty($oldResponsible));
                }
            }

            $this->checklist->afterSave($insert);
            $this->schedule->afterSave($insert, $changedAttributes);

            $this->saveNewItems();

        }

        if ($this->list && $this->list->addition) {
            $this->list->addition->updateAttributes(['updated_at' => date('Y-m-d G:i:s')]);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterMove(?ContentContainerActiveRecord $container = null)
    {
        foreach ($this->taskUsers as $taskUser) {
            if (!$container->isMember($taskUser->user_id)) {
                $taskUser->delete();
            }
        }

        $this->updateAttributes(['task_list_id' => null]);
    }

    /**
     * Returns an ActiveQuery for all assigned user models of this task.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignedUsers($filterOutResponsibleUsers = false)
    {
        $query = $this->hasMany(User::class, ['id' => 'user_id'])->via('assignedTaskUsers');

        if ($filterOutResponsibleUsers) {
            $responsible = $this->getTaskResponsibleUsers()->select(['id']);
            $query->where(['not in', 'user.id', $responsible]);
        }

        return $query;
    }

    public function isTaskAssigned($user = null)
    {
        if (!$user && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
        } elseif (!$user) {
            return false;
        }

        $taskAssigned = array_filter($this->assignedTaskUsers, fn(TaskUser $p) => $p->user_id == $user->id);

        return !empty($taskAssigned);
    }

    public function addTaskAssigned($user, $sendNotification = true)
    {
        $user = (is_string($user)) ? User::findOne(['guid' => $user]) : $user;

        if (!$user) {
            return false;
        }

        if ($this->content->container instanceof User && !$user->is($this->content->container)) {
            return false;
        }


        if (!$this->isTaskAssigned($user)) {
            $taskAssigned = new TaskUser([
                'task_id' => $this->id,
                'user_id' => $user->id,
                'user_type' => self::USER_ASSIGNED,
                'sendNotificationOnCreation' => $sendNotification,
            ]);
            return $taskAssigned->save();
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        if ($this->list) {
            $this->list->setAttributes(['updated_at' => date('Y-m-d G:i:s')]);
        }

        parent::afterDelete();
    }


    // ###########  handle assigned users  ###########

    public function getTaskUsers()
    {
        return $this->hasMany(TaskUser::class, ['task_id' => 'id']);
    }

    /**
     * Returns an ActiveQuery for all assigned task users of this task.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedTaskUsers()
    {
        return $this->getTaskUsers()->andOnCondition(['user_type' => self::USER_ASSIGNED]);
    }

    public function hasTaskAssigned()
    {
        return !empty($this->assignedTaskUsers);
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->via('taskUsers');
    }




    // ###########  handle responsible users  ###########

    /**
     * Returns an ActiveQuery for all responsible task users of this task.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskResponsible()
    {
        return $this->hasMany(TaskUser::class, ['task_id' => 'id'])->andOnCondition(['user_type' => self::USER_RESPONSIBLE]);
    }

    public function hasTaskResponsible()
    {
        return !empty($this->taskResponsible);
    }

    /**
     * Returns an ActiveQuery for all responsible user models of this task.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskResponsibleUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->via('taskResponsible');
    }

    public function isTaskResponsible($user = null)
    {
        if (!$user && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
        } elseif (!$user) {
            return false;
        }

        $taskResponsible = array_filter($this->taskResponsible, fn(TaskUser $p) => $p->user_id == $user->id);

        return !empty($taskResponsible);
    }

    public function isOwner($user = null)
    {
        if (!$user && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
        } elseif (!$user) {
            return false;
        }

        return $this->content->created_by === $user->getId();
    }

    public function addTaskResponsible($user, $sendNotification = true)
    {
        $user = (is_string($user)) ? User::findOne(['guid' => $user]) : $user;

        if (!$user) {
            return false;
        }

        if (!$this->isTaskResponsible($user)) {
            $taskResponsible = new TaskUser([
                'task_id' => $this->id,
                'user_id' => $user->id,
                'user_type' => self::USER_RESPONSIBLE,
                'sendNotificationOnCreation' => $sendNotification,
            ]);
            return $taskResponsible->save();
        }

        return false;
    }

    public function isOverdue()
    {
        return $this->schedule->isOverdue();
    }


    /**
     * Returns an ActiveQuery for all assigned task users of this task.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskReminder()
    {
        $query = $this->hasMany(TaskReminder::className(), ['task_id' => 'id']);
        return $query;
    }


    // ###########  handle task items  ###########

    /**
     * Returns an ActiveQuery for all task items of this task.
     *
     * @return ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(TaskItem::class, ['task_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }

    public function hasItems()
    {
        // Todo check task_items and subtask-Items
        return !empty($this->items);
    }

    public function saveNewItems()
    {
        if ($this->newItems == null) {
            return;
        }

        foreach ($this->newItems as $itemText) {
            $this->addItem($itemText);
        }

        // Reset cached items
        unset($this->items);
    }

    public function addItem($itemText)
    {
        if (trim((string) $itemText) === '') {
            return;
        }

        $item = new TaskItem();
        $item->task_id = $this->id;
        $item->title = $itemText;
        $item->save();
        return $item;
    }



    /**
     * Sets the newItems array, which is used for creating and updating (afterSave)
     * the task, by saving all valid item title contained in the given array.
     * @param array $newItemArr
     */
    public function setNewItems($newItemArr)
    {
        $this->newItems = TaskItem::filterValidItems($newItemArr);
    }

    /**
     * Sets the editItems array, which is used for updating (afterSave)
     * the task. The given array has to contain task item ids as key and an title
     * as values.
     * @param array $editItemArr
     */
    public function setEditItems($editItemArr)
    {
        $this->editItems = TaskItem::filterValidItems($editItemArr);
    }

    /**
     * @param array $items
     * @throws \yii\db\Exception
     */
    public function confirm($items = [])
    {
        foreach ($items as $itemID) {
            $item = TaskItem::findOne(['id' => $itemID, 'task_id' => $this->id]);
            if ($item) {
                $item->completed = 1;
                $item->save();
            }
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function completeItems()
    {
        TaskItem::updateAll(['completed' => 1], ['task_id' => $this->id]);
    }

    /**
     * Returns the total number of confirmed users got this message
     *
     * @return int
     */
    public function getConfirmedCount()
    {
        return $this->getItems()->where(['completed' => true])->count();
    }

    public function isPending()
    {
        return $this->state->isPending();
    }

    public function isInProgress()
    {
        return $this->state->isInProgress();
    }

    public function isPendingReview()
    {
        return $this->state->isPendingReview();
    }

    public function isCompleted()
    {
        return $this->state->isCompleted();
    }

    public function canView($user = null): bool
    {
        if (!$this->content->canView($user)) {
            return false;
        }

        if (($this->content->container instanceof Space) && !$this->content->container->isMember($user)) {
            return false;
        }

        return true;
    }

    public function canReview($user = null)
    {
        return $this->review && $this->isTaskResponsible($user);
    }

    /**
     * Additional canEdit check for responsible users.
     * @return bool
     * @see Content::canEdit()
     */
    public function canEdit()
    {
        if ($this->isNewRecord) {
            return $this->content->container->can([CreateTask::class, ManageTasks::class]);
        } elseif (!$this->hasTaskResponsible()) {
            return  $this->content->container->can([ManageTasks::class]);
        }

        return  $this->isTaskResponsible();
    }

    // ###########  handle notifications  ###########

    /**
     * Filters responsible users from the list of assigned users
     *
     * @return array|User[]
     */
    public function filterResponsibleAssigned()
    {
        $responsible = $this->getTaskResponsibleUsers()->select(['id']);

        $filteredAssigned = $this->getTaskAssignedUsers()
            ->where(['not in', 'id', $responsible])
            ->all();
        return $filteredAssigned;
    }

    public function deleteOldNotifications($notificationClassName = null)
    {
        if (!$notificationClassName) {
            // delete all old notifications - used for reset
            $notifications = Notification::find()->where(['source_class' => self::class, 'source_pk' => $this->id, 'space_id' => $this->content->container->id])->all();
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        } else {
            // delete specific old notifications
            $notifications = Notification::find()->where(['class' => $notificationClassName, 'source_class' => self::class, 'source_pk' => $this->id, 'space_id' => $this->content->container->id])->all();
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        }
    }

    /**
     * Access url of the source content or other view
     *
     * @return string the timezone this item was originally saved, note this is
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns a badge for the snippet
     *
     * @return string the timezone this item was originally saved, note this is
     */
    public function getBadge()
    {
        if ($this->schedule->isOverdue()) {
            return Badge::danger(Yii::t('TasksModule.base', 'Overdue'))->icon('exclamation-triangle')->cssClass(['class' => 'me-1'])->right();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {
        $itemTitles = "";
        $itemDescriptions = "";

        foreach ($this->items as $item) {
            $itemTitles .= $item->title . ' ';
            $itemDescriptions .= $item->description . ' ';
        }

        return [
            'title' => $this->title,
            'description' => $this->description,
            'itemTitles' => trim($itemTitles),
            'itemDescriptions' => trim($itemDescriptions),
        ];
    }

    /**
     * handle task specific permissions
     * @return bool
     */
    public function canProcess($user = null)
    {
        if (!$user && Yii::$app->user->isGuest) {
            return false;
        }

        if (!$user) {
            $user = Yii::$app->user->getIdentity();
        }

        if ($this->isOwner($user)) {
            return true;
        }

        if ($this->canManageTasks($user)) {
            return true;
        }

        $permissionManager = new ContentContainerPermissionManager([
            'contentContainer' => $this->content->container,
            'subject' => $user,
        ]);

        return (!$this->hasTaskAssigned() && $permissionManager->can(ProcessUnassignedTasks::class));
    }

    public function canManageTasks($user = null)
    {
        if (!$user && Yii::$app->user->isGuest) {
            return false;
        }

        if (!$user) {
            $user = Yii::$app->user->getIdentity();
        }

        $permissionManager = new ContentContainerPermissionManager([
            'contentContainer' => $this->content->container,
            'subject' => $user,
        ]);

        return $permissionManager->can(ManageTasks::class);
    }

    /**
     * handle task specific permissions
     * @return bool
     */
    public function canCheckItems()
    {
        return (($this->isTaskResponsible() || $this->isTaskAssigned() || $this->canProcess()) && (!($this->isCompleted())));
    }

    /**
     * Only responsible users can review task
     * @return bool
     */
    public function canReviewTask()
    {
        return (self::isTaskResponsible());
    }

    /**
     * handle task specific permissions
     * @return bool
     */
    public function canResetTask()
    {
        return (self::isTaskResponsible() && (self::isCompleted()));
    }

    /**
     * handle task specific permissions
     * @return bool
     */
    public function canResortItems()
    {
        return (self::isTaskResponsible() && (!self::isCompleted()));
    }




    // ###########  handle view-specific  ###########

    /**
     * Returns the percentage of task
     *
     * @return int
     */
    public function getPercent()
    {
        //        $denominator = TaskItem::find()->where(['task_id' => $this->id])->count();
        $denominator = $this->getItems()->count();
        // add STATUS_IN_PROGRESS and STATUS_COMPLETED
        $denominator += 2;
        // handle special status STATUS_PENDING_REVIEW
        if ($this->review) {
            $denominator += 1;
        }
        if ($denominator == 0) {
            return 0;
        }


        $counter = $this->getConfirmedCount();
        if (self::isInProgress()) {
            $counter += 1;
        } elseif (self::isCompleted() && !$this->review) {
            $counter += 2;
        } elseif (self::isPendingReview() && $this->review) {
            $counter += 2;
        } elseif (self::isCompleted() && $this->review) {
            $counter += 3;
        }

        return $counter / $denominator * 100;
    }

    /**
     * Returns additional labels
     *
     * @param array $labels
     * @param bool $includeContentName
     * @return Badge[]|string[]
     */
    public function getLabels($labels = [], $includeContentName = true)
    {
        switch ($this->status) {
            case self::STATUS_PENDING :
                $labels[] = Badge::light(Yii::t('TasksModule.base', 'Pending'))->icon('info-circle')->sortOrder(350);
                break;
            case self::STATUS_IN_PROGRESS :
                $labels[] = Badge::accent(Yii::t('TasksModule.base', 'In Progress'))->icon('edit')->sortOrder(350);
                break;
            case self::STATUS_PENDING_REVIEW :
                $labels[] = Badge::warning(Yii::t('TasksModule.base', 'Pending Review'))->icon('exclamation-triangle')->sortOrder(350);
                break;
            case self::STATUS_COMPLETED :
                $labels[] = Badge::success(Yii::t('TasksModule.base', 'Completed'))->icon('check-square')->sortOrder(350);
                break;
            default:
                break;
        }

        if ($this->schedule->isOverdue()) {
            $labels[] = Badge::danger(Yii::t('TasksModule.base', 'Overdue'))->icon('exclamation-triangle')->sortOrder(360);
        }

        return parent::getLabels($labels, $includeContentName);
    }

    public function moveItemIndex($itemId, $newIndex)
    {
        $this->checklist->moveItemIndex($itemId, $newIndex);
    }

    /**
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function beforeRemoveUser()
    {
        $notifications = Notification::find()->where(['source_class' => self::className(), 'source_pk' => $this->id, 'space_id' => $this->content->contentContainer->id])->all();
        foreach ($notifications as $notification) {
            $notification->delete();
        }
    }

    /**
     * @param $userId
     * @return boolw
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function removeUser($userId)
    {
        if (empty($userId) || !isset($userId)) {
            return false;
        }

        $taskAssigned = $this->getAssignedTaskUsers()->where(['task_user.user_id' => $userId])->all();
        foreach ($taskAssigned as $assigned) {
            $assigned->delete();
        }
        $taskResponsible = $this->getTaskResponsible()->where(['task_user.user_id' => $userId])->all();
        foreach ($taskResponsible as $responsible) {
            $responsible->delete();
        }
    }

    public function getColor($default = null): ?string
    {
        $color = $this->task_list_id && $this->list ? $this->list->getColor() : null;
        return $color ?: $default;
    }

    private function hasNewTaskList(): bool
    {
        return !empty($this->task_list_id) && !preg_match('/^\d+$/', (string) $this->task_list_id);
    }

    private function saveTaskList(): bool
    {
        if (!$this->hasNewTaskList()) {
            return true;
        }

        $taskList = new TaskList($this->content->container);
        $taskList->color = RandomColor::one(['luminosity' => 'dark']);
        $taskList->name = $this->task_list_id;
        if (!$taskList->save()) {
            return false;
        }

        $this->task_list_id = $taskList->id;
        return true;
    }
}
