<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use Yii;
use humhub\modules\file\handler\FileHandlerCollection;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\checklist\TaskItem;

/**
 * Widget for rendering the menue buttons for a Task.
 * @author davidborn
 */
class TaskContextMenu extends \yii\base\Widget
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

        $deleteUrl = $this->contentContainer->createUrl('/tasks/task/delete', ['id' => $this->task->id, 'redirect' => 1]);
        $editUrl = $this->contentContainer->createUrl('/tasks/task/edit', ['id' => $this->task->id, 'redirect' => 1]);
        $extensionRequestUrl = $this->contentContainer->createUrl('/tasks/task/extend', ['id' => $this->task->id]);
        $resetUrl = $this->contentContainer->createUrl('/tasks/task/reset', ['id' => $this->task->id]);

        return $this->render('taskMenuDropdown', [
                    'deleteUrl' => $deleteUrl,
                    'editUrl' => $editUrl,
                    'canEdit' => $this->canEdit,
                    'extensionRequestUrl' => $extensionRequestUrl,
                    'canRequestExtension' => ( $this->task->schedule->canRequestExtension()),
                    'resetUrl' => $resetUrl,
                    'canReset' => $this->task->canResetTask()
        ]);
    }

}
