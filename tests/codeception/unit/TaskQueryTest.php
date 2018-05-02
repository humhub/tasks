<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\tests\codeception\unit;

use humhub\modules\space\models\Space;
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
}
