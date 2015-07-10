<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
?>

<?php echo Html::textArea("title", "", array('id' => 'contentForm_title', 'class' => 'form-control autosize contentForm', 'rows' => '1', "placeholder" => Yii::t('TasksModule.widgets_views_taskForm', "What to do?"))); ?>
<div class="contentForm_options">
    <?php echo Html::textInput('preassignedUsers', '', array('id' => 'preassignedUsers', 'placeholder' => Yii::t('TasksModule.widgets_views_taskForm', 'Preassign user(s) for this task.'))); ?>
    <?php
    echo humhub\modules\user\widgets\UserPicker::widget(array(
        'inputId' => 'preassignedUsers',
        'userSearchUrl' => $contentContainer->createUrl('/space/membership/search', array('keyword' => '-keywordPlaceholder-')),
        'maxUsers' => 10,
        'placeholderText' => Yii::t('TasksModule.widgets_views_taskForm', 'Assign users to this task')
    ));
    ?>

    <?php
    echo DatePicker::widget([
        'name' => 'deadline',
        'options' => [
            'class' => 'form-control',
            'id' => 'deadline',
            'placeholder' => Yii::t('TasksModule.widgets_views_taskForm', 'Deadline for this task?')
        ]
    ]);
    ?>
</div>
