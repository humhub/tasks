<?php

namespace humhub\modules\tasks;

use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\permissions\ProcessUnassignedTasks;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use Yii;
use yii\helpers\Url;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\user\TaskUser;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;

class Module extends ContentContainerModule
{
    /**
     * @inheritdoc
     */
    public $resourcesPath = 'resources';

    /**
     * @inheritdoc
     */
    public $searchPaginationSize = 30;

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::class,
            User::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return TaskUrl::toConfig();
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (Task::find()->all() as $task) {
            $task->delete();
        }

        foreach (TaskList::find()->all() as $taskList) {
            $taskList->delete();
        }

        TaskList::deleteByModule();
        parent::disable();
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (Task::find()->contentContainer($container)->all() as $task) {
            $task->delete();
        }

        foreach (TaskList::findByContainer($container)->all() as $taskList) {
            $taskList->delete();
        }

        TaskList::deleteByModule($container);
        parent::disableContentContainer($container);
    }

    public static function onUserDelete($event)
    {
        foreach (TaskUser::findAll(['created_by' => $event->sender->id]) as $task) {
            $task->delete();
        }

        foreach (TaskUser::findAll(['user_id' => $event->sender->id]) as $task) {
            $task->delete();
        }

        return true;
    }

    public static function onDashboardSidebarInit($event)
    {
        $event->sender->addWidget(widgets\MyTasks::class, [], ['sortOrder' => 600]);
    }

    /**
     * @inheritdoc
     */
    public function getContainerPermissions($contentContainer = null)
    {
        return [
            new CreateTask(),
            new ManageTasks(),
            new ProcessUnassignedTasks(),
        ];
    }

}
