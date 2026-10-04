<?php

namespace humhub\modules\tasks\models\lists;

use humhub\modules\tasks\models\forms\ItemDrop;
use humhub\modules\tasks\models\Task;

class TaskListItemDrop extends ItemDrop
{
    public $contentContainer;

    public $modelClass = TaskList::class;

    public function save()
    {
        $task = Task::find()
            ->contentContainer($this->contentContainer)
            ->readable()
            ->andWhere(['task.id' => $this->itemId])
            ->one();

        if (!$task || !$task->content->canEdit()) {
            return false;
        }

        $sortableModel = $this->getSortableModel();
        if (!$sortableModel) {
            return false;
        }

        $sortableModel->moveItemIndex($task->id, $this->index);
        return true;
    }

    public function getSortableModel()
    {
        if (!$this->model) {
            $this->model = $this->modelId
                ? TaskList::findById($this->modelId, $this->contentContainer)
                : new UnsortedTaskList(['contentContainer' => $this->contentContainer]);
        }

        return $this->model;
    }
}
