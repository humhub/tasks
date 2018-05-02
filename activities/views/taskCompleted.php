<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use yii\helpers\Html;

echo Yii::t('TasksModule.activities', '{userName} completed task {task}.', [
        '{userName}' => Html::tag('strong', Html::encode($originator->displayName)),
        '{task}' => Html::tag('strong', Html::encode($this->context->getContentInfo($source, false))),
    ]);

?>