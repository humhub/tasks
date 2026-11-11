<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\notifications;

use humhub\modules\notification\components\BaseNotification;
use humhub\modules\notification\components\MessageParam;
use humhub\modules\notification\components\NotificationCategory;
use Yii;

/**
 * Base of the notifications about a task: the category and the parameters of the sentences.
 *
 * The sentences keep their `{userName}`, `{task}` and `{spaceName}` placeholders, so the existing
 * translations stay valid.
 */
abstract class AbstractTaskNotification extends BaseNotification
{
    public static function category(): NotificationCategory
    {
        return new NotificationCategory(
            'task',
            Yii::t('TasksModule.base', 'Tasks'),
            Yii::t('TasksModule.base', 'Receive Notifications for Tasks (Deadline Updates, Status changes ...).'),
        );
    }

    protected static function reminderCategory(): NotificationCategory
    {
        return new NotificationCategory(
            'task_reminder',
            Yii::t('TasksModule.base', 'Tasks: Reminder'),
            Yii::t('TasksModule.base', 'Receive Notifications for Task Reminder.'),
        );
    }

    /**
     * The container is named `spaceName` also when it is a profile, as before.
     */
    protected function getMessageParams(): array
    {
        return [
            'spaceName' => MessageParam::emphasis((string) $this->contentContainer?->polymorphicRelation?->displayName),
        ];
    }

    /**
     * The built-in parameters of the core under the names of the sentences.
     *
     * @param array<string, string|int> $params
     * @return array<string, string|int>
     */
    protected static function sentenceParams(array $params): array
    {
        return $params + [
            'userName' => $params['displayName'] ?? '',
            'task' => $params['contentTitle'] ?? '',
        ];
    }
}
