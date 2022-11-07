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
use humhub\widgets\Link;
use humhub\widgets\Button;
use humhub\modules\tasks\models\Task;
use humhub\modules\content\widgets\MoveContentLink;
use yii\helpers\Html;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $task \humhub\modules\tasks\models\Task */
/* @var $options array */
/* @var $details boolean */
/* @var $canManage boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentActiveRecord */

$checkUrl = $task->state->getCheckUrl();

?>

<?= Html::beginTag('div', $options) ?>

<div class="task-list-task-title-bar">
    <div>
    <span class="task-list-item-title">

         <?php if ($canManage && !$task->isCompleted()) : ?>
             <i class="fa fa-arrows task-moving-handler"></i>
         <?php endif; ?>

        <?php // We use an extra label in order to prevent click events on the actual label otherwise tasks could be accidentally finished ?>
        <?= Html::checkBox('item[' . $task->id . ']', $task->isCompleted(), [
            'label' => '&nbsp;',
            'data-action-change' => 'changeState',
            'data-action-url' => $checkUrl,
            'disabled' => empty($checkUrl)
        ]); ?>

        <span class="toggleTaskDetails"><?= Html::encode($task->title) ?></span>

        <?= TaskBadge::widget(['task' => $task, 'includePending' => false, 'includeCompleted' => false]) ?>

    </span>

    <?php if ($task->content->canEdit()) : ?>
        <div class="task-controls end pull-right">
            <div class="btn-group">
                <?= Link::none()->icon('fa-ellipsis-v')
                    ->cssClass('dropdown-toggle')
                    ->options([
                        'data-toggle' => 'dropdown',
                        'haspopup' => 'true',
                        'aria-expanded' => 'false'
                    ])->sm()->loader(false) ?>
                <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <?= Button::asLink(Yii::t('TasksModule.base', 'Edit task'))
                            ->action('ui.modal.load', TaskUrl::editTask($task))
                            ->icon('fa-pencil'); ?>
                    </li>
                    <li>
                        <?= MoveContentLink::widget(['model' => $task]) ?>
                    </li>
                    <li>
                        <?= Button::asLink(Yii::t('TasksModule.base', 'Delete task'))
                            ->action('task.deleteTask', TaskUrl::deleteTask($task))
                            ->icon('fa-trash')->confirm(); ?>
                    </li>
                </ul>
            </div>


        </div>
    <?php endif; ?>

    <div class="task-controls pull-right toggleTaskDetails hidden-xs"
         style="<?= (!$task->content->canEdit()) ? 'border-right:0;margin-right:0' : '' ?>">
        <i class="fa fa-comment-o"></i> <?= Comment::getCommentCount(Task::class, $task->id); ?>
    </div>

    <?php if ($task->scheduling) : ?>

        <?php
        if ($task->isCompleted()) {
            $schedulingTitle = Yii::t('TasksModule.base', 'Completed');
            $schedulingColor = 'colorSuccess';
        } elseif ($task->schedule->isOverdue()) {
            $schedulingTitle = Yii::t('TasksModule.base', 'Overdue');
            $schedulingColor = 'colorDanger';
        } elseif ($task->schedule->isDeadlineToday()) {
            $schedulingTitle = Yii::t('TasksModule.base', 'Today');
            $schedulingColor = 'colorWarning';
        } else {
            $daysRemaining = $task->schedule->getDaysRemaining();
            $schedulingTitle = Yii::t('TasksModule.base', '{count,plural,=1{# day} other{# days}} remaining', ['count' => $daysRemaining]);
            $schedulingColor = $daysRemaining > 1 ? '' : 'colorWarning';
        }
        ?>

        <div class="task-controls pull-right toggleTaskDetails hidden-xs">
            <i class="fa fa-clock-o tt <?= $schedulingColor ?>" title="<?= $schedulingTitle ?>"></i>
        </div>

    <?php endif; ?>

    <?php if ($task->review) : ?>
        <div class="task-controls pull-right toggleTaskDetails">
            <i class="fa fa-eye tt hidden-xs tt"
               title="<?= Yii::t('TasksModule.base', 'This task requires to be reviewed by a responsible') ?>"></i>
        </div>
    <?php endif; ?>

    <?php if ($task->hasTaskAssigned() || $task->hasTaskResponsible()) : ?>
        <div class="task-controls assigned-users pull-right hidden-xs" style="display: inline;">
            <?= TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'style' => 'border:2px solid ' . $this->theme->variable('info'), 'type' => Task::USER_RESPONSIBLE]) ?>
            <?= TaskUserList::widget(['users' => $task->taskAssignedUsers]) ?>
        </div>
    <?php endif; ?>
    </div>
</div>
<?php if ($details) : ?>
    <?= TaskListDetails::widget(['task' => $task]) ?>
<?php endif; ?>
<?= Html::endTag('div') ?>


