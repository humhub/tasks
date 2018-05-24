<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\comment\models\Comment;

use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\widgets\lists\TaskListDetails;
use humhub\modules\tasks\widgets\TaskBadge;
use humhub\modules\tasks\widgets\TaskUserList;
use humhub\modules\user\widgets\Image;
use humhub\widgets\Button;
use humhub\modules\tasks\models\Task;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $task \humhub\modules\tasks\models\Task */
/* @var $options array */
/* @var $details boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentActiveRecord */

$checkUrl = $task->state->getCheckUrl();

?>

<?= Html::beginTag('div', $options) ?>

<div class="task-list-task-title-bar">
    <span class="task-list-item-title">

        <?php // We use an extra label in order to prevent click events on the actual label otherwise tasks could be accidentally finished ?>
        <?= Html::checkBox('item[' . $task->id . ']', $task->isCompleted(), [
                'label' => '&nbsp;',
                'data-action-change' => 'changeState',
                'data-action-url' => $checkUrl,
                'disabled' => empty($checkUrl)
        ]); ?>

        <span class="toggleTaskDetails"><?= Html::encode($task->title) ?></span>

        <?= TaskBadge::widget(['task' => $task, 'includePending' => false, 'includeCompleted' => false]) ?>

        <?php if (!$task->isCompleted()) : ?>
            <span class="task-drag-icon tt" title="<?= Yii::t('TasksModule.views_index_index', 'Drag task') ?>"  style="display:none">
                <i class="fa fa-arrows"></i>&nbsp;
            </span>
        <?php endif; ?>

    </span>

    <?php if($task->content->canEdit()) : ?>
        <div class="task-controls end pull-right">
            <?= Button::asLink()->action('task.list.editTask', TaskUrl::editTask($task))->icon('fa-pencil')->cssClass('tt')->options(['title' => Yii::t('TasksModule.base', 'Edit task')]); ?>
            <?= Button::asLink()->action('task.deleteTask', TaskUrl::deleteTask($task))->icon('fa-trash')->confirm()->cssClass('tt')->options(['title' => Yii::t('TasksModule.base', 'Delete task')]); ?>
        </div>
    <?php endif; ?>

    <div class="task-controls pull-right toggleTaskDetails" style="<?= (!$task->content->canEdit()) ? 'border-right:0;margin-right:0' : '' ?>">
        <?= Button::asLink()->icon('fa-comment-o') ?> <?= Comment::getCommentCount(Task::class, $task->id); ?>
    </div>

   <?php  if($task->scheduling) : ?>

       <?php
       $daysRemaining = $task->schedule->getDaysRemaining();
       if ($task->schedule->isOverdue()) {
           $daysRemaining = '-'.$daysRemaining;
           $schedulingTitle = Yii::t('TasksModule.views_index_index', 'Overdue');
           $schedulingColor = 'colorDanger';
       } elseif ($task->schedule->isDeadlineToday()) {
           $schedulingTitle = Yii::t('TasksModule.views_index_index', 'Today');
           $schedulingColor = 'colorWarning';
       } else {
           $schedulingTitle = Yii::t('TasksModule.views_index_index', '{count} {n,plural,=1{day} other{days}} remaining', ['count' => $daysRemaining, 'n' => $daysRemaining]);
           $schedulingColor = $daysRemaining > 1 ? '' : 'colorWarning';
       }
       ?>

       <div class="task-controls pull-right toggleTaskDetails">
           <?= Button::asLink()->icon('fa-clock-o')->cssClass($schedulingColor)->cssClass('tt')->options(['title' => $schedulingTitle]) ?> <?= $daysRemaining ?>
       </div>

    <?php endif; ?>

    <?php  if($task->review) : ?>
        <div class="task-controls pull-right toggleTaskDetails">
            <?= Button::asLink()->icon('fa-eye')->cssClass('tt')->options(['title' => Yii::t('TasksModule.base', 'This task requires to be reviewed by a responsible')]) ?>
        </div>
    <?php endif; ?>

    <?php if($task->hasTaskAssigned() || $task->hasTaskResponsible()) : ?>
        <div class="task-controls assigned-users pull-right" style="display: inline;">
            <?= TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'style' =>  'border:2px solid '.$this->theme->variable('info'), 'type' => Task::USER_RESPONSIBLE])?>
            <?= TaskUserList::widget(['users' => $task->taskAssignedUsers])?>
        </div>
    <?php endif; ?>
</div>
<?php if($details) : ?>
    <?= TaskListDetails::widget(['task' => $task])?>
<?php endif; ?>
<?= Html::endTag('div') ?>


