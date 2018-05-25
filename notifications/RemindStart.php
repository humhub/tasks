<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\notifications;

use humhub\modules\tasks\models\Task;
use Yii;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\space\models\Space;
use yii\helpers\Html;

/**
 * Notifies an admin about reported content
 *
 * @since 0.5
 */
class RemindStart extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $suppressSendToOriginator = false;

    /**
     * @inheritdoc
     */
    public $moduleId = 'tasks';

    /**
     * @inheritdoc
     */
    public $viewName = "remind.php";

    /**
     * @var Task
     */
    public $source;

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new TaskReminderCategory();
    }

    public function html()
    {
        return Yii::t('TasksModule.notifications', 'Task {task} in space {spaceName} starts at {dateTime}.', [
            '{task}' => Html::tag('strong', Html::encode($this->getContentInfo($this->source, false))),
            '{spaceName}' => Html::tag('strong', Html::encode($this->source->content->container->displayName)),
            '{dateTime}' => Html::encode($this->source->schedule->getFormattedStartDateTime())
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('TasksModule.notifications', 'Task {task} in space {spaceName} starts at {dateTime}.', [
            '{task}' => Html::encode($this->getContentInfo($this->source, false)),
            '{spaceName}' => Html::encode($this->source->content->container->displayName),
            '{dateTime}' => Html::encode($this->source->schedule->getFormattedStartDateTime())
        ]);
    }
}
