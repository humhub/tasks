<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\file\widgets\Upload;


/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\calendar\models\forms\CalendarEntryForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */

$upload = Upload::withName();

?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-2">
            <?= $upload->button([
                'label' => true,
                'tooltip' => false,
                'cssButtonClass' => 'btn-default btn-sm',
                'dropZone' => '#task-form',
                'max' => Yii::$app->getModule('content')->maxAttachedFiles,
            ])?>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-9">
            <?= $upload->preview([
                'options' => ['style' => 'margin-top:10px'],
                'model' => $taskForm->task,
                'showInStream' => true,
            ])?>
        </div>
    </div>
    <br>
    <?= $upload->progress()?>
</div>