<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\scheduling\TaskScheduling;
use humhub\widgets\MultiSelectField;
use humhub\widgets\TimePicker;
use humhub\widgets\TimeZoneDropdownAddition;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;

/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */

$taskReminder = ArrayHelper::map($taskForm->task->getTaskReminder()->all(),'id','remind_mode');

?>

<div class="modal-body">
    <?= $form->field($taskForm->task, 'all_day')->checkbox(['data-action-change' => 'toggleDateTime']) ?>

    <div class="row">
        <div class="col-md-6 dateField">
            <?= $form->field($taskForm, 'start_date')->widget(DatePicker::className(), ['dateFormat' => Yii::$app->params['formatter']['defaultDateFormat'], 'clientOptions' => [], 'options' => ['class' => 'form-control', 'autocomplete' => "off"]]) ?>
        </div>
        <div class="col-md-6 timeField" <?= !$taskForm->showTimeFields() ? 'style="opacity:0.2"' : '' ?>>
            <?= $form->field($taskForm, 'start_time')->widget(TimePicker::class, ['disabled' => $taskForm->task->all_day]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 dateField">
            <?= $form->field($taskForm, 'end_date')->widget(DatePicker::className(), ['dateFormat' => Yii::$app->params['formatter']['defaultDateFormat'], 'clientOptions' => [], 'options' => ['class' => 'form-control',  'autocomplete' => "off"]]) ?>
        </div>
        <div class="col-md-6 timeField" <?= !$taskForm->showTimeFields() ? 'style="opacity:0.2"' : '' ?>>
            <?= $form->field($taskForm, 'end_time')->widget(TimePicker::class, ['disabled' => $taskForm->task->all_day]); ?>
        </div>
    </div>

    <?php Yii::$app->i18n->autosetLocale(); ?>

    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6 timeZoneField">
            <?= TimeZoneDropdownAddition::widget(['model' => $taskForm]) ?>
        </div>
    </div>

    <br>

    <div>
        <?= $form->field($taskForm->task, 'selectedReminders')->widget( MultiSelectField::className(), [
                'selection' => $taskReminder,
                'placeholder' => Yii::t('TasksModule.views_index_edit', 'Add reminder'),
                'items' => $taskForm->getRemindModeItems(),
                'url' => '#',
                'placeholderMore' => Yii::t('TasksModule.views_index_edit', 'Add reminder')
            ]);
        ?>
    </div>

    <?php if($taskForm->getContentContainer()->isModuleEnabled('calendar')) : ?>
        <br>
        <?= $form->field($taskForm->task, 'cal_mode')->checkbox() ?>
    <?php endif; ?>

</div>