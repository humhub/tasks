<?php

namespace humhub\modules\tasks;

use humhub\components\console\Application as ConsoleApplication;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\forms\ConfigureContainerForm;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\permissions\ProcessUnassignedTasks;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use Yii;
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
    public $searchPaginationSize = 30;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app instanceof ConsoleApplication) {
            // Prevents the Yii HelpCommand from crawling all web controllers and possibly throwing errors at REST endpoints if the REST module is not available.
            $this->controllerNamespace = 'tasks/commands';
        }
    }

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
    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        return TaskUrl::toContainerConfig($container);
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
            $task->hardDelete();
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
            $task->hardDelete();
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

    /**
     * @inheritdoc
     */
    public function getContentClasses(): array
    {
        return [Task::class];
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return Yii::t('TasksModule.base', 'Tasks');
    }

    public function getContentHiddenGlobalDefault(): bool
    {
        return $this->settings->get('contentHiddenGlobalDefault', false);
    }

    public function getContentHiddenDefault(ContentContainerActiveRecord $contentContainer): bool
    {
        return (new ConfigureContainerForm(['contentContainer' => $contentContainer]))->contentHiddenDefault;
    }
}
