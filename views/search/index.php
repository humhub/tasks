<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

\humhub\modules\tasks\assets\Assets::register($this);

use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\state\TaskState;
use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\TaskSubMenu;
use yii\bootstrap\ActiveForm;

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

    <div class="task-filter">
        <?php $form = ActiveForm::begin(['action' => TaskUrl::filterTasks($contentContainer), 'options' => ['data-ui-widget' => 'task.search.TaskFilter', 'data-ui-init' => '1'], 'enableClientValidation' => false]) ?>
            <?= $form->field($filter, 'title')->textInput(['id' => 'taskfilter-title', 'placeholder' => Yii::t('TasksModule.views_index_index', 'Filter tasks by title')])->label(false) ?>
            <div id="task-filter-loader" class="pull-right"></div>

            <div class="row">
                <div class="checkbox-filter">
                    <?= $form->field($filter, 'overdue')->checkbox(['style' => 'float:left']); ?>
                </div>
                <div class="checkbox-filter">
                    <?= $form->field($filter, 'taskAssigned')->checkbox(['style' => 'float:left']); ?>
                </div>
                <div class="checkbox-filter">
                    <?= $form->field($filter, 'taskResponsible')->checkbox(['style' => 'float:left']); ?>
                </div>
                <div class="checkbox-filter">
                    <?= $form->field($filter, 'own')->checkbox(['style' => 'float:left']); ?>
                </div>
                <div class="dropdown-filter">
                    <?= $form->field($filter, 'status')->dropDownList(TaskState::getStatusItems())->label(false); ?>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>

    <div id="filter-tasks-list" class="panel-body">
        <?= TaskSearchList::widget(['filter' => $filter]) ?>
    </div>
</div>
