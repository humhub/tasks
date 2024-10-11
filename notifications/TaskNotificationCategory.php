<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\notifications;

use humhub\modules\notification\components\NotificationCategory;
use Yii;

/**
 * SpaceMemberNotificationCategory
 *
 * @author buddha
 */
class TaskNotificationCategory extends NotificationCategory
{
    /**
     * @inheritdoc
     */
    public $id = 'task';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Tasks');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('TasksModule.base', 'Receive Notifications for Tasks (Deadline Updates, Status changes ...).');
    }
}
