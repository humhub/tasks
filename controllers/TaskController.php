<?php

namespace humhub\modules\tasks\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\tasks\models\Task;

class TaskController extends ContentContainerController
{

    public $hideSidebar = true;

    public function actionShow()
    {

        $tasks = Task::find()->contentContainer($this->contentContainer)->readable()->all();
        $completedTaskCount = Task::find()->contentContainer($this->contentContainer)->readable()->where(['task.status' => 5])->count();
        $canCreateNewTasks = $this->contentContainer->permissionManager->can(new \humhub\modules\tasks\permissions\CreateTask());
        
        
        return $this->render('show', [
            'tasks' => $tasks,
            'completedTaskCount' => $completedTaskCount,
            'contentContainer' => $this->contentContainer,
            'canCreateNewTasks' => $canCreateNewTasks
        ]);


    }

    public function actionEdit() {

        $id = (int) Yii::$app->request->get('id');
        $task = Task::find()->contentContainer($this->contentContainer)->readable()->where(['task.id' => $id])->one();

        if ($task === null) {
            // Check permission to create new task
            if (!$this->contentContainer->permissionManager->can(new \humhub\modules\tasks\permissions\CreateTask())) {
                throw new HttpException(400, 'Access denied!');
            }

            $task = new Task();
            $task->status = 1;
            $task->content->container = $this->contentContainer;
        }

        if ($task->load(Yii::$app->request->post())) {
            if ($task->validate()) {
                if ($task->save()) {
                    return $this->htmlRedirect($this->contentContainer->createUrl('show'));
                }
            }
        }

        return $this->renderAjax('edit', ['task'=>$task]);

    }


    public function actionDelete() {

        $id = (int) Yii::$app->request->get('id');

        if ($id != 0) {
            $task = Task::find()->contentContainer($this->contentContainer)->where(['task.id' => $id])->one();
            if ($task) {
                $task->delete();
            }
        }

        Yii::$app->response->format='json';
        return ['status'=>'ok'];
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
