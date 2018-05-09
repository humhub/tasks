<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: Buddha
 * Date: 21.06.2017
 * Time: 13:59
 */

namespace humhub\modules\tasks\widgets\checklist;


use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\checklist\TaskItem;
use humhub\modules\user\models\fieldtype\DateTime;
use humhub\widgets\JsWidget;

class TaskChecklistItem extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'task.checklist.Item';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var Task
     */
    public $task;

    /**
     * @var TaskChecklistItem
     */
    public $item;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('taskChecklistItem', [
            'options' => $this->getOptions(),
            'task' => $this->task,
            'item' => $this->item,
        ]);
    }

    public function getData()
    {
        return [
            'task-id' => $this->task->id,
            'item-id' => $this->item->id,
            'sort-order' => $this->item->sort_order,
            'can-resort' => $this->task->canResortItems(),
            'check-url' => TaskUrl::checkChecklistItem($this->task, $this->item),
        ];
    }
}