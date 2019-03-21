<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\jobs;

use DateTime;
use humhub\modules\queue\ActiveJob;
use humhub\modules\tasks\models\Task;

class SendReminder extends ActiveJob
{
    public function run()
    {
        $now = new DateTime('now');

        /** @var $tasks Task[] */
        $tasks = Task::find()
            ->innerJoinWith('taskReminder')
            ->where(['task.scheduling' => 1])
            ->andWhere(['!=', 'task.status', Task::STATUS_COMPLETED])
            ->all();

        foreach ($tasks as $task) {
            if ($task->schedule->hasTaskReminder()) {
                $reminderSent = false;  // only send one reminder per run per task
                foreach ($task->taskReminder as $reminder) {
                    if ($reminderSent) {
                        continue;
                    }
                    $reminderSent = $reminder->handleRemind($now);
                }
            }
        }
    }
}
