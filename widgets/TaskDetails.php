<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\widgets;

use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use yii\base\Widget;

/**
 * Widget for rendering the Tasks header.
 */
class TaskDetails extends Widget
{
    public ?Task $task = null;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('taskDetails', [
            'task' => $this->task,
            'options' => $this->getOptions(),
        ]);
    }

    public function getOptions(): array
    {
        return [
            'id' => 'task-container',
            'class' => 'panel panel-default task-details',
            'data-reload-url' => TaskUrl::reloadTask($this->task),
            'data-ui-widget' => 'task.list.Task',
            'data-ui-init' => true,
        ];
    }
}
