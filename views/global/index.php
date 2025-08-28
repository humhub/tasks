<?php

use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\search\TaskFilterNavigation;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $this \humhub\components\View */
/* @var $filter TaskFilter */

Assets::register($this);
?>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Icon::get('tasks') ?> <?= Yii::t('TasksModule.base', '<strong>Task</strong> Overview'); ?>
        </div>
    </div>

    <div class="task-overview">
        <?= TaskFilterNavigation::widget(['filter' => $filter]) ?>

        <div id="filter-tasks-list">
            <?= TaskSearchList::widget(['filter' => $filter]) ?>
        </div>
    </div>
</div>

