<?php

namespace humhub\modules\tasks\models\lists;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\models\forms\ItemDrop;

class TaskListRootItemDrop extends ItemDrop
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    public function getSortableModel()
    {
        return new TaskListRoot(['contentContainer' => $this->contentContainer]);
    }

    public function save()
    {
        if (!TaskList::findById($this->itemId, $this->contentContainer)) {
            return false;
        }

        return parent::save();
    }
}
