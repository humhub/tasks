<?php

namespace humhub\modules\tasks\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\tasks\components\ActiveQueryTask;
use humhub\modules\tasks\models\Task;

class TaskController extends ContentContainerController
{

    public $hideSidebar = true;

    public function actionShow()
    {
        $this->subLayout = '@tasks/views/task/showLayout.php';

        $filters = self::getFiltersByRequest(true);
        if (empty($filters)) {
            $filters = \humhub\modules\tasks\widgets\FilterSnippet::getDefaultFilter();
        }

        $tasks = Task::find()->readable();
        $tasks->applyTaskFilters($filters);

        $countQuery = clone $tasks;
        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $this->module->paginationSize]);
        $tasks->offset($pagination->offset)->limit($pagination->limit);

        if (Yii::$app->request->isAjax) {
            $output = '';
            foreach ($tasks->all() as $task) {
                $output .= \humhub\modules\tasks\widgets\Task::widget(['model' => $task]);
            }
            $output .= \humhub\modules\tasks\widgets\MoreButton::widget(['pagination' => $pagination]);
            return $output;
        }

        return $this->render('show', [
                    'tasks' => $tasks->all(),
                    'pagination' => $pagination,
        ]);
    }

    public function actionEdit()
    {

        $id = (int) Yii::$app->request->get('id');
        $task = $this->getTaskById($id);

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
            Yii::$app->response->format = 'json';
            $result = [];
            if ($task->save()) {
                $result['success'] = true;
                $result['task'] = $this->getTaskArrayByFilter($task, Yii::$app->request->post('filters'));
            } else {
                $result['success'] = false;
                $result['output'] = $this->renderAjax('edit', ['task' => $task]);
            }

            return $result;
        }

        return $this->renderAjax('edit', ['task' => $task]);
    }

    public function actionDelete()
    {
        Yii::$app->response->format = 'json';
        $success = false;
        $id = (int) Yii::$app->request->get('id');

        $task = $this->getTaskById($id);
        if ($task && $task->delete()) {
            $success = true;
        }

        return [
            'success' => $success,
            'id' => $task->id
        ];
    }

    public function actionChangeStatus()
    {
        Yii::$app->response->format = 'json';

        $task = $this->getTaskById((int) Yii::$app->request->get('taskId'));
        if ($task === null) {
            throw new HttpException(404, "Could not load task!");
        }

        $status = (int) Yii::$app->request->get('status');
        $task->changeStatus($status);

        $result = $this->getTaskArrayByFilter($task, Yii::$app->request->get('filters'));
        $result['success'] = true;

        return $result;
    }

    protected function renderTask($task)
    {
        Yii::$app->response->format = 'json';
        $json = array();
        $json['output'] = $this->renderAjaxContent($task->getWallOut());
        $json['wallEntryId'] = $task->content->getFirstWallEntryId();
        return $json;
    }

    protected function getTaskById($id, $filters = null)
    {
        $task = Task::find()->userRelated([ActiveQueryTask::USER_RELATED_SCOPE_SPACES])->readable()->where(['task.id' => $id])->one();
        return $task;
    }

    protected function getTaskArrayByFilter($task, $filters = [])
    {
        if (!is_array($filters)) {
            try {
                $filters = \yii\helpers\Json::decode($filters);
            } catch (Exception $ex) {
                $filters = [];
            }
        }

        $taskReloaded = Task::find()->where(['task.id' => $task->id])->applyTaskFilters($filters)->one();
        if ($taskReloaded !== null) {
            return [
                'id' => $taskReloaded->id,
                'output' => \humhub\modules\tasks\widgets\Task::widget(['model' => $taskReloaded])
            ];
        }

        return [
            'id' => $task->id,
        ];
    }

    protected static function getFiltersByRequest($store = false)
    {
        $filters = [];

        $reqFilters = Yii::$app->request->post('filters', Yii::$app->request->get('filters'));
        if ($reqFilters != '') {
            try {
                $filters = \yii\helpers\Json::decode($reqFilters);
                if ($store) {
                    Yii::$app->getModule('tasks')->settings->user()->set('filters', $reqFilters);
                }
            } catch (\Exception $ex) {
                $filters = [];
            }
        }

        return $filters;
    }

}
