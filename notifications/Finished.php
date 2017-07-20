<?php

namespace humhub\modules\tasks\notifications;

use Yii;
use yii\bootstrap\Html;
use humhub\modules\notification\components\BaseNotification;

class Finished extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'tasks';

    /**
     * @inheritdoc
     */
    public function getAsHtml()
    {
        return Yii::t('TasksModule.views_notifications_taskFinished', '{userName} finished task {task}.', array(
                    '{userName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
                    '{task}' => Html::encode($this->source->getContentDescription())
        ));
    }

}

?>
