<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\controllers;


use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\lists\TaskListItemDrop;
use humhub\modules\tasks\models\lists\TaskListRootItemDrop;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\models\lists\TaskListInterface;
use humhub\modules\tasks\models\lists\UnsortedTaskList;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\tasks\widgets\lists\CompletedTaskListItem;
use humhub\modules\tasks\widgets\lists\CompletedTaskListView;
use humhub\modules\tasks\widgets\lists\TaskListDetails;
use humhub\modules\tasks\widgets\lists\TaskListItem;
use humhub\modules\tasks\widgets\lists\TaskListWidget;
use humhub\modules\tasks\widgets\lists\UnsortedTaskListWidget;
use humhub\modules\user\models\User;
use humhub\widgets\ModalClose;
use Yii;
use yii\web\HttpException;

class ListController extends AbstractTaskController
{
    public function getAccessRules()
    {
        return [
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_MEMBER, User::USERGROUP_SELF]],
            ['permission' => ManageTasks::class, 'actions' => ['edit', 'delete', 'drop-task', 'drop-task-list']],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'contentContainer' => $this->contentContainer,
            'canManage' =>  $this->canManageTasks(),
            'canCreate' => $this->canCreateTask(),
            'taskLists' => TaskList::findOverviewLists($this->contentContainer)->all()]);
    }

    public function actionEdit($id = null)
    {
        $taskList = ($id) ? $this->getTaskListById($id) : new TaskList($this->contentContainer);

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

    public function actionDelete($id)
    {
        $this->forcePostRequest();
        $this->getTaskListById($id)->delete();
        return $this->asJson(['success' => true]);
    }

    public function actionLoadClosedLists()
    {
        return CompletedTaskListView::widget(['contentContainer' => $this->contentContainer]);
    }

    public function actionLoadCompleted($id)
    {
        return CompletedTaskListItem::widget(['contentContainer' => $this->contentContainer, 'taskList' =>  $this->getTaskListById($id)]);
    }

    public function getTaskListById($id)
    {
        $taskList = TaskList::findById($id, $this->contentContainer);

        if(!$taskList) {
            throw new HttpException(404);
        }

        return $taskList;
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
            return UnsortedTaskListWidget::widget(['canManage' =>  $this->canManageTasks(), 'canCreate' => $this->canCreateTask()]);
        }

        $taskList = TaskList::findById($id, $this->contentContainer);
        if(!$taskList) {
            throw new HttpException(404);
        }

        return TaskListWidget::widget(['list' => $taskList, 'canManage' =>  $this->canManageTasks(), 'canCreate' => $this->canCreateTask()]);
    }

    public function actionLoadAjaxTask($id)
    {
        return TaskListItem::widget(['task' => $this->getTaskById($id), 'details' => true]);
    }
}