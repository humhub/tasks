<?php

namespace humhub\modules\tasks\models\checklist;

use Yii;
use yii\base\Component;
use humhub\modules\tasks\models\Sortable;
use humhub\modules\tasks\models\Task;

/**
 * Class TaskCheckList
 *
 * @todo change base class back to BaseObject after v1.3 stable
 * @package humhub\modules\tasks\models\checklist
 */
class TaskCheckList extends Component implements Sortable
{

    /**
     * @var Task
     */
    public $task;

    /**
     * Resets items
     *
     * @throws \yii\db\Exception
     */
    public function resetItems()
    {
        TaskItem::updateAll(['completed' => 0], ['task_id' => $this->task->id]);
    }

    public function checkAll()
    {
        TaskItem::updateAll(['completed' => 1], ['task_id' => $this->task->id]);
    }

    public function moveItemIndex($itemId, $newIndex)
    {
        $moveItem = TaskItem::findOne(['id' => $itemId]);
        $items = $this->task->items;

        // make sure no invalid index is given
        if($moveItem->sort_order === $newIndex) {
            return;
        } else if($newIndex < 0) {
            $newIndex = 0;
        } else if($newIndex >= count($items)) {
            $newIndex = count($items) -1;
        }

        array_splice($items, $moveItem->sort_order, 1);
        array_splice($items, $newIndex, 0, [$moveItem]);

        foreach ($items as $index => $item) {
            $item->updateAttributes(['sort_order' => $index]);
        }

        $this->task->refresh();
    }

    public function afterSave($insert)
    {
        if(!$insert) {
            $this->updateItems();
        }
    }

    public function updateItems()
    {
        if (!isset($this->task->editItems)) {
            return;
        }

        foreach ($this->task->items as $item) {
            /** @var $item TaskItem */
            if (!array_key_exists($item->id, $this->task->editItems)) {
                $item->delete();
            } else if ($item->title !== $this->task->editItems[$item->id]) {
                $item->title = $this->task->editItems[$item->id];
                $item->update();
            }
        }
    }

    public function canCheckItem($user = null)
    {
        return $this->task->isTaskResponsible($user) || $this->task->isTaskAssigned($user) || $this->task->canProcess($user);
    }

}