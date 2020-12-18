<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2020 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\topic\widgets\TopicPicker;

/* @var $form \humhub\widgets\ActiveForm */
/* @var $taskForm \humhub\modules\tasks\models\forms\TaskForm */

?>

<div class="modal-body">
    <?= $form->field($taskForm, 'topics')->widget(TopicPicker::class, ['contentContainer' => $taskForm->task->content->container])->label(false) ?>
</div>