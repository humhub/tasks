<?php


namespace humhub\modules\tasks\controllers;


use humhub\modules\content\components\ContentContainerController;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use yii\web\HttpException;

abstract class AbstractTaskController extends ContentContainerController
{
    protected function getTaskById($id)
    {
        $task = Task::find()->contentContainer($this->contentContainer)->readable()->where(['task.id' => $id])->one();
        if ($task === null) {
            throw new HttpException(404, "Could not load task!");
        }
        return $task;
    }

    protected function canCreateTask()
    {
        return $this->contentContainer->can(CreateTask::class);
    }

    protected function canManageTasks()
    {
        return $this->contentContainer->can(ManageTasks::class);
    }

}