<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use yii\base\Widget;

/**
 * Widget for rendering the menue buttons for a Task.
 * @author davidborn
 */
class TaskContextMenu extends Widget
{

    /**
     * @var Task
     */
    public $task;

    /**
     * @var \humhub\modules\content\components\ContentContainerActiveRecord Current content container.
     */
    public $contentContainer;

    /**
     * @var boolean Determines if the user has write permissions.
     */
    public $canEdit;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if(!$this->task->content->canEdit()) {
            return '';
        }

        return $this->render('taskMenuDropdown', [
                    'deleteUrl' => TaskUrl::deleteTask($this->task, null, 1),
                    'editUrl' =>  TaskUrl::editTask($this->task, null, 1),
                    'task' => $this->task,
                    'extensionRequestUrl' => TaskUrl::requestExtension($this->task),
                    'resetUrl' => TaskUrl::resetTask($this->task),
                    'canEdit' => $this->task->content->canEdit(),
                    'canRequestExtension' => ( $this->task->schedule->canRequestExtension()),
                    'canReset' => $this->task->canResetTask()
        ]);
    }

}
