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

namespace humhub\modules\tasks\widgets\checklist;


use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use humhub\widgets\JsWidget;
use yii\helpers\Url;

class TaskChecklist extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'task.checklist.ItemList';

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

        return $this->render('taskChecklist', [
            'options' => $this->getOptions(),
            'items' => $this->task->items,
            'task' => $this->task,
        ]);
    }

    public function getAttributes()
    {
        return [
            'class' => 'task-items'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return [
            'task-id' => $this->task->id,
            'drop-url' =>  TaskUrl::dropChecklistItem($this->task),
            'can-edit' => $this->canEdit,
            'can-resort' => $this->task->canResortItems()
        ];
    }


}