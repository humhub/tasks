<?php

namespace humhub\modules\tasks\controllers;

use Yii;
use yii\web\HttpException;
use humhub\modules\tasks\models\forms\ItemDrop;
use humhub\modules\tasks\models\forms\TaskForm;
use humhub\modules\tasks\models\user\TaskUser;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\user\models\UserPicker;
use humhub\widgets\ModalClose;
use humhub\modules\tasks\models\Task;

class TaskController extends AbstractTaskController
{

    public $hideSidebar = true;

    public function getAccessRules()
    {
        return [
            ['permissions' => [ManageTasks::class, CreateTask::class], 'actions' => ['edit', 'delete']]
        ];
    }

    public function actionProceed($id, $status)
    {
        $this->forcePostRequest();
        $task = $this->getTaskById($id);

        if(!$task->state->canProceed($status)) {
            throw new HttpException(403);
        }

        return $this->asJson([
            'success' => $task->state->proceed($status)
        ]);
    }

    public function actionRevert($id, $status)
    {
        $this->forcePostRequest();
        $task = $this->getTaskById($id);

        if(!$task->state->canRevert($status)) {
            throw new HttpException(403);
        }

        return $this->asJson([
            'success' => $task->state->revert($status)
        ]);
    }

    public function actionTaskAssignedPicker($id = null, $keyword)
    {
        if($id) {
            $subQuery = TaskUser::find()->where(['task_user.task_id' => $id, 'task_user.user_type' => Task::USER_ASSIGNED])
                ->andWhere('task_user.user_id=user.id');
            $query = $this->getSpace()->getMembershipUser()->where(['not exists', $subQuery]);
        } else {
            $query = $this->getSpace()->getMembershipUser();
        }

        return $this->asJson(UserPicker::filter([
            'keyword' => $keyword,
            'query' => $query,
            'fillUser' => true
        ]));
    }

    public function actionTaskResponsiblePicker($id = null, $keyword)
    {
        if($id) {
            $subQuery = TaskUser::find()->where(['task_user.task_id' => $id, 'task_user.user_type' => Task::USER_RESPONSIBLE])
                ->andWhere('task_user.user_id=user.id');
            $query = $this->getSpace()->getMembershipUser()->where(['not exists', $subQuery]);
        } else {
            $query = $this->getSpace()->getMembershipUser();
        }

        return $this->asJson(UserPicker::filter([
            'keyword' => $keyword,
            'query' => $query,
            'fillUser' => true
        ]));
    }

    /**
     * @param int|null $id
     * @param bool $cal
     * @param int|null $listId used while task creation and is ignored for edits
     * @return string
     * @throws HttpException
     */
    public function actionEdit($id = null, $cal = false, $redirect = false, $listId = null)
    {
        $isNewTask = empty($id);

        if ($isNewTask) {
            $taskForm = new TaskForm(['cal' => $cal, 'taskListId' =>  $listId]);
            $taskForm->createNew($this->contentContainer);
        } else {
            $taskForm = new TaskForm([
                'task' => Task::find()->contentContainer($this->contentContainer)->where(['task.id' => $id])->one(),
                'cal' => $cal,
                'redirect' => $redirect,
                'taskListId' => $listId
            ]);
        }

        if(!$taskForm->task) {
            throw new HttpException(404);
        }

        if ($taskForm->load(Yii::$app->request->post()) && $taskForm->save()) {
            if($cal) {
                return ModalClose::widget(['saved' => true]);
            } else if($redirect) {
                return $this->htmlRedirect($this->contentContainer->createUrl('view', ['id' => $taskForm->task->id]));
            }

            return $this->asJson([
                'reloadLists' => $taskForm->reloadListId,
                // Workaround for humhub modal bug in v1.2.5
                'output' => '<div class="modal-dialog"><div class="modal-content"></div></div></div>'
            ]);
        }

        return $this->renderAjax('edit', ['taskForm' => $taskForm]);
    }

    public function actionView($id)
    {
        $task = Task::find()->contentContainer($this->contentContainer)->where(['task.id' => $id])->one();

        if(!$task) {
            throw new HttpException(404);
        }

        if( !$task->content->canView() && !($task->isTaskAssigned() || $task->isTaskResponsible()) ) {
            throw new HttpException(403);
        }

        return $this->render("task", [
            'task' => $task,
            'contentContainer' => $this->contentContainer
        ]);
    }

    public function actionModal($id, $cal)
    {
        $task = $this->getTaskById($id);

        if(!$task->content->canView()) {
            throw new HttpException(403);
        }

        return $this->renderAjax('modal', [
            'task' => $task,
            'editUrl' => $this->contentContainer->createUrl('/tasks/task/edit', ['id' => $task->id, 'cal' => $cal]),
            'canManageEntries' => $task->content->canEdit()
        ]);
    }

    public function actionDelete()
    {
        $id = (int) Yii::$app->request->get('id');

        if ($id != 0) {
            $task = Task::find()->contentContainer($this->contentContainer)->where(['task.id' => $id])->one();
            if ($task) {
                $task->delete();
            }
        }

        Yii::$app->response->format = 'json';
        return ['status' => 'ok'];
    }

    /**
     * @param $id
     * @return string
     * @throws HttpException
     * @throws \yii\base\Exception
     */
    public function actionExtend($id)
    {
        $task = Task::find()->contentContainer($this->contentContainer)->where(['task.id' => $id])->one();

        if(!$task) {
            throw new HttpException(404);
        }

        $taskAssigned = $task->getAssignedTaskUsers()->where(['task_user.user_id' => Yii::$app->user->id])->one();
        if(!$taskAssigned) {
            throw new HttpException(404);
        }

        if( !$task->content->canView() && !$task->schedule->canRequestExtension() ) {
            throw new HttpException(401, Yii::t('TasksModule.controller', 'You have insufficient permissions to perform that operation!'));
        }

        if ($task->schedule->hasRequestedExtension()) {
            $this->view->error(Yii::t('TasksModule.controller', 'Already requested'));
        }
        else {
            $task->schedule->sendExtensionRequest();
            $task->updateAttributes(['request_sent' => 1]);
            $this->view->success(Yii::t('TasksModule.controller', 'Request sent'));
        }

        return $this->htmlRedirect($this->contentContainer->createUrl('view', [
            'id' => $task->id,
        ]));

    }

    public function actionDrop($taskId)
    {
        $dropModel = new ItemDrop(['modelClass' => Task::class, 'modelId' => $taskId]);

        if ($dropModel->load(Yii::$app->request->post()) && $dropModel->save()) {
            $result = [];
            foreach ($dropModel->model->items as $item) {
                $result[$item->id] = [
                    'sortOrder' => $item->sort_order,
                    'checked' => $item->completed,
                    'statChanged' => false,
                ];
            }

            return $this->asJson([
                'success' => true,
                'items' => $result
            ]);
        }

        return $this->asJson(['success' => false]);
    }
}
