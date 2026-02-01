<?php

namespace humhub\modules\tasks\activities;

use humhub\modules\activity\components\BaseContentActivity;
use Yii;

class TaskCompletedActivity extends BaseContentActivity
{
    protected function getMessage(array $params): string
    {
        return Yii::t('TasksModule.base', '{displayName} completed task "{contentTitle}".', $params);
    }
}
