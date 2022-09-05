<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\permissions\CreateTask;
use yii\base\Widget;

/**
 * Widget for rendering the Tasks header.
 */
class TaskHeader extends Widget
{
    /**
     * @var null|ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var bool
     */
    public $displayAddTask = false;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('taskHeader', [
            'addTaskUrl' => $this->getAddTaskUrl()
        ]);
    }

    private function getAddTaskUrl(): ?string
    {
        if ($this->displayAddTask && (new Task($this->contentContainer))->content->canEdit()) {
            return TaskListUrl::addTaskListTask(null, $this->contentContainer);
        }

        return null;
    }

}
