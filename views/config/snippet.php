<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\SnippetModuleSettings;
use humhub\modules\ui\form\widgets\ContentHiddenCheckbox;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model SnippetModuleSettings */
?>
<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('TasksModule.base', '<strong>Tasks</strong>'); ?></div>

    <div class="panel-body">
        <p><?= Yii::t('TasksModule.base', 'Configure the Tasks module to meet your specific requirements and needs. You can define individual settings and for instance decide whether a widget is displayed.') ?></p>

        <?php $form = ActiveForm::begin(); ?>

        <h4><?= Yii::t('TasksModule.base', 'General Settings') ?></h4>
        <?= $form->field($model, 'contentHiddenDefault')->widget(ContentHiddenCheckbox::class, [
            'type' => ContentHiddenCheckbox::TYPE_GLOBAL,
        ]) ?>

        <h4><?= Yii::t('TasksModule.base', 'Module Widget') ?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'A widget is an element added to to the sidebar of a stream. In this case it provides users with a short overview of their tasks.') ?></div>

        <?= $form->field($model, 'myTasksSnippetShow')->checkbox(); ?>

        <?= $form->field($model, 'myTasksSnippetShowSpace')->checkbox(); ?>

        <?= $form->field($model, 'myTasksSnippetMaxItems')->input('number', ['min' => 1, 'max' => 30]) ?>

        <h4><?= Yii::t('TasksModule.base', 'Main Navigation') ?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'Choose if you want the module to appear in the main navigation.') ?></div>

        <?= $form->field($model, 'showGlobalMenuItem')->checkbox(); ?>

        <?= $form->field($model, 'menuSortOrder')->input('number', ['min' => 0]) ?>

        <hr>

        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
