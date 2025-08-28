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
use humhub\helpers\Html;

/**
 * Notifies an admin about reported content
 *
 * @since 0.5
 */
class ExtensionRequestNotification extends BaseNotification
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
        return Yii::t('TasksModule.base', '{userName} requests a deadline extension for task {task} in space {spaceName}.', [
            '{userName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
            '{task}' => Html::tag('strong', Html::encode($this->getContentInfo($this->source, false))),
            '{spaceName}' => Html::tag('strong', Html::encode($this->source->content->container->displayName)),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('TasksModule.base', '{userName} requests a deadline extension for task {task} in space {spaceName}.', [
            '{userName}' => Html::encode($this->originator->displayName),
            '{task}' => Html::encode($this->getContentInfo($this->source, false)),
            '{spaceName}' => Html::encode($this->source->content->container->displayName),
        ]);
    }
}
