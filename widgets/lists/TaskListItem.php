<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets\lists;


use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\tasks\models\Task;
use humhub\widgets\JsWidget;
use Yii;

class TaskListItem extends JsWidget
{
    /**
     * @var Task
     */
    public $task;

    /**
     * @inheritdoc
     */
    public $jsWidget = 'task.list.Task';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var ContentActiveRecord
     */
    private $contentContainer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->contentContainer = Yii::$app->controller->contentContainer;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('taskListItem', [
            'task' => $this->task,
            'options' => $this->getOptions(),
            'contentContainer' =>  $this->contentContainer,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return [
            'class' => 'task-list-item'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return [
            'task-id' =>  $this->task->id,
            'reload-url' => $this->contentContainer->createUrl('/tasks/list/load-ajax-task', ['id' => $this->task->id]),
            'load-details-url' =>  $this->contentContainer->createUrl('/tasks/list/load-task-details', ['id' => $this->task->id]),
            'task-status' => $this->task->status
        ];
    }
}