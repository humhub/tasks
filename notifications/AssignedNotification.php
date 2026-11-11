<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\notifications;

use Yii;

/**
 * The user was assigned to a task.
 */
final class AssignedNotification extends AbstractTaskNotification
{
    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', '{userName} assigned you to task {task} in space {spaceName}.', self::sentenceParams($params));
    }
}
