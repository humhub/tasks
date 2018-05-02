<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\widgets\lists\TaskListItem;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $title string */
/* @var $color string */
/* @var $listId int|null*/
/* @var $options array */
/* @var $tasks \humhub\modules\tasks\models\Task[] */
/* @var $completedTasks \humhub\modules\tasks\models\Task[] */
/* @var $completedTaskCount int */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord*/
/* @var $editListUrl string|null */
/* @var $addTaskUrl string */
/* @var $showMoreCompletedUrl string */

?>

<?= Html::beginTag('div', $options) ?>
    <div class="task-list-container" style="border-color:<?= $color ?>">
        <div class="task-list-title-bar clearfix">
            <i class="fa fa-minus-square toggleItems"></i>
            <?= Button::asLink(Html::encode($title))->cssClass('task-list-title') ?>
            <?php if($editListUrl) : ?>
                <span class="task-drag-icon tt" title="<?= Yii::t('TasksModule.views_index_index', 'Drag entry')?>" style="display:none">
                    <i class="fa fa-arrows"></i>&nbsp;
                </span>
                <?= Button::asLink()->icon('fa-pencil')->xs()->action('task.list.edit', $editListUrl)->loader(false)->cssClass('task-list-edit')->style('display:none;') ?>
            <?php endif; ?>
            <?= Button::success()->icon('fa-plus')->xs()->right()->action('task.list.editTask', $addTaskUrl)->loader(false) ?>
        </div>

        <div class="task-list-items">
            <?php foreach ($tasks as $task) : ?>
                <?= TaskListItem::widget(['task' => $task]) ?>
            <?php endforeach; ?>
        </div>

        <div class="task-list-items tasks-completed" style="<?= (!$completedTasksCount) ? 'display:none' : ''?>">
            <?php foreach ($completedTasks as $task) : ?>
                <?= TaskListItem::widget(['task' => $task]) ?>
            <?php endforeach; ?>
            <?php if($completedTasksCount > count($completedTasks)) : ?>
                <?php $remainingCount = $completedTasksCount - count($completedTasks); ?>

                <div class="task-list-task-completed-show-more">
                        <?= Button::asLink('<i class="fa fa-chevron-down"></i> '.Yii::t('TasksModule.base','Show {count} more completed {n,plural,=1{task} other{tasks}}', ['n' => $remainingCount, 'count' => $remainingCount]))
                            ->action('showMoreCompleted', $showMoreCompletedUrl)->cssClass('showMoreCompleted')->loader(true)?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?= Html::endTag('div')?>
