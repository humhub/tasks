<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets\lists;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\lists\TaskListInterface;
use humhub\widgets\JsWidget;
use Yii;

class TaskListWidget extends JsWidget
{
    /**
     * @inheritdocs
     */
    public $init = true;

    /**
     * @inheritdocs
     */
    public $jsWidget = 'task.list.TaskList';

    /**
     * @var TaskListInterface
     */
    public $list;

    /**
     * @var Task[] | null
     */
    public $tasks;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @inheritdocs
     */
    public function init()
    {
        $this->contentContainer = Yii::$app->controller->contentContainer;
        parent::init();
    }

    /**
     * @inheritdocs
     */
    public function run()
    {
        $completedTasksQuery = $this->getCompletedTasksQuery();
        $countQuery = clone $completedTasksQuery;
        $tasks = $this->getNonCompletedTasks();

        return $this->render('taskList', [
            'title' => $this->getTitle(),
            'tasks' => $tasks,
            'completedTasks' => $completedTasksQuery->limit(3)->all(),
            'completedTasksCount' => $countQuery->count(),
            'options' => $this->getOptions(),
            'color' => $this->list->getColor(),
            'editListUrl' => $this->getEditListUrl(),
            'addTaskUrl'  => $this->getAddTaskUrl(),
            'showMoreCompletedUrl'  => $this->getShowMoreCompletedUrl(),
        ]);
    }


    protected function getNonCompletedTasks()
    {
        return $this->list->getNonCompletedTasks()->all();
    }

    protected function getCompletedTasksQuery()
    {
        return $this->list->getCompletedTasks();
    }

    protected function getEditListUrl()
    {
        return $this->contentContainer->createUrl('/tasks/list/edit', ['id' => $this->list->getId()]);
    }

    protected function getShowMoreCompletedUrl()
    {
        return $this->contentContainer->createUrl('/tasks/list/show-more-completed', ['id' => $this->list->getId()]);
    }

    protected function getAddTaskUrl()
    {
        return $this->contentContainer->createUrl('/tasks/task/edit', ['listId' => $this->list->getId()]);
    }

    protected function getTitle()
    {
        return $this->list->getTitle();
    }

    public function getData()
    {
        return [
            'task-list-id' => $this->list->getId(),
            'reload-url' => $this->contentContainer->createUrl('/tasks/list/load-ajax', ['id' =>  $this->list->getId()]),
            'drop-task-url' => $this->contentContainer->createUrl('/tasks/list/drop-task')
        ];
    }
}