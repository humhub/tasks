<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\forms\TaskForm;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\tasks\widgets\ContentTagDropDown;
use humhub\modules\topic\widgets\TopicPicker;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\ContentHiddenCheckbox;
use humhub\modules\ui\form\widgets\ContentVisibilitySelect;

/* @var $form ActiveForm */
/* @var $taskForm TaskForm */

$canManage = $taskForm->contentContainer->can(ManageTasks::class);
?>
<div class="modal-body">
    <?= $form->field($taskForm->task, 'title')->textInput(); ?>

    <?= $form->field($taskForm->task, 'task_list_id')->widget(ContentTagDropDown::class, [
        'prompt' => Yii::t('TasksModule.base', 'Unsorted'),
        'contentContainer' => $taskForm->contentContainer,
        'options' => [
            'data-ui-select2' => true,
            'data-ui-select2-allow-new' => $canManage,
            'data-ui-select2-new-sign' => '➕',
            'data-ui-select2-placeholder' => Yii::t('TasksModule.base', 'New list or search')
        ],
        'tagClass' => TaskList::class
    ]); ?>

    <?= $form->field($taskForm->task, 'description')->widget(RichTextField::class) ?>
    <?= $form->field($taskForm, 'topics')->widget(TopicPicker::class, ['contentContainer' => $taskForm->task->content->container]) ?>

    <?= $form->field($taskForm, 'is_public')->widget(ContentVisibilitySelect::class, ['contentOwner' => 'task'])->label(true) ?>
    <?= $form->field($taskForm->task, 'scheduling')->checkbox(['data-action-change' => 'toggleScheduling']) ?>
    <?= $form->field($taskForm, 'hidden')->widget(ContentHiddenCheckbox::class) ?>
</div>
