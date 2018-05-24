<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\notifications;

use Yii;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\space\models\Space;
use yii\helpers\Html;

/**
 * Notifies an admin about reported content
 *
 * @since 0.5
 */
class ReviewSuccessNotification extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $suppressSendToOriginator = true;

    /**
     * @inheritdoc
     */
    public $moduleId = 'tasks';

    /**
     * @inheritdoc
     */
    public $viewName = "taskNotification";

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new TaskNotificationCategory();
    }

    public function html()
    {
        return Yii::t('TasksModule.notifications', '{userName} marked Task {task} in space {spaceName} as completed.', [
            '{userName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
            '{task}' => Html::tag('strong', Html::encode($this->getContentInfo($this->source, false))),
            '{spaceName}' => Html::tag('strong', Html::encode($this->source->content->container->displayName))
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('TasksModule.notifications', '{userName} marked Task {task} in space {spaceName} as completed.', [
            '{userName}' => Html::encode($this->originator->displayName),
            '{task}' => Html::encode($this->getContentInfo($this->source, false)),
            '{spaceName}' => Html::encode($this->source->content->container->displayName)
        ]);
    }
}
