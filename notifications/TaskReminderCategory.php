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
class TaskReminderCategory extends NotificationCategory
{
    /**
     * @inheritdoc
     */
    public $id = 'task_reminder';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Tasks: Reminder');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('TasksModule.base', 'Receive Notifications for Task Reminder.');
    }
}
