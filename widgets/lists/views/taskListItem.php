<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\comment\models\Comment;

use humhub\modules\tasks\widgets\TaskBadge;
use humhub\modules\user\widgets\Image;
use humhub\widgets\Button;
use humhub\modules\tasks\models\Task;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $task \humhub\modules\tasks\models\Task */
/* @var $options array */
/* @var $contentContainer \humhub\modules\content\components\ContentActiveRecord */

$editUrl = $contentContainer->createUrl('/tasks/task/edit', ['id' => $task->id]);
$deleteUrl = $contentContainer->createUrl('/tasks/task/delete', ['id' => $task->id]);
$checkUrl = $task->state->getCheckUrl();

?>

<?= Html::beginTag('div', $options) ?>

<div class="task-list-task-title-bar">
    <span class="task-list-item-title">
        <?= Html::checkBox('item[' . $task->id . ']', $task->isCompleted(), ['label' => Html::encode($task->title), 'data-action-change' => 'changeState', 'data-action-url' => $checkUrl, 'disabled' => empty($checkUrl)]); ?>
        <?= TaskBadge::widget(['task' => $task, 'includePending' => false, 'includeCompleted' => false]) ?>

        <?php if (!$task->isCompleted()) : ?>
            <span class="task-drag-icon tt" title="<?= Yii::t('TasksModule.views_index_index', 'Drag entry') ?>"  style="display:none">
                <i class="fa fa-arrows"></i>&nbsp;
            </span>
        <?php endif; ?>

    </span>

    <div class="task-controls end pull-right">
        <?= Button::asLink()->action('task.list.editTask', $editUrl)->icon('fa-pencil'); ?>
        <?= Button::asLink()->action('task.list.deleteTask', $deleteUrl)->icon('fa-times')->confirm()->cssClass('colorDanger'); ?>
    </div>

    <div class="task-controls pull-right toggleTaskDetails">
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
            <?php if($task->hasTaskResponsible()) : ?>
                <?php foreach ($task->taskResponsibleUsers as $user): ?>
                    <?= Image::widget([
                        'user' => $user,
                        'width' => '24',
                        'showTooltip' => true,
                        'imageOptions' => ['style' => 'border:2px solid '.$this->theme->variable('info')],
                        'tooltipText' =>  Yii::t('TasksModule.base', '{displayName} is responsible for this task', ['displayName' => Html::encode($user->displayName)])
                    ])?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if($task->hasTaskAssigned()) : ?>
                <!-- Show assigned user -->
                <?php foreach ($task->taskAssignedUsers as $user): ?>
                    <?= Image::widget([
                        'user' => $user,
                        'width' => '24',
                        'showTooltip' => true,
                        'tooltipText' =>  Yii::t('TasksModule.base', '{displayName} is assigned to this task', ['displayName' => Html::encode($user->displayName)])
                    ])?>
                <?php endforeach; ?>
            <?php endif ?>
        </div>
    <?php endif; ?>


</div>
<?= Html::endTag('div') ?>


