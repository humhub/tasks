<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: davidborn
 */

namespace humhub\modules\tasks\widgets;


use humhub\modules\tasks\models\Task;
use humhub\widgets\JsWidget;
use yii\helpers\Url;

class TaskItemList extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'task.ItemList';

    /**
     * @inheritdoc
     */
    public $id = 'task-items';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var Task
     */
    public $task;

    /**
     * @var Task
     */
    public $canEdit;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if(!count($this->task->items)) {
            return '';
        }

        return $this->render('taskItemList', [
            'options' => $this->getOptions(),
            'items' => $this->task->items,
            'task' => $this->task,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $contentContainer = $this->task->content->container;
        return [
            'task-id' => $this->task->id,
            'drop-url' => $contentContainer->createUrl('/tasks/task/drop', ['taskId' => $this->task->id]),
            'can-edit' => $this->canEdit,
            'can-resort' => $this->task->canResortItems()
        ];
    }


}