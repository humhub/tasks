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

namespace humhub\modules\tasks\widgets;


use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\checklist\TaskItem;
use humhub\modules\user\models\fieldtype\DateTime;
use humhub\widgets\JsWidget;

class TaskItemWidget extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'task.Item';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var Task
     */
    public $task;

    /**
     * @var TaskItemWidget
     */
    public $item;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('_item', [
            'options' => $this->getOptions(),
            'task' => $this->task,
            'item' => $this->item,
        ]);
    }

    public function getData()
    {
        $contentContainer = $this->task->content->container;

        return [
            'task-id' => $this->task->id,
            'item-id' => $this->item->id,
            'sort-order' => $this->item->sort_order,
            'can-resort' => $this->task->canResortItems(),
            'check-url' => $contentContainer->createUrl('/tasks/checklist/check-item', ['id' => $this->item->id, 'taskId' => $this->task->id]),
        ];
    }
}