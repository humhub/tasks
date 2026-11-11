<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\tests\codeception\unit;

use humhub\modules\notification\components\NotificationManager;
use humhub\modules\notification\models\Notification;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\notifications\AddResponsibleNotification;
use humhub\modules\tasks\notifications\AssignedNotification;
use humhub\modules\tasks\notifications\RemindStart;
use humhub\modules\tasks\notifications\ReviewRejectedNotification;
use humhub\modules\tasks\notifications\TaskResetNotification;
use humhub\modules\user\models\User;
use tasks\TaskTestCase;

class TaskNotificationTest extends TaskTestCase
{
    public function testRejectReviewNotification()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1', null, ['review' => 1, 'status' => Task::STATUS_PENDING_REVIEW]);
        $this->assertHasNoNotification(ReviewRejectedNotification::class, $task);

        $task->addTaskAssigned(User::findOne(3));
        $task->addTaskAssigned(User::findOne(1));
        $task->addTaskResponsible(User::findOne(2));
        $task->refresh();

        $this->assertTrue($task->state->revert());
        $this->assertEquals(Task::STATUS_IN_PROGRESS, $task->status);

        $this->assertEqualsNotificationCount(1, ReviewRejectedNotification::class, $task, null, 3);
        $this->assertEqualsNotificationCount(1, ReviewRejectedNotification::class, $task, null, 1);
        $this->assertEqualsNotificationCount(0, ReviewRejectedNotification::class, $task, null, 2);
    }

    public function testResetNotification()
    {
        $this->becomeUser('User2');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1', null, ['status' => Task::STATUS_COMPLETED]);
        $this->assertHasNoNotification(TaskResetNotification::class, $task);

        $task->addTaskAssigned(User::findOne(2));
        $task->addTaskResponsible(User::findOne(3));
        $task->addTaskResponsible(User::findOne(2));
        $task->addTaskResponsible(User::findOne(1));
        $task->refresh();

        $this->assertTrue($task->state->reset());

        $this->assertEqualsNotificationCount(1, TaskResetNotification::class, $task, null, 1);
        $this->assertEqualsNotificationCount(0, TaskResetNotification::class, $task, null, 3);
        $this->assertEqualsNotificationCount(1, TaskResetNotification::class, $task, null, 2);
    }

    public function testAssignedNotification()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1');

        $this->assertHasNoNotification(AssignedNotification::class, $task, null, 3);

        $task->addTaskAssigned(User::findOne(3));
        $this->assertHasNotification(AssignedNotification::class, $task, null, 3);
    }

    public function testResponsibleNotification()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1');

        $this->assertHasNoNotification(AddResponsibleNotification::class, $task);

        $task->addTaskResponsible(User::findOne(1));
        $task->refresh();

        $this->assertHasNotification(AddResponsibleNotification::class, $task, null, 1);
    }

    public function testAssignedNotificationIsRendered()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1');
        $task->addTaskAssigned(User::findOne(3));

        $record = Notification::findOne(['class' => AssignedNotification::class, 'user_id' => 3]);
        $this->assertNotNull($record);

        $notification = NotificationManager::load($record);

        // The category keeps its id, so the settings of the users stay valid
        $this->assertSame('task', $notification::category()->id);

        // The sentence keeps its translation string
        $subject = $notification->asMailSubject();
        $this->assertStringContainsString('assigned you to task', $subject);
        $this->assertStringContainsString('Task1', $subject);
        $this->assertStringContainsString($space4->displayName, $subject);
        $this->assertStringContainsString('Task1', $notification->asWeb());
    }

    public function testReminderCategory()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1');
        $task->addTaskAssigned(User::findOne(3), false);

        RemindStart::send(User::findOne(3), source: $task, originator: User::findOne(1), dedupe: false);

        $record = Notification::findOne(['class' => RemindStart::class, 'user_id' => 3]);
        $this->assertNotNull($record);
        $this->assertSame('task_reminder', NotificationManager::load($record)::category()->id);
    }

    public function testDeleteOldNotifications()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1');
        $task->addTaskAssigned(User::findOne(3));
        // The user himself (User1) is never notified, so the responsible one is the admin
        $task->addTaskResponsible(User::findOne(1));

        $this->assertHasNotification(AssignedNotification::class, $task, null, 3);
        $this->assertHasNotification(AddResponsibleNotification::class, $task, null, 1);

        $task->deleteOldNotifications(AssignedNotification::class);
        $this->assertHasNoNotification(AssignedNotification::class, $task, null, 3);
        $this->assertHasNotification(AddResponsibleNotification::class, $task, null, 1);

        $task->deleteOldNotifications();
        $this->assertHasNoNotification(AddResponsibleNotification::class, $task, null, 1);
    }

    public function testDeleteOldNotificationsOfRecipient()
    {
        $this->becomeUser('User1');
        $space4 = Space::findOne(4);

        $task = $this->createTask($space4, 'Task1');
        $task->addTaskAssigned(User::findOne(3));
        $task->addTaskResponsible(User::findOne(1));

        $task->deleteOldNotifications(null, User::findOne(3));

        $this->assertHasNoNotification(AssignedNotification::class, $task, null, 3);
        $this->assertHasNotification(AddResponsibleNotification::class, $task, null, 1);
    }
}
