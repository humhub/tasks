<?php

namespace humhub\modules\tasks;

use Yii;
use yii\helpers\Url;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\TaskUser;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;

class Module extends ContentContainerModule
{

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (Task::find()->all() as $task) {
            $task->delete();
        }

        parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (Task::find()->contentContainer($container)->all() as $task) {
            $task->delete();
        }
    }

    public static function onUserDelete($event)
    {

        foreach (TaskUser::findAll(array('created_by' => $event->sender->id)) as $task) {
            $task->delete();
        }
        foreach (TaskUser::findAll(array('user_id' => $event->sender->id)) as $task) {
            $task->delete();
        }

        return true;
    }

    public static function onSpaceMenuInit($event)
    {
        $space = $event->sender->space;
        if ($space->isModuleEnabled('tasks') && $space->isMember()) {
            $event->sender->addItem(array(
                'label' => Yii::t('TasksModule.base', 'Tasks'),
                'group' => 'modules',
                'url' => $space->createUrl('/tasks/task/show'),
                'icon' => '<i class="fa fa-check-square"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'tasks'),
            ));
        }
    }

    public static function onDashboardSidebarInit($event)
    {
        $event->sender->addWidget(widgets\MyTasks::className(), array(), array('sortOrder' => 600));
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer instanceof \humhub\modules\space\models\Space) {
            return [
                new permissions\CreateTask(),
            ];
        }

        return [];
    }

}
