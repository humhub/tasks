<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\tests\codeception\unit;

use humhub\modules\content\notifications\ContentCreatedNotification;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\user\notifications\MentionedNotification;
use tasks\TaskTestCase;

/**
 * The description of a new task is processed while the task is saved, so a follower who is mentioned in it
 * gets the mention only, not the "created" notification on top.
 */
class TaskMentionTest extends TaskTestCase
{
    public function testMentionedFollowerGetsTheMentionOnly()
    {
        $this->becomeUser('User2');

        // Admin follows Space 2 and is mentioned
        $task = $this->createTask(
            Space::findOne(['id' => 2]),
            'Task with a mention',
            null,
            ['description' => '[](mention:' . User::findOne(['id' => 1])->guid . ') please have a look'],
        );

        $this->assertEqualsNotificationCount(1, MentionedNotification::class, $task, null, 1);
        $this->assertHasNoNotification(ContentCreatedNotification::class, $task, null, 1);
    }

    public function testFollowerWithoutMentionGetsTheCreatedNotification()
    {
        $this->becomeUser('User2');

        $task = $this->createTask(Space::findOne(['id' => 2]), 'Task without a mention', null, ['description' => 'Nobody is mentioned']);

        $this->assertHasNoNotification(MentionedNotification::class, $task, null, 1);
        $this->assertEqualsNotificationCount(1, ContentCreatedNotification::class, $task, null, 1);
    }
}
