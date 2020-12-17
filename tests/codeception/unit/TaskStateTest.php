<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\tests\codeception\unit;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\state\CompletedState;
use humhub\modules\tasks\models\state\InProgressState;
use humhub\modules\tasks\models\state\PendingReviewState;
use humhub\modules\tasks\models\state\PendingState;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\user\models\User;
use tasks\TaskTestCase;


class TaskStateTest extends TaskTestCase
{
    public function _before()
    {
        parent::_before();
        $space4 = Space::findOne(4);
        $this->setContentContainerPermission($space4,Space::USERGROUP_ADMIN,ManageTasks::class, BasePermission::STATE_DENY);
        $this->setContentContainerPermission($space4,Space::USERGROUP_MODERATOR,ManageTasks::class, BasePermission::STATE_DENY);
    }

    public function testResetTask()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1', null, ['review' => 0, 'status' => Task::STATUS_COMPLETED]);
        $this->assertTrue($task->state->reset());
        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(PendingState::class, get_class($task->state));
    }

    public function testReviewRevertStateProcess()
    {

        $user1 = User::findOne(2);
        $user2 = User::findOne(3);
        $this->becomeUser('User2');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1',null,['review' => 1, 'status' => Task::STATUS_COMPLETED]);

        // Set User2 as responsible
        $task->addTaskResponsible($user2);
        $task->addTaskAssigned($user1);
        $task->refresh();

        $this->becomeUser('User1');

        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);
        $this->assertEquals(CompletedState::class, get_class($task->state));
        $this->assertFalse($task->state->canProceed());

        // Current logged in user can not revert
        $this->assertFalse($task->state->canRevert());

        // Task responsible can revert task from completed to in review
        $this->becomeUser('User2');
        $this->assertTrue($task->state->canRevert());
        $this->assertTrue($task->state->revert());
        $this->assertEquals(Task::STATUS_PENDING_REVIEW, $task->status);
        $this->assertEquals(PendingReviewState::class, get_class($task->state));

        // Assigned user can't revert in review state
        $this->assertFalse($task->state->canRevert(null, $user1));

        // Responsible user can revert from review to in progress
        $this->assertTrue($task->state->canRevert());
        $this->assertTrue($task->state->revert());
        $this->assertEquals(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertEquals(InProgressState::class, get_class($task->state));

        // Assigned user and responsible users can revert
        $this->assertTrue($task->state->canRevert(null, $user1));
        $this->assertTrue($task->state->canRevert());
        $this->assertTrue($task->state->revert());
        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(PendingState::class, get_class($task->state));
    }

    public function testSimpleStateProcess()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1');

        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(PendingState::class, get_class($task->state));

        // Make sure the state also works with afterFind
        $task = Task::findOne(['id' => $task->id]);
        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(PendingState::class, get_class($task->state));

        $this->assertTrue($task->state->proceed());
        $this->assertEquals(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertEquals(InProgressState::class, get_class($task->state));

        $this->assertTrue($task->state->proceed());
        $this->assertEquals(CompletedState::class, get_class($task->state));
        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);
    }

    public function testReviewStateProcess()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1',null,['review' => 1]);

        // Set User2 as responsible
        $task->addTaskResponsible(User::findOne(['id' => 3]));
        $task->refresh();

        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(PendingState::class, get_class($task->state));

        $this->assertTrue($task->state->proceed());
        $this->assertEquals(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertEquals(InProgressState::class, get_class($task->state));

        $this->assertTrue($task->state->proceed());
        $this->assertEquals(Task::STATUS_PENDING_REVIEW, $task->status);
        $this->assertEquals(PendingReviewState::class, get_class($task->state));

        // Make sure assigned user can't proceed anymore
        $this->assertFalse($task->state->proceed());

        // Make sure only responsible user can proceed
        $this->becomeUser('User2');
        $this->assertTrue($task->state->proceed());
        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);
        $this->assertEquals(CompletedState::class, get_class($task->state));
    }

    public function testPendingTaskChangePermissions()
    {
        $user1 = User::findOne(['id' => 2]);
        $this->becomeUser('User2');
        $space4 = Space::findOne(4);

        // Create Pending Task
        $task = $this->createTask($space4, 'Task1');

        // Check canProceed for current user
        $this->assertTrue($task->state->canProceed());
        $this->assertTrue($task->state->canProceed(Task::STATUS_IN_PROGRESS));
        $this->assertTrue($task->state->canProceed(Task::STATUS_COMPLETED));
        $this->assertFalse($task->state->canProceed(Task::STATUS_PENDING));
        $this->assertFalse($task->state->canProceed(Task::STATUS_PENDING_REVIEW));

        // Make sure non space members can't proceed the task
        $user3 = User::findOne(['id' => 4]);
        $this->assertFalse($task->state->canProceed(null, $user3));

        // Make sure space members can proceed a task without assignment
        $user2 = User::findOne(['id' => 3]);
        $this->assertTrue($task->state->canProceed(null, $user2));

        // Activate review
        $task->addTaskResponsible($user2);
        $task->updateAttributes(['review' => 1]);
        $task->refresh();

        // Make sure non responsible user can not complete task
        $this->assertTrue($task->state->canProceed(Task::STATUS_PENDING_REVIEW, $user1));
        $this->assertFalse($task->state->canProceed(Task::STATUS_COMPLETED, $user1));

        // Make sure responsible user can complete task
        $this->assertTrue($task->state->canProceed(Task::STATUS_PENDING_REVIEW, $user2));
        $this->assertTrue($task->state->canProceed(Task::STATUS_COMPLETED, $user2));


        $task->addTaskAssigned($user1);
        $task->updateAttributes(['review' => 0]);
        $task->refresh();

        // Make sure assigned user can complete task
        $this->assertTrue($task->state->canProceed(Task::STATUS_COMPLETED, $user1));
    }

    public function testAnyOneCanManageProcess()
    {
        $user1 = User::findOne(2);
        $user2 = User::findOne(3);
        $user3 = User::findOne(4);

        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1',null,['review' => 0]);

        // Any space member can process task
        $this->assertEquals(Task::STATUS_PENDING,  $task->status);
        $this->assertTrue($task->state->canProceed(null, $user1));
        $this->assertTrue($task->state->canProceed(null, $user2));
        $this->assertFalse($task->state->canProceed(null, $user3));
        $this->assertTrue($task->state->proceed());

        $this->assertEquals(Task::STATUS_IN_PROGRESS,  $task->status);
        $this->assertTrue($task->state->canProceed(null, $user1));
        $this->assertTrue($task->state->canProceed(null, $user2));
        $this->assertFalse($task->state->canProceed(null, $user3));
        $this->assertTrue($task->state->proceed());

        $this->assertEquals(Task::STATUS_COMPLETED,  $task->status);
        $this->assertFalse($task->state->canProceed(null, $user1));
        $this->assertFalse($task->state->canProceed(null, $user2));
        $this->assertFalse($task->state->canProceed(null, $user3));
        $this->assertFalse($task->state->proceed());

        $this->assertTrue($task->state->canRevert(null, $user1));
        $this->assertTrue($task->state->canRevert(null, $user2));
        $this->assertFalse($task->state->canRevert(null, $user3));
        $this->assertTrue($task->state->revert());

        $this->assertEquals(Task::STATUS_PENDING,  $task->status);
    }


    public function testNonReviewRevertStateProcess()
    {
        $admin = User::findOne(1);
        $user1 = User::findOne(2);
        $user2 = User::findOne(3);

        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1',null,['review' => 0, 'status' => Task::STATUS_COMPLETED]);

        // Set User2 as responsible
        $task->addTaskResponsible($admin);
        $task->addTaskAssigned($user1);
        $task->refresh();

        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);

        // No one can revert to pending review since review is deactivated
        $this->assertFalse($task->state->canRevert(Task::STATUS_PENDING_REVIEW, $admin));
        $this->assertFalse($task->state->canRevert(Task::STATUS_PENDING_REVIEW));

        // Assigned user can revert
        $this->assertTrue($task->state->canRevert());

        // Non assigned user can't revert
        $this->assertFalse($task->state->canRevert(null, $user2));

        $this->assertTrue($task->state->revert());
        $this->assertEquals(Task::STATUS_PENDING, $task->status);
        $this->assertEquals(PendingState::class, get_class($task->state));
    }
}
