<?php

use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\search\TaskFilterNavigation;
use humhub\modules\tasks\models\forms\TaskFilter;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $filter TaskFilter */

\humhub\modules\tasks\assets\Assets::register($this);

?>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-tasks"></i> <?= Yii::t('TasksModule.base', '<strong>Task</strong> Overview'); ?>
        </div>



        <div class="task-overview">

            <?= TaskFilterNavigation::widget(['filter' => $filter, 'options' => ['style' => 'border-radius:4px;background-color:' . $this->theme->variable('background-color-secondary')]]) ?>

            <div id="filter-tasks-list" class="panel-body">
                <?= TaskSearchList::widget(['filter' => $filter]) ?>
            </div>
        </div>
    </div>
</div>

