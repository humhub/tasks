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

    <div class="panel-heading"><?= Yii::t('TasksModule.config', '<strong>Task</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
        <h4>
            <?= Yii::t('TasksModule.config', 'Your tasks snippet'); ?>
        </h4>
        
        <div class="help-block">
            <?= Yii::t('TasksModule.config', 'Shows a widget with tasks on the dashboard where you are assigned/responsible.') ?>
        </div>
        
        <?= $form->field($model, 'myTasksSnippetShow')->checkbox(); ?>

        <div class="help-block">
            <?= Yii::t('TasksModule.config', 'Shows the widget also on the dashboard of spaces.') ?>
        </div>

        <?= $form->field($model, 'myTasksSnippetShowSpace')->checkbox(); ?>

        <?= $form->field($model, 'myTasksSnippetMaxItems')->input('number', ['min' => 1, 'max' => 30]) ?>



        <h4>
            <?= Yii::t('TasksModule.config', 'Global task menu item'); ?>
        </h4>

        <div class="help-block">
            <?= Yii::t('TasksModule.config', 'Displays a global task menu item on the main menu.') ?>
        </div>

        <?= $form->field($model, 'showGlobalMenuItem')->checkbox(); ?>

        <?= $form->field($model, 'menuSortOrder')->input('number', ['min' => 0]) ?>

        <hr>

        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
