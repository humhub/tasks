<?php
namespace tasks;

use humhub\modules\tasks\helpers\RestDefinitions;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\models\Task;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \ApiTester
{
    use _generated\ApiTesterActions;

    /**
     * Define custom actions here
     */

    public function createTask($title, $description, $params = [])
    {
        $params = array_merge([
            'containerId' => 1,
            'task_list_id' => null,
            'scheduling' => 1,
            'all_day' => 0,
            'selectedReminders' => [],
            'cal_mode' => 1,
            'assignedUsers' => ['01e50e0d-82cd-41fc-8b0c-552392f5839c'],
            'responsibleUsers' => ['01e50e0d-82cd-41fc-8b0c-552392f5839c'],
            'review' => 1,
            'is_public' => 0,
            'start_date' => '2021-03-29',
            'start_time' => '9:00',
            'end_date' => '2021-03-29',
            'end_time' => '18:00',
            'timeZone' => 'Europe/Helsinki',
            'topics' => '',
            'newItems' => ['First checkpoint', 'Second  checkpoint', 'Third checkpoint'],
        ], $params);

        $this->amGoingTo('create a sample task');
        $this->sendPost('tasks/container/' . $params['containerId'], [
            'Task' => [
                'title' => $title,
                'description' => $description,
                'task_list_id' => $params['task_list_id'],
                'scheduling' => $params['scheduling'],
                'all_day' => $params['all_day'],
                'selectedReminders' => $params['selectedReminders'],
                'cal_mode' => $params['cal_mode'],
                'assignedUsers' => $params['assignedUsers'],
                'responsibleUsers' => $params['responsibleUsers'],
                'review' => $params['review'],
            ],
            'TaskForm' => [
                'is_public' => $params['is_public'],
                'start_date' => $params['start_date'],
                'start_time' => $params['start_time'],
                'end_date' => $params['end_date'],
                'end_time' => $params['end_time'],
                'timeZone' => $params['timeZone'],
                'newItems' => $params['newItems'],
            ],
        ]);
    }

    public function createSampleTask()
    {
        $this->createTask('Sample task title', 'Sample task content');
    }

    public function getTaskDefinitionById($taskId)
    {
        $task = Task::findOne(['id' => $taskId]);
        return ($task ? RestDefinitions::getTask($task) : []);
    }

    public function seeLastCreatedTaskDefinition()
    {
        $task = Task::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $taskDefinition = ($task ? RestDefinitions::getTask($task) : []);
        $this->seeSuccessResponseContainsJson($taskDefinition);
    }

    public function seeTaskDefinitionById($taskId)
    {
        $this->seeSuccessResponseContainsJson($this->getTaskDefinitionById($taskId));
    }

    public function seePaginationTasksResponse($url, $taskIds)
    {
        $taskDefinitions = [];
        foreach ($taskIds as $taskId) {
            $taskDefinitions[] = $this->getTaskDefinitionById($taskId);
        }

        $this->seePaginationGetResponse($url, $taskDefinitions);
    }

    public function createTaskList($name, $params = [])
    {
        $params = array_merge([
            'containerId' => 1,
            'color' => '#7b9102',
            'hide_if_completed' => 0,
        ], $params);

        $this->amGoingTo('create a sample task list');
        $this->sendPost('tasks/lists/container/' . $params['containerId'], [
            'TaskList' => [
                'name' => $name,
                'color' => $params['color'],
            ],
            'TaskListSettings' => [
                'hide_if_completed' => $params['hide_if_completed'],
            ],
        ]);
    }

    public function createSampleTaskList()
    {
        $this->createTaskList('Sample task list title');
    }

    public function getTaskListDefinitionById($taskListId)
    {
        $taskList = TaskList::findOne(['id' => $taskListId]);
        return ($taskList ? RestDefinitions::getTaskList($taskList) : []);
    }

    public function seeLastCreatedTaskListDefinition()
    {
        $taskList = TaskList::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $taskListDefinition = ($taskList ? RestDefinitions::getTaskList($taskList) : []);
        $this->seeSuccessResponseContainsJson($taskListDefinition);
    }

    public function seeTaskListDefinitionById($taskListId)
    {
        $this->seeSuccessResponseContainsJson($this->getTaskListDefinitionById($taskListId));
    }

    public function seePaginationTaskListsResponse($url, $taskListIds)
    {
        $taskListDefinitions = [];
        foreach ($taskListIds as $taskListId) {
            $taskListDefinitions[] = $this->getTaskDefinitionById($taskListId);
        }

        $this->seePaginationGetResponse($url, $taskListDefinitions);
    }

}
