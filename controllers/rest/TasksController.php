<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2019 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\controllers\rest;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\rest\components\BaseContentController;
use humhub\modules\tasks\helpers\RestDefinitions;
use humhub\modules\tasks\models\forms\TaskForm;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use Yii;

class TasksController extends BaseContentController
{
    public static $moduleId = 'tasks';

    /**
     * {@inheritdoc}
     */
    public function getContentActiveRecordClass()
    {
        return Task::class;
    }

    /**
     * {@inheritdoc}
     */
    public function returnContentDefinition(ContentActiveRecord $contentRecord)
    {
        /** @var Task $contentRecord */
        return RestDefinitions::getTask($contentRecord);
    }

    private function saveTask(TaskForm $taskForm): bool
    {
        $data = Yii::$app->request->bodyParams;
        return $taskForm->load($data)
            && $taskForm->save()
            && (!method_exists($this, 'updateContent') || $this->updateContent($taskForm->task, $data));
    }

    public function actionCreate($containerId)
    {
        $containerRecord = ContentContainer::findOne(['id' => $containerId]);
        if ($containerRecord === null) {
            return $this->returnError(404, 'Content container not found!');
        }
        /** @var ContentContainerActiveRecord $container */
        $container = $containerRecord->getPolymorphicRelation();

        if (! in_array($container::class, Yii::$app->getModule('tasks')->getContentContainerTypes())
            || ! $container->permissionManager->can([CreateTask::class, ManageTasks::class])) {
            return $this->returnError(403, 'You are not allowed to create task!');
        }

        $taskParams = Yii::$app->request->post('Task', []);

        $taskForm = new TaskForm([
            'cal' => $taskParams['cal_mode'] ?? null,
            'taskListId' => $taskParams['task_list_id'] ?? null,
            'dateFormat' => 'php:Y-m-d',
            'timeFormat' => 'php:H:i',
        ]);
        $taskForm->createNew($container);

        if (! $taskForm->task->content->canEdit()) {
            return $this->returnError(403, 'You are not allowed to edit this task!');
        }

        if ($this->saveTask($taskForm)) {
            return $this->returnContentDefinition(Task::findOne(['id' => $taskForm->task->id]));
        }

        if ($taskForm->hasErrors() || $taskForm->task->hasErrors()) {
            return $this->returnError(422, 'Validation failed', [
                'taskForm' => $taskForm->getErrors(),
                'task' => $taskForm->task->getErrors(),
            ]);
        } else {
            Yii::error('Could not create validated task.', 'api');
            return $this->returnError(500, 'Internal error while save task!');
        }
    }

    public function actionUpdate($id)
    {
        $task = Task::findOne(['id' => $id]);
        if (! $task) {
            return $this->returnError(404, 'Task not found!');
        }

        $taskForm = new TaskForm([
            'task' => $task,
            'dateFormat' => 'php:Y-m-d',
            'timeFormat' => 'php:H:i',
        ]);

        if (!$taskForm->task->content->canEdit()) {
            return $this->returnError(403, 'You are not allowed to update this task!');
        }

        if ($this->saveTask($taskForm)) {
            return $this->returnContentDefinition(Task::findOne(['id' => $taskForm->task->id]));
        }

        if ($taskForm->hasErrors() || $taskForm->task->hasErrors()) {
            return $this->returnError(422, 'Validation failed', [
                'taskForm' => $taskForm->getErrors(),
                'task' => $taskForm->task->getErrors(),
            ]);
        } else {
            Yii::error('Could not update validated task.', 'api');
            return $this->returnError(500, 'Internal error while save task!');
        }
    }

    public function actionProcessed($id)
    {
        $task = Task::findOne(['id' => $id]);
        if (! $task) {
            return $this->returnError(404, 'Task not found!');
        }

        $status = Yii::$app->request->post('status', null);

        if (!$task->state->canProceed($status)) {
            return $this->returnError(403, 'You are not allowed to change status of this task!');
        }

        if ($task->state->proceed($status)) {
            return $this->returnSuccess('Status successfully changed.');
        } else {
            return $this->returnError(400, 'Invalid status!');
        }
    }

    public function actionRevert($id)
    {
        $task = Task::findOne(['id' => $id]);
        if (! $task) {
            return $this->returnError(404, 'Task not found!');
        }

        if (!$task->state->canRevert(Task::STATUS_PENDING)) {
            return $this->returnError(403, 'You are not allowed to revert this task!');
        }

        if ($task->state->revert(Task::STATUS_PENDING)) {
            return $this->returnSuccess('Task successfully reverted.');
        } else {
            return $this->returnError(400, 'Invalid status!');
        }
    }
}
