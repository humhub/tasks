<?php

namespace humhub\modules\tasks\activities;

use humhub\modules\activity\components\BaseContentActivity;
use Yii;

class TaskResetActivity extends BaseContentActivity
{
    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', '{displayName} reset task "{contentTitle}".', $params);
    }
}
