<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;


/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\calendar\models\forms\CalendarEntryForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */

?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-2">
            <?= UploadButton::widget([
                'id' => 'task_upload_button',
                'label' => true,
                'tooltip' => false,
                'cssButtonClass' => 'btn-default btn-sm',
                'model' => $taskForm->task,
                'attribute' => 'files',
                'dropZone' => '#task-form',
                'preview' => '#task_upload_preview',
                'progress' => '#task_upload_progress',
                'max' => Yii::$app->getModule('content')->maxAttachedFiles,
            ]) ?>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-9">
            <?= FilePreview::widget([
                'id' => 'task_upload_preview',
                'options' => ['style' => 'margin-top:10px'],
                'model' => $taskForm->task,
                'showInStream' => true,
                'edit' => true,
            ]) ?>
        </div>
    </div>
    <br>
    <div id="task_upload_progress" style="display:none"></div>
</div>