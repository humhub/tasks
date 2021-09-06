<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\rest\Module as RestModule;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\user\models\User;
use Yii;
use humhub\modules\notification\models\Notification;
use humhub\modules\tasks\jobs\SendReminder;
use humhub\modules\tasks\models\SnippetModuleSettings;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\checklist\TaskItem;
use humhub\modules\tasks\models\scheduling\TaskReminder;
use humhub\modules\tasks\integration\calendar\TaskCalendar;
use humhub\modules\tasks\widgets\MyTasks;
use humhub\modules\tasks\models\user\TaskUser;
use yii\db\Expression;


/* @var $user \humhub\modules\user\models\User */

/**
 * Created by PhpStorm.
 * User: davidborn
 * Date: 14.09.2017
 * Time: 12:12
 */
class Events
{

    public static function onTopMenuInit($event)
    {
        try {
            /* @var $module Module */
            $module = Yii::$app->getModule('tasks');


            if (!(int)$module->settings->get('showGlobalMenuItem', 1)) {
                return;
            }

            // Is Module enabled on this workspace?
            $event->sender->addItem([
                'label' => Yii::t('TasksModule.base', 'Tasks'),
                'id' => 'tasks-global',
                'icon' => '<i class="fa fa-tasks"></i>',
                'url' => TaskUrl::globalView(),
                'sortOrder' => $module->settings->get('menuSortOrder', 500),
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'tasks' && Yii::$app->controller->id == 'global'),
            ]);
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemTypesEvent
     * @return mixed
     */
    public static function onGetCalendarItemTypes($event)
    {
        try {
            /* @var ContentContainerActiveRecord $contentContainer */
            $contentContainer = $event->contentContainer;

            if (!$contentContainer || $contentContainer->moduleManager->isEnabled('tasks')) {
                TaskCalendar::addItemTypes($event);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemsEvent;
     */
    public static function onFindCalendarItems($event)
    {
        try {
            /* @var ContentContainerActiveRecord $contentContainer */
            $contentContainer = $event->contentContainer;

            if (!$contentContainer || $contentContainer->moduleManager->isEnabled('tasks')) {
                TaskCalendar::addItems($event);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onDashboardSidebarInit($event)
    {
        try {
            if (Yii::$app->user->isGuest) {
                return;
            }

            $settings = SnippetModuleSettings::instantiate();

            if ($settings->showMyTasksSnippet()) {
                $event->sender->addWidget(MyTasks::class, ['limit' => $settings->myTasksSnippetMaxItems], ['sortOrder' => $settings->myTasksSnippetSortOrder]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onSpaceSidebarInit($event)
    {
        try {
            if (Yii::$app->user->isGuest) {
                return;
            }

            /* @var $space Space */
            $space = $event->sender->space;

            if ($space->moduleManager->isEnabled('tasks') && $space->isMember()) {
                $settings = SnippetModuleSettings::instantiate();
                if ($settings->showMyTasksSnippetSpace()) {
                    $event->sender->addWidget(MyTasks::class, [
                        'contentContainer' => $space,
                        'limit' => $settings->myTasksSnippetMaxItems], ['sortOrder' => $settings->myTasksSnippetSortOrder]);
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onSpaceMenuInit($event)
    {
        try {
            /* @var $space Space */
            $space = $event->sender->space;

            if ($space->moduleManager->isEnabled('tasks') && $space->isMember()) {
                $event->sender->addItem([
                    'label' => Yii::t('TasksModule.base', 'Tasks'),
                    'group' => 'modules',
                    'url' => TaskListUrl::taskListRoot($space),
                    'icon' => '<i class="fa fa-tasks"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'tasks'),
                ]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onProfileMenuInit($event)
    {
        /* @var $user User */
        try {
            $user = $event->sender->user;
            if ($user->moduleManager->isEnabled('tasks')) {
                $event->sender->addItem([
                    'label' => Yii::t('TasksModule.base', 'Tasks'),
                    'url' => TaskListUrl::taskListRoot($user),
                    'icon' => '<i class="fa fa-tasks"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'tasks'),
                ]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * Callback to validate module database records.
     *
     * @param Event $event
     * @throws \Exception
     * @throws \Throwable
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

        $integrityController->showTestHeadline("Tasks Module (" . Task::find()->count() . " entries)");

        foreach (Task::find()->all() as $task) {
            if ($task->task_list_id != null && !$task->list) {
                if ($integrityController->showFix("Reset task list for task" . $task->id . " with invalid task_list_setting!")) {
                    $task->updateAttributes(['task_list_id' => new Expression('NULL')]);
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
     * @throws \Throwable
     */
    public static function onMemberRemoved($event)
    {
        try {
            $tasks = Task::find()->contentContainer($event->space)->all();

            if (!empty($tasks)) {
                foreach ($tasks as $task) {
                    $taskUser = $task->getAssignedTaskUsers()->where(['task_user.user_id' => $event->user->id])->all();
                    foreach ($taskUser as $user) {
                        $user->delete();
                    }

                    $notifications = Notification::find()->where(['source_class' => Task::className(), 'source_pk' => $task->id, 'space_id' => $event->space->id])->all();
                    foreach ($notifications as $notification) {
                        $notification->delete();
                    }
                }
            }
        } catch(\Throwable $e) {
            Yii::error($e);
        }
    }


    public static function onCronRun($event)
    {
        Yii::$app->queue->push(new SendReminder());
    }

    public static function onRestApiAddRules()
    {
        /* @var RestModule $restModule */
        $restModule = Yii::$app->getModule('rest');
        $restModule->addRules([

            ['pattern' => 'tasks/', 'route' => 'tasks/rest/tasks/find', 'verb' => ['GET', 'HEAD']],
            ['pattern' => 'tasks/container/<containerId:\d+>', 'route' => 'tasks/rest/tasks/find-by-container', 'verb' => ['GET', 'HEAD']],
            ['pattern' => 'tasks/container/<containerId:\d+>', 'route' => 'tasks/rest/tasks/delete-by-container', 'verb' => 'DELETE'],

            //Task CRUD
            ['pattern' => 'tasks/container/<containerId:\d+>', 'route' => 'tasks/rest/tasks/create', 'verb' => 'POST'],
            ['pattern' => 'tasks/task/<id:\d+>', 'route' => 'tasks/rest/tasks/view', 'verb' => ['GET', 'HEAD']],
            ['pattern' => 'tasks/task/<id:\d+>', 'route' => 'tasks/rest/tasks/update', 'verb' => 'PUT'],
            ['pattern' => 'tasks/task/<id:\d+>', 'route' => 'tasks/rest/tasks/delete', 'verb' => 'DELETE'],

            //Task management
            ['pattern' => 'tasks/task/<id:\d+>/processed', 'route' => 'tasks/rest/tasks/processed', 'verb' => 'PATCH'],
            ['pattern' => 'tasks/task/<id:\d+>/revert', 'route' => 'tasks/rest/tasks/revert', 'verb' => 'PATCH'],
            ['pattern' => 'tasks/task/<id:\d+>/upload-files', 'route' => 'tasks/rest/tasks/attach-files', 'verb' => 'POST'],
            ['pattern' => 'tasks/task/<id:\d+>/remove-file/<fileId:\d+>', 'route' => 'tasks/rest/tasks/remove-file', 'verb' => 'DELETE'],

            //Task List CRUD
            ['pattern' => 'tasks/lists/container/<containerId:\d+>', 'route' => 'tasks/rest/task-list/index', 'verb' => 'GET'],
            ['pattern' => 'tasks/lists/container/<containerId:\d+>', 'route' => 'tasks/rest/task-list/create', 'verb' => 'POST'],
            ['pattern' => 'tasks/list/<id:\d+>', 'route' => 'tasks/rest/task-list/view', 'verb' => ['GET', 'HEAD']],
            ['pattern' => 'tasks/list/<id:\d+>', 'route' => 'tasks/rest/task-list/update', 'verb' => 'PUT'],
            ['pattern' => 'tasks/list/<id:\d+>', 'route' => 'tasks/rest/task-list/delete', 'verb' => 'DELETE'],

        ], 'tasks');
    }

}