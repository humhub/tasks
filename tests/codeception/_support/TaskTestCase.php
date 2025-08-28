<?php

namespace tasks;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\lists\TaskList;
use tests\codeception\_support\HumHubDbTestCase;

class TaskTestCase extends HumHubDbTestCase
{
    /**
     * @param ContentContainerActiveRecord $contentContainer
     * @param $title
     * @param array $config
     * @return TaskList
     */
    public function createTaskList(ContentContainerActiveRecord $contentContainer, $title, $config = [])
    {
        $taskList = new TaskList($contentContainer, $title, $config);
        $this->assertTrue($taskList->save());
        return $taskList;
    }

    public function createTask(ContentContainerActiveRecord $contentContainer, $title, $taskList = null, $config = [])
    {
        $config['title'] = $title;
        if ($taskList) {
            $config['task_list_id'] = $taskList->id;
        }
        $task = new Task($contentContainer, Content::VISIBILITY_PUBLIC, $config);
        $this->assertTrue($task->save());
        $task->refresh();
        return $task;
    }
}
