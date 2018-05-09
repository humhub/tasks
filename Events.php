<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks;

use humhub\modules\tasks\helpers\TaskListUrl;
use Yii;
use yii\base\Object;
use humhub\modules\notification\models\Notification;
use humhub\modules\tasks\jobs\SendReminder;
use humhub\modules\tasks\models\SnippetModuleSettings;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\checklist\TaskItem;
use humhub\modules\tasks\models\scheduling\TaskReminder;
use humhub\modules\tasks\integration\calendar\TaskCalendar;
use humhub\modules\tasks\widgets\MyTasks;
use humhub\modules\tasks\models\user\TaskUser;


/* @var $user \humhub\modules\user\models\User */

/**
 * Created by PhpStorm.
 * User: davidborn
 * Date: 14.09.2017
 * Time: 12:12
 */
class Events extends Object
{
    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemTypesEvent
     * @return mixed
     */
    public static function onGetCalendarItemTypes($event)
    {
        $contentContainer = $event->contentContainer;

        if(!$contentContainer || $contentContainer->isModuleEnabled('tasks')) {
            TaskCalendar::addItemTypes($event);
        }
    }

    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemsEvent;
     */
    public static function onFindCalendarItems($event)
    {
        $contentContainer = $event->contentContainer;

        if(!$contentContainer || $contentContainer->isModuleEnabled('tasks')) {
            TaskCalendar::addItems($event);
        }
    }

    public static function onDashboardSidebarInit($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $settings = SnippetModuleSettings::instantiate();

        if ($settings->showMyTasksSnippet()) {
            $event->sender->addWidget(MyTasks::className(), ['limit' => $settings->myTasksSnippetMaxItems], ['sortOrder' => $settings->myTasksSnippetSortOrder]);
        }
    }

    public static function onSpaceSidebarInit($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $space = $event->sender->space;
        $settings = SnippetModuleSettings::instantiate();

        if ($space->isModuleEnabled('tasks')) {
            if ($settings->showMyTasksSnippetSpace()) {
                $event->sender->addWidget(MyTasks::className(), ['limit' => $settings->myTasksSnippetMaxItems], ['sortOrder' => $settings->myTasksSnippetSortOrder]);
            }
        }
    }

    public static function onSpaceMenuInit($event)
    {
        /* @var $space \humhub\modules\space\models\Space */

        $space = $event->sender->space;

        if ($space->isModuleEnabled('tasks') && $space->isMember()) {

            $event->sender->addItem([
                'label' => Yii::t('TasksModule.base', 'Tasks'),
                'group' => 'modules',
                'url' => TaskListUrl::taskListRoot($space),
                'icon' => '<i class="fa fa-tasks"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'tasks'),
            ]);
        }
    }

    /**
     * Callback to validate module database records.
     *
     * @param Event $event
     * @throws \Exception
     */
    public static function onIntegrityCheck($event)
    {
        $integrityController = $event->sender;
        $integrityController->showTestHeadline("Tasks Module - Entries (" . Task::find()->count() . " entries)");

        // check for taskItems without task
        foreach (TaskItem::find()->all() as $taskItem) {
            if ($taskItem->task === null) {
                if ($integrityController->showFix("Deleting task item id " . $taskItem->id . " without existing task!")) {
                    $taskItem->delete();
                }
            }
        }

        // check for task responsible users without task or existing user
        foreach (TaskUser::find()->all() as $taskUser) {
            if ($taskUser->task === null) {
                if ($integrityController->showFix("Deleting task user user id " . $taskUser->id . " without existing task!")) {
                    $taskUser->delete();
                }
            }
            if ($taskUser->user === null) {
                if ($integrityController->showFix("Deleting task user user id " . $taskUser->id . " without existing user!")) {
                    $taskUser->delete();
                }
            }
        }

//        // check for task responsible users without task or existing user
//        foreach (TaskResponsible::find()->all() as $taskResponsible) {
//            if ($taskResponsible->task === null) {
//                if ($integrityController->showFix("Deleting task responsible user id " . $taskResponsible->id . " without existing task!")) {
//                    $taskResponsible->delete();
//                }
//            }
//            if ($taskResponsible->user === null) {
//                if ($integrityController->showFix("Deleting task responsible user id " . $taskResponsible->id . " without existing user!")) {
//                    $taskResponsible->delete();
//                }
//            }
//        }

//        // check for task assigned users without task or existing user
//        foreach (TaskAssigned::find()->all() as $taskAssigned) {
//            if ($taskAssigned->task === null) {
//                if ($integrityController->showFix("Deleting task assigned user id " . $taskAssigned->id . " without existing task!")) {
//                    $taskAssigned->delete();
//                }
//            }
//            if ($taskAssigned->user === null) {
//                if ($integrityController->showFix("Deleting task assigned user id " . $taskAssigned->id . " without existing user!")) {
//                    $taskAssigned->delete();
//                }
//            }
//        }

        // check for task reminders without task
        foreach (TaskReminder::find()->all() as $taskReminder) {
            if ($taskReminder->task === null) {
                if ($integrityController->showFix("Deleting task reminder id " . $taskReminder->id . " without existing task!")) {
                    $taskReminder->delete();
                }
            }
        }
    }


    /**
     * Handle what happens, when user left space.
     *
     * @param $event
     * @throws \Exception
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public static function onMemberRemoved ($event)
    {
        $tasks = Task::find()->contentContainer($event->space)->all();

        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $taskUser = $task->getAssignedTaskUsers()->where(['task_user.user_id' => $event->user->id])->all();
                foreach ($taskUser as $user) {
                    $user->delete();
                }

                // remove notifications
//                $event->sender->className()
//                $event->sender->getPrimaryKey()
                $notifications = Notification::find()->where(['source_class' => Task::className(), 'source_pk' => $task->id, 'space_id' => $event->space->id])->all();
                foreach ($notifications as $notification) {
                    $notification->delete();
                }
            }
        }
    }


    public static function onCronRun($event)
    {
        if (Yii::$app->controller->action->id == 'hourly') {
            Yii::$app->queue->push( new SendReminder());
        }
    }

}