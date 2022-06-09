<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\TaskSubMenu;
use humhub\modules\tasks\widgets\search\TaskFilterNavigation;

/* @var $canEdit boolean */
/* @var $filter TaskFilter */

Assets::register($this);
?>
<div class="panel panel-default task-list-header">
    <div class="panel-body clearfix">
        <h4><?= Yii::t('TasksModule.base', 'Tasks') ?></h4>
        <div class="help-block"><?= Yii::t('TasksModule.base', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci animi esse obcaecati unde voluptatem! Assumenda, sed.') ?></div>
    </div>
</div>

<div class="task-list-tabs">
    <?= TaskSubMenu::widget() ?>
</div>

<div class="panel panel-default task-overview">
    <?= TaskFilterNavigation::widget(['filter' => $filter, 'options' => ['style' => 'border-radius:4px;background-color:'.$this->theme->variable('background-color-secondary')]]) ?>

    <div id="filter-tasks-list" class="panel-body">
        <?= TaskSearchList::widget(['filter' => $filter]) ?>
    </div>
</div>