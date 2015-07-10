<?php

namespace module\tasks\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use module\tasks\models\Task;

class TaskController extends ContentContainerController
{

    public function actions()
    {
        return array(
            'stream' => array(
                'class' => \module\tasks\components\StreamAction::className(),
                'mode' => \module\tasks\components\StreamAction::MODE_NORMAL,
                'contentContainer' => $this->contentContainer
            ),
        );
    }

    public function actionShow()
    {
        return $this->render('show', ['contentContainer' => $this->contentContainer]);
    }

    public function actionCreate()
    {
        $task = new Task();
        $task->title = Yii::$app->request->post('title');
        $task->max_users = Yii::$app->request->post('max_users', 1);
        $deadline = Yii::$app->request->post('deadline');
        if ($deadline != "") {
            $deadline = Yii::$app->formatter->asDateTime($deadline, 'php:Y-m-d H:i:s');
        }
        $task->deadline = $deadline;
        $task->preassignedUsers = Yii::$app->request->post('preassignedUsers');
        $task->status = Task::STATUS_OPEN;

        return \module\tasks\widgets\WallCreateForm::create($task);
    }

    public function actionAssign()
    {
        $task = $this->getTaskById((int) Yii::$app->request->get('taskId'));
        $task->assignUser();
        return $this->renderTask($task);
    }

    public function actionUnAssign()
    {
        $task = $this->getTaskById((int) Yii::$app->request->get('taskId'));
        $task->unassignUser();
        return $this->renderTask($task);
    }

    public function actionChangePercent()
    {
        $task = $this->getTaskById((int) Yii::$app->request->get('taskId'));
        $task->changePercent((int) Yii::$app->request->get('percent'));
        return $this->renderTask($task);
    }

    public function actionChangeStatus()
    {
        $task = $this->getTaskById((int) Yii::$app->request->get('taskId'));
        $status = (int) Yii::$app->request->get('status');
        $task->changeStatus($status);
        return $this->renderTask($task);
    }

    protected function renderTask($task)
    {
        Yii::$app->response->format = 'json';
        $json = array();
        $json['output'] = $this->renderAjaxContent($task->getWallOut());
        $json['wallEntryId'] = $task->content->getFirstWallEntryId();
        return $json;
    }

    protected function getTaskById($id)
    {
        $task = Task::find()->contentContainer($this->contentContainer)->readable()->where(['task.id' => $id])->one();
        if ($task === null) {
            throw new HttpException(404, "Could not load task!");
        }
        return $task;
    }

}
