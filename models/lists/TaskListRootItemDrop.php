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
}