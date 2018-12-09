<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

\humhub\modules\tasks\assets\Assets::register($this);

use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\TaskSubMenu;
use humhub\modules\tasks\widgets\search\TaskFilterNavigation;
use humhub\modules\tasks\models\forms\TaskFilter;

/* @var $canEdit boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $pendingTasks \humhub\modules\tasks\models\Task[] */
/* @var $tasksPastProvider \yii\data\ActiveDataProvider */
/* @var $filter \humhub\modules\tasks\models\forms\TaskFilter */

$emptyText = ($canEdit) ? Yii::t('TasksModule.views_index_index', 'Start now, by creating a new task!')
    : Yii::t('TasksModule.views_index_index', 'There are currently no upcoming tasks!.');

?>


<div class="panel panel-default task-overview">
    <?= TaskSubMenu::widget() ?>

    <?= TaskFilterNavigation::widget(['filter' => $filter, 'options' => ['style' => 'border-radius:4px;background-color:'.$this->theme->variable('background-color-secondary')]]) ?>

    <div id="filter-tasks-list" class="panel-body">
        <?= TaskSearchList::widget(['filter' => $filter]) ?>
    </div>
</div>
