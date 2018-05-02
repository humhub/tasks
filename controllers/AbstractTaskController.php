<?php


namespace humhub\modules\tasks\controllers;


use humhub\modules\content\components\ContentContainerController;
use humhub\modules\tasks\models\Task;
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

}