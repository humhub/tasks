<?php

namespace module\tasks;

use Yii;
use humhub\modules\user\models\User;
use module\tasks\models\Task;
use yii\helpers\Url;

class Module extends \humhub\components\Module
{

    public function behaviors()
    {
        return [
            \humhub\modules\space\behaviors\SpaceModule::className(),
        ];
    }

    public function disable()
    {
        if (parent::disable()) {

            foreach (Task::find()->all() as $task) {
                $task->delete();
            }

            return true;
        }

        return false;
    }

    public function disableSpaceModule(Space $space)
    {
        foreach (Task::find()->contentContainer($space)->all() as $task) {
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
        if ($space->isModuleEnabled('tasks')) {
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

}
