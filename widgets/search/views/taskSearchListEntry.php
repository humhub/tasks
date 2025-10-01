<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\modules\comment\models\Comment;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\TaskBadge;
use humhub\modules\tasks\widgets\TaskPercentageBar;
use humhub\modules\tasks\widgets\TaskUserList;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\widgets\Image as UserImage;

/* @var $task \humhub\modules\tasks\models\Task */
/* @var $canEdit boolean */
/* @var $filterResult boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

$image = $task->content->container instanceof Space
    ? SpaceImage::widget([
        'space' => $task->content->container,
        'width' => '24',
        'showTooltip' => true,
        'link' => true])
    : UserImage::widget([
        'user' => $task->content->container,
        'width' => '24',
        'showTooltip' => true,
        'link' => true]);

?>


<div class="d-flex task" data-task-url="<?= TaskUrl::viewTask($task) ?>">
    <div class="task-head flex-grow-1">
        <div class="clearfix">

            <?php if(!$contentContainer) : ?>

                <div class="task-controls" style="display:inline">
                    <?= $image ?>
                </div>

            <?php endif ?>

            <div style="margin-right:2px;display:inline-block">
                <h4 class="mt-0" style="display:inline-block">
                    <?= Html::encode($task->title); ?>
                </h4>
            </div>

            <?= TaskBadge::widget(['task' => $task]) ?>

            <div class="float-end toggleTaskDetails d-none d-sm-block"
                 style="<?= (!$task->content->canEdit()) ? 'border-right:0;margin-right:0' : '' ?>">
                <?= Icon::get('comment-o') ?> <?= Comment::getCommentCount(Task::class, $task->id); ?>
            </div>

            <?php if ($task->review) : ?>
                <div class="task-controls float-end toggleTaskDetails">
                <?= Icon::get('eye')
                    ->class('d-none d-sm-inline')
                    ->tooltip(Yii::t('TasksModule.base', 'This task requires to be reviewed by a responsible'))
                ?>
                </div>
            <?php endif; ?>

            <div class="task-controls assigned-users float-end d-inline">
                <?= TaskUserList::widget(['users' => $task->taskResponsibleUsers, 'style' => 'border:2px solid var(--accent)', 'type' => Task::USER_RESPONSIBLE]) ?>
                <?= TaskUserList::widget(['users' => $task->taskAssignedUsers]) ?>
            </div>

            <?php if ($task->isInProgress()) : ?>
                <div class="task-controls float-end d-none d-sm-inline-block pt-3" style="width:50px; height:24px;">
                    <?= TaskPercentageBar::widget(['task' => $task, 'filterResult' => $filterResult]) ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
