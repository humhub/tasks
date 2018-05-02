<?php


namespace humhub\modules\tasks\models\lists;

use humhub\modules\tasks\models\forms\ItemDrop;

class TaskListItemDrop extends ItemDrop
{
    public $contentContainer;

    public $modelClass = TaskList::class;

    public function save()
    {
        $this->getSortableModel()->moveItemIndex($this->itemId, $this->index);
        return true;
    }

    public function getSortableModel()
    {
        if(!$this->model && !$this->modelId) {
            return new UnsortedTaskList(['contentContainer' => $this->contentContainer]);
        }

        return parent::getSortableModel();
    }
}