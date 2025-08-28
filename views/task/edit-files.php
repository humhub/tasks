<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\file\widgets\Upload;


/* @var $form \humhub\widgets\form\ActiveForm */
/* @var $taskForm \humhub\modules\calendar\models\forms\CalendarEntryForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */

$upload = Upload::withName();

?>

<div class="container">
    <div class="row">
        <div class="col-lg-2">
            <?= $upload->button([
                'label' => true,
                'tooltip' => false,
                'cssButtonClass' => 'btn-light btn-sm',
                'dropZone' => '#task-form',
                'max' => Yii::$app->getModule('content')->maxAttachedFiles,
            ])?>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-9">
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