<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\notifications;

use Yii;

/**
 * The user was assigned as responsible person of a task.
 */
final class AddResponsibleNotification extends AbstractTaskNotification
{
    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', '{userName} assigned you as responsible person in task {task} from space {spaceName}.', self::sentenceParams($params));
    }
}
