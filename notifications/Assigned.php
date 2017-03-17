<?php

namespace humhub\modules\tasks\notifications;

use Yii;
use humhub\libs\Html;
use humhub\modules\notification\components\BaseNotification;

class Assigned extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'tasks';

    /**
     * @inheritdoc
     */
    public function html()
    {
        return Yii::t('TasksModule.views_notifications_taskAssigned', '{userName} assigned you to the task {task}.', [
                    '{userName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
                    '{task}' => Html::encode($this->source->getContentDescription())
        ]);
    }

}

?>
