<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\notifications;

use Yii;

/**
 * A deadline extension of a task was requested.
 */
final class ExtensionRequestNotification extends AbstractTaskNotification
{
    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', '{userName} requests a deadline extension for task {task} in space {spaceName}.', self::sentenceParams($params));
    }
}
