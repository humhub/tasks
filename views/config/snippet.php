<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
/* @var $this yii\web\View */
/* @var $model \humhub\modules\tasks\models\SnippetModuleSettings */

use yii\widgets\ActiveForm;
use \yii\helpers\Html;
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('TasksModule.config', '<strong>Tasks</strong>'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
        <h4><?= Yii::t('TasksModule.config', 'Module Widget') ?></h4>

        <?= $form->field($model, 'myTasksSnippetShow')->checkbox(); ?>

        <?= $form->field($model, 'myTasksSnippetShowSpace')->checkbox(); ?>

        <?= $form->field($model, 'myTasksSnippetMaxItems')->input('number', ['min' => 1, 'max' => 30]) ?>

        <h4><?= Yii::t('TasksModule.config', 'Main Navigation') ?></h4>

        <?= $form->field($model, 'showGlobalMenuItem')->checkbox(); ?>

        <?= $form->field($model, 'menuSortOrder')->input('number', ['min' => 0]) ?>

        <hr>

        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
