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

namespace humhub\modules\tasks\widgets\search;


use humhub\components\Widget;

class TaskSearchListEntry extends Widget
{
    public $task;
    public $canEdit;
    public $contentContainer;
    public $filterResult = false;

    public function run()
    {
        return $this->render('taskSearchListEntry', [
            'task' => $this->task,
            'url' => $this->contentContainer->createUrl('/tasks/task/view', ['id' => $this->task->id]),
            'editUrl' => $this->contentContainer->createUrl('/tasks/task/edit', ['id' => $this->task->id]),
            'canEdit' => $this->canEdit,
            'filterResult' => $this->filterResult
        ]);
    }

}