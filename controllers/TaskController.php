<?php

namespace humhub\modules\tasks\controllers;

use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\user\models\User;
use Yii;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\forms\ItemDrop;
use humhub\modules\tasks\models\forms\TaskForm;
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
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_MEMBER, User::USERGROUP_SELF]],
            [ContentContainerControllerAccess::RULE_SPACE_ONLY => ['task-assigned-picker', 'task-responsible-picker']]
        ];
    }

    /**
     * @param int|null $id
     * @param bool $cal
     * @param bool $redirect
     * @param int|null $listId used while task creation and is ignored for edits
     * @return string
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEdit($id = null, $cal = false, $redirect = false, $listId = null)
    {
        $isNewTask = empty($id);

        if($isNewTask && !$this->contentContainer->can([CreateTask::class, ManageTasks::class])) {
            throw new HttpException(403);
        }

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
        } else if(!$taskForm->task->content->canEdit()) {
            throw new HttpException(403);
        }

        if ($taskForm->load(Yii::$app->request->post()) && $taskForm->save()) {
            if($cal) {
                return ModalClose::widget(['saved' => true]);
            } else if($redirect) {
                return $this->htmlRedirect(TaskUrl::viewTask($taskForm->task));
            }

            return $this->asJson([
                'reloadLists' => $taskForm->reloadListId,
                'reloadTask' => empty($taskForm->reloadListId) ? $taskForm->task->id : false,
                // Workaround for humhub modal bug in v1.2.5
                'output' => '<div class="modal-dialog"><div class="modal-content"></div></div></div>'
            ]);
        }

        return $this->renderAjax('edit', ['taskForm' => $taskForm]);
    }

    public function actionProceed($id, $status)
    {
        $this->forcePostRequest();
        $task = $this->getTaskById($id);

        if(!$task->state->canProceed($status)) {
            throw new HttpException(403);
        }

        return $this->asJson(['success' => $task->state->proceed($status)]);
    }

    public function actionRevert($id, $status)
    {
        $this->forcePostRequest();
        $task = $this->getTaskById($id);

        if(!$task->state->canRevert($status)) {
            throw new HttpException(403);
        }

        return $this->asJson(['success' => $task->state->revert($status)]);
    }

    public function actionTaskAssignedPicker($keyword = '')
    {
        $query = $this->getSpace()->getMembershipUser();

        return $this->asJson(UserPicker::filter([
            'query' => $query,
            'keyword' => $keyword,
            'fillUser' => true
        ]));
    }

    public function actionTaskResponsiblePicker($keyword = '')
    {
        $query = $this->getSpace()->getMembershipUser();

        return $this->asJson(UserPicker::filter([
            'keyword' => $keyword,
            'query' => $query,
            'fillUser' => true
        ]));
    }

    public function actionView($id)
    {
        $task = Task::find()->contentContainer($this->contentContainer)->where(['task.id' => $id])->one();

        if(!$task) {
            throw new HttpException(404);
        }

        if(!$task->content->canView()) {
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
            'editUrl' => TaskUrl::editTask($task, $cal),
            'canManageEntries' => $task->content->canEdit()
        ]);
    }

    public function actionDelete($id)
    {
        $this->forcePostRequest();
        $task = $this->getTaskById($id);

        if(!$task->content->canEdit()) {
            throw new HttpException(403);
        }

        $task->delete();

        return $this->asJson([
            'success' => true
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function actionExtend($id)
    {
        $task = $this->getTaskById($id);

        if( !$task->content->canView() && !$task->schedule->canRequestExtension() ) {
            throw new HttpException(401, Yii::t('TasksModule.controller', 'You have insufficient permissions to perform that operation!'));
        }

        if ($task->schedule->hasRequestedExtension()) {
            $this->view->error(Yii::t('TasksModule.controller', 'Already requested'));
        } else {
            $task->schedule->sendExtensionRequest();
            $task->updateAttributes(['request_sent' => 1]);
            $this->view->success(Yii::t('TasksModule.controller', 'Request sent'));
        }

        return $this->htmlRedirect(TaskUrl::viewTask($task));
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

            return $this->asJson(['success' => true, 'items' => $result]);
        }

        return $this->asJson(['success' => false]);
    }
}
