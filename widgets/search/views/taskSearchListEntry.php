<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\TaskContextMenu;
use humhub\modules\tasks\widgets\TaskPercentageBar;
use humhub\modules\tasks\widgets\TaskStatus;
use humhub\modules\tasks\widgets\TaskUserList;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\widgets\bootstrap\Link;

/* @var $task Task */
/* @var $canEdit boolean */
/* @var $filterResult boolean */
/* @var $contentContainer ContentContainerActiveRecord */

$imageOptions = [
    'width' => '40',
    'showTooltip' => true,
    'link' => true
];
$image = $task->content->container instanceof Space
    ? SpaceImage::widget(['space' => $task->content->container] + $imageOptions)
    : UserImage::widget(['user' => $task->content->container] + $imageOptions);

$taskResponsibleUsers = $task->taskResponsibleUsers;
$taskAssignedUsers = $task->taskAssignedUsers;
$taskContainer = $task->content->container;
?>
<div class="float-end ms-2">
    <?= TaskContextMenu::widget(['task' => $task, 'cal' => 'filter']) ?>
</div>
<div class="task d-flex flex-wrap" data-task-url="<?= TaskUrl::viewTask($task) ?>">
    <div class="d-flex flex-grow-1">
        <?php if (!$contentContainer) : ?>
            <div><?= $image ?></div>
        <?php endif ?>

        <div class="flex-fill">
            <h4 class="mt-0 mb-1"><?= Html::encode($task->title) ?></h4>
            <div class="task-item-info flex-nowrap">
                <?php if ($task->task_list_id) : ?>
                    <div data-bs-title="<?= Html::encode(Yii::t('TasksModule.base', 'List: {list}', ['list' => $task->list->title])) ?>" data-bs-toggle="tooltip">
                        <?= Icon::get('list-ul') . ' ' . $task->list->title ?>
                    </div>
                <?php endif; ?>

                <?php if ($taskContainer instanceof Space) : ?>
                    <div>
                        <?= Link::to($taskContainer->displayName, $taskContainer->getUrl())
                            ->icon('dot-circle-o')
                            ->tooltip(Yii::t('TasksModule.base', 'Space : {space}', ['space' => $taskContainer->displayName])) ?>
                    </div>
                <?php elseif ($taskContainer instanceof User) : ?>
                    <div>
                        <?= Link::to($taskContainer->displayName, $taskContainer->getUrl())
                            ->icon('user')
                            ->tooltip(Yii::t('TasksModule.base', 'User : {user}', ['user' => $taskContainer->displayName])) ?>
                    </div>
                <?php endif; ?>

                <?php if ($task->scheduling) : ?>
                    <div data-bs-title="<?= Html::encode($task->schedule->getFormattedDateTime()) ?>" data-bs-toggle="tooltip">
                        <?= Icon::get('clock-o')->tooltip('123') . ' '
                            . ($task->all_day
                                ? Yii::$app->formatter->asDate($task->schedule->getEndDateTime(), 'short')
                                : Yii::$app->formatter->asDatetime($task->schedule->getEndDateTime(), 'short'));
                        ?>
                    </div>
                <?php endif; ?>
                <div><?= TaskStatus::widget(['task' => $task]) ?></div>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <?php if ($task->isInProgress()) : ?>
            <div class="task-progress d-none d-sm-inline-block">
                <?= TaskPercentageBar::widget(['task' => $task, 'filterResult' => $filterResult]) ?>
            </div>
        <?php endif; ?>

        <?php if ($taskResponsibleUsers !== []) : ?>
            <div class="task-users">
                <?= TaskUserList::widget([
                    'users' => $taskResponsibleUsers,
                    'size' => 32,
                    'style' => 'border:2px solid var(--accent)',
                    'type' => Task::USER_RESPONSIBLE,
                ]) ?>
            </div>
        <?php endif; ?>

        <?php if ($taskAssignedUsers !== []) : ?>
            <div class="task-users">
                <?= TaskUserList::widget([
                    'users' => $taskAssignedUsers,
                    'size' => 32,
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
