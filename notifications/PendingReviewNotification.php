<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\notifications;

use Yii;

/**
 * A task is waiting for a review.
 */
final class PendingReviewNotification extends AbstractTaskNotification
{
    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', '{userName} requests you to review Task {task} in space {spaceName}.', self::sentenceParams($params));
    }
}
