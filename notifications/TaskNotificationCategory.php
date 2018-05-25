<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\notifications;

use Yii;
use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\notification\targets\BaseTarget;
use humhub\modules\notification\targets\MailTarget;
use humhub\modules\notification\targets\WebTarget;
use humhub\modules\notification\targets\MobileTarget;

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
        return Yii::t('TasksModule.notifications', 'Receive Notifications for Tasks (Deadline Updates, Status changes ...).');
    }

    /**
     * @inheritdoc
     */
    public function getDefaultSetting(BaseTarget $target)
    {
        if($target instanceof WebTarget || $target instanceof MailTarget) {
            return true;
        }  else if ($target instanceof MobileTarget) {
            return false;
        }

        return $target->defaultSetting;
    }

}
