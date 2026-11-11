<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\notifications;

use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\tasks\models\Task;
use Yii;

/**
 * The start of a task is near.
 */
final class RemindStart extends AbstractTaskNotification
{
    public static function category(): NotificationCategory
    {
        return self::reminderCategory();
    }

    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', 'Task {task} in space {spaceName} starts at {dateTime}.', self::sentenceParams($params));
    }

    protected function getMessageParams(): array
    {
        $task = $this->sourceRecord;

        return parent::getMessageParams() + [
            'dateTime' => $task instanceof Task ? (string) $task->schedule->getFormattedStartDateTime() : '',
        ];
    }
}
