<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\tests\codeception\unit;

use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\models\Task;
use humhub\modules\user\models\User;
use tasks\TaskTestCase;


class TaskQueryTest extends TaskTestCase
{

    /**
     * Test find dates by open range query.
     */
    public function testGetTaskAssignedUser()
    {
        $this->becomeUser('User1');
        $task = $this->createTask(Space::findOne(4), 'Task1');

        $task->addTaskResponsible(User::findOne(1));
        $task->addTaskAssigned(User::findOne(1));
        $task->addTaskAssigned(User::findOne(2));
        $task->refresh();

        $responsible = $task->taskResponsibleUsers;
        $this->assertEquals(1, count($responsible));

        $assigned = $task->taskAssignedUsers;
        $this->assertEquals(2, count($assigned));

        $assignedFiltered = $task->getTaskAssignedUsers(true)->all();
        $this->assertEquals(1, count($assignedFiltered));

    }

    /**
     * Test find scheduled dates by open date range query.
     */
    public function testGetScheduledTasksByDateRange()
    {
        $this->becomeUser('User1');
        $this->createTask(Space::findOne(4), 'Task1', null, [
            'scheduling' => 1,
            'start_datetime' => date('Y-m-d H:i:s', strtotime('-10 days')),
            'end_datetime' => date('Y-m-d H:i:s', strtotime('+10 days'))
        ]);
        $this->createTask(Space::findOne(4), 'Task2', null, [
            'scheduling' => 1,
            'start_datetime' => date('Y-m-d H:i:s', strtotime('-20 days')),
            'end_datetime' => date('Y-m-d H:i:s', strtotime('+20 days'))
        ]);
        $this->createTask(Space::findOne(4), 'Task3', null, [
            'scheduling' => 1,
            'start_datetime' => date('Y-m-d H:i:s', strtotime('-5 days')),
            'end_datetime' => date('Y-m-d H:i:s', strtotime('+10 days'))
        ]);
        $this->createTask(Space::findOne(4), 'Task4', null, [
            'scheduling' => 1,
            'start_datetime' => date('Y-m-d H:i:s', strtotime('-10 days')),
            'end_datetime' => date('Y-m-d H:i:s', strtotime('+5 days'))
        ]);

        $this->assertEquals(4, Task::find()->count());

        // Testing with 'en_us' locale
        \Yii::$app->language = 'en_us';
        $this->applyDateFilters('m/d/y');

        // Testing with 'en_gb' locale
        \Yii::$app->language = 'en_gb';
        $this->applyDateFilters('d/m/y');
    }

    private function applyDateFilters($dateFormat)
    {
        $taskFilter = new TaskFilter();
        $taskFilter->load([
            'TaskFilter' => [
                'date_end' => date($dateFormat, strtotime('+8 days')),
            ]
        ]);
        $taskFilter->query();
        $this->assertEquals(1, $taskFilter->query()->count());
        $task = $taskFilter->query()->one();
        $this->assertEquals('Task4', $task->title);

        $taskFilter->load([
            'TaskFilter' => [
                'date_start' => date($dateFormat, strtotime('-10 days')),
                'date_end' => date($dateFormat, strtotime('+10 days')),
            ]
        ]);
        $this->assertEquals(3, $taskFilter->query()->count());
        $tasks = $taskFilter->query()->select('title')->indexBy('id')->column();
        $this->assertTrue(in_array('Task1', $tasks));
        $this->assertTrue(in_array('Task3', $tasks));
        $this->assertTrue(in_array('Task4', $tasks));
    }
}
