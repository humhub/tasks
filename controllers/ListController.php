<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\controllers;


use humhub\modules\content\components\ContentContainerController;

use humhub\modules\tasks\models\lists\TaskListItemDrop;
use humhub\modules\tasks\models\lists\TaskListRootItemDrop;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\models\lists\TaskListInterface;
use humhub\modules\tasks\models\lists\UnsortedTaskList;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\tasks\widgets\lists\TaskListDetails;
use humhub\modules\tasks\widgets\lists\TaskListItem;
use humhub\modules\tasks\widgets\lists\TaskListWidget;
use humhub\modules\tasks\widgets\lists\UnsortedTaskListWidget;
use humhub\widgets\ModalClose;
use Yii;
use yii\web\HttpException;

class ListController extends ContentContainerController
{
    public function getAccessRules()
    {
        return [
            ['permission' => CreateTask::class, 'actions' => ['edit']],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'contentContainer' => $this->contentContainer,
            'canEdit' =>  $this->contentContainer->can(ManageTasks::class),
            'taskLists' => TaskList::findOverviewLists($this->contentContainer)->all()]);
    }

    public function actionEdit($id = null)
    {
        $taskList = ($id) ? TaskList::findById($id, $this->contentContainer) : new TaskList($this->contentContainer);

        if(!$taskList) {
            throw new HttpException(404);
        }

        if($taskList->load(Yii::$app->request->post()) && $taskList->save()) {
            return ModalClose::widget();
        }

        return $this->renderAjax('edit', [
            'model' => $taskList,
        ]);
    }

    public function actionLoadTaskDetails($id)
    {
        return TaskListDetails::widget(['task' => $this->getTaskById($id)]);
    }

    public function actionDropTask()
    {
        $model = new TaskListItemDrop(['contentContainer' => $this->contentContainer]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson([
                'success' => true
            ]);
        }

        return $this->asJson(['success' => false]);
    }

    public function actionDropTaskList()
    {
        $model = new TaskListRootItemDrop(['contentContainer' => $this->contentContainer]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson(['success' => true]);
        }

        return $this->asJson(['success' => false]);
    }

    public function actionShowMoreCompleted($id = null, $offset)
    {
        /** @var $taskList TaskListInterface */
        $taskList = $id ? TaskList::findById($id, $this->contentContainer) :  new UnsortedTaskList(['contentContainer' => $this->contentContainer]);

        if(!$taskList) {
            throw new HttpException(404);
        }

        $tasks = $taskList->getShowMoreCompletedTasks($offset, 10);
        $completedTaskCount = $taskList->getCompletedTasks()->count();

        $result = [];
        foreach ($tasks as $task) {
            $result[] = TaskListItem::widget(['task' => $task]);
        }


        $remainingCount = $completedTaskCount - ($offset + count($result));

        return $this->asJson([
            'tasks' => $result,
            'remainingCount' => $remainingCount,
            'showMoreMessage' => Yii::t('TasksModule.base','Show {count} more completed {n,plural,=1{task} other{tasks}}', ['n' => $remainingCount, 'count' => $remainingCount])
        ]);
    }

    public function actionLoadAjax($id = null)
    {
        if(!$id) {
            return UnsortedTaskListWidget::widget();
        }

        $taskList = TaskList::findById($id, $this->contentContainer);
        if(!$taskList) {
            throw new HttpException(404);
        }

        return TaskListWidget::widget(['list' => $taskList, 'contentContainer' => $this->contentContainer]);
    }

    public function actionLoadAjaxTask($id)
    {
        return TaskListItem::widget(['task' => $this->getTaskById($id)]);
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