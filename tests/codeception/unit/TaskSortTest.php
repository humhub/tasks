<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\tests\codeception\unit;

use DateInterval;
use DateTime;
use humhub\modules\space\models\Space;
use tasks\TaskTestCase;


class TaskSortTest extends TaskTestCase
{

    /**
     * Test find dates by open range query.
     */
    public function testDefaultOrderByUpdateTime()
    {
        $this->becomeUser('User2');
        $space4 = Space::findOne(4);
        $taskList1 = $this->createTaskList($space4, 'TaskList1');

        $dateTime = new DateTime();

        $task1 = $this->createTask($space4, 'Task1', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task1->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task2 = $this->createTask($space4, 'Task2', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task2->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task3 = $this->createTask($space4, 'Task3', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task3->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task4 = $this->createTask($space4, 'Task4', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task4->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task5 = $this->createTask($space4, 'Task5', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task5->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $tasks = $taskList1->getNonCompletedTasks()->all();

        $this->assertEquals('Task5', $tasks[0]->title);
        $this->assertEquals('Task4', $tasks[1]->title);
        $this->assertEquals('Task3', $tasks[2]->title);
        $this->assertEquals('Task2', $tasks[3]->title);
        $this->assertEquals('Task1', $tasks[4]->title);
    }

    public function testSimpleMoveIndex()
    {
        $this->becomeUser('User2');
        $space4 = Space::findOne(4);
        $taskList1 = $this->createTaskList($space4, 'TaskList1');

        $dateTime = new DateTime();

        $task1 = $this->createTask($space4, 'Task1', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task1->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task2 = $this->createTask($space4, 'Task2', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task2->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task3 = $this->createTask($space4, 'Task3', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task3->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task4 = $this->createTask($space4, 'Task4', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task4->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task5 = $this->createTask($space4, 'Task5', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task5->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);


        $taskList1->moveItemIndex($task1->id, 0);

        $tasks = $taskList1->getNonCompletedTasks()->all();

        $this->assertEquals('Task1', $tasks[0]->title);
        $this->assertEquals(0, $tasks[0]->sort_order);
        $this->assertEquals('Task5', $tasks[1]->title);
        $this->assertEquals(1, $tasks[1]->sort_order);
        $this->assertEquals('Task4', $tasks[2]->title);
        $this->assertEquals(2, $tasks[2]->sort_order);
        $this->assertEquals('Task3', $tasks[3]->title);
        $this->assertEquals(3, $tasks[3]->sort_order);
        $this->assertEquals('Task2', $tasks[4]->title);
        $this->assertEquals(4, $tasks[4]->sort_order);
    }

    public function testSimpleMoveIndex2()
    {
        $this->becomeUser('User2');
        $space4 = Space::findOne(4);
        $taskList1 = $this->createTaskList($space4, 'TaskList1');

        $dateTime = new DateTime();

        $task1 = $this->createTask($space4, 'Task1', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task1->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task2 = $this->createTask($space4, 'Task2', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task2->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task3 = $this->createTask($space4, 'Task3', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task3->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task4 = $this->createTask($space4, 'Task4', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task4->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task5 = $this->createTask($space4, 'Task5', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task5->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $taskList1->moveItemIndex($task1->id, 0);
        $taskList1->moveItemIndex($task5->id, 4);

        $tasks = $taskList1->getNonCompletedTasks()->all();

        $this->assertEquals('Task1', $tasks[0]->title);
        $this->assertEquals(0, $tasks[0]->sort_order);
        $this->assertEquals('Task4', $tasks[1]->title);
        $this->assertEquals(1, $tasks[1]->sort_order);
        $this->assertEquals('Task3', $tasks[2]->title);
        $this->assertEquals(2, $tasks[2]->sort_order);
        $this->assertEquals('Task2', $tasks[3]->title);
        $this->assertEquals(3, $tasks[3]->sort_order);
        $this->assertEquals('Task5', $tasks[4]->title);
        $this->assertEquals(4, $tasks[4]->sort_order);
    }

    public function testMoveFromUnsortedToList()
    {
        $this->becomeUser('User2');
        $space4 = Space::findOne(4);
        $taskList1 = $this->createTaskList($space4, 'TaskList1');

        $dateTime = new DateTime();

        $task1 = $this->createTask($space4, 'Task1', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task1->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task2 = $this->createTask($space4, 'Task2', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task2->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task3 = $this->createTask($space4, 'Task3', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task3->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task4 = $this->createTask($space4, 'Task4', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task4->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $task5 = $this->createTask($space4, 'Task5', $taskList1);
        $dateTime->add(new DateInterval('PT1M'));
        $task5->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        // Unsorted task
        $task6 = $this->createTask($space4, 'Task6');
        $dateTime->add(new DateInterval('PT1M'));
        $task5->updateAttributes(['updated_at' => $dateTime->format('Y-m-d H:i:s')]);

        $taskList1->moveItemIndex($task6->id, 1);

        $tasks = $taskList1->getNonCompletedTasks()->all();

        $this->assertEquals('Task5', $tasks[0]->title);
        $this->assertEquals('Task6', $tasks[1]->title);
        $this->assertEquals('Task4', $tasks[2]->title);
        $this->assertEquals('Task3', $tasks[3]->title);
        $this->assertEquals('Task2', $tasks[4]->title);
        $this->assertEquals('Task1', $tasks[5]->title);
    }
}
