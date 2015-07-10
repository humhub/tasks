<?php

echo \module\tasks\widgets\WallCreateForm::widget([
    'contentContainer' => $contentContainer,
    'submitButtonText' => Yii::t('TasksModule.widgets_TaskFormWidget', 'Create')
]);

echo \humhub\modules\content\widgets\Stream::widget([
    'contentContainer' => $contentContainer,
    'streamAction' => '//tasks/task/stream',
    'messageStreamEmpty' => ($contentContainer->canWrite()) ?
            Yii::t('TasksModule.widgets_views_stream', '<b>There are no tasks yet!</b><br>Be the first and create one...') :
            Yii::t('TasksModule.widgets_views_stream', '<b>There are no tasks yet!</b>'),
    'messageStreamEmptyCss' => ($contentContainer->canWrite()) ? 'placeholder-empty-stream' : '',
    'filters' => [
        'filter_tasks_meAssigned' => Yii::t('TasksModule.widgets_views_stream', 'Assigned to me'),
        'filter_entry_mine' => Yii::t('TasksModule.widgets_views_stream', 'Created by me'),
        'filter_tasks_open' => Yii::t('TasksModule.widgets_views_stream', 'State is open'),
        'filter_tasks_finished' => Yii::t('TasksModule.widgets_views_stream', 'State is finished'),
        'filter_tasks_notassigned' => Yii::t('TasksModule.widgets_views_stream', 'Nobody assigned'),
    ]
]);
?>