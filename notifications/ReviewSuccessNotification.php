<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\notifications;

use Yii;

/**
 * The review of a task was successful.
 */
final class ReviewSuccessNotification extends AbstractTaskNotification
{
    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', '{userName} marked Task {task} in space {spaceName} as completed.', self::sentenceParams($params));
    }
}
