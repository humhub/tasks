<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\Task;
use humhub\modules\ui\icon\widgets\Icon;

/** @var $task Task **/
/** @var $includePending boolean **/
/** @var $includeCompleted boolean **/
/** @var $right boolean **/

?>
<?php if ($task->status == Task::STATUS_PENDING && $includePending) : ?>
    <div class="label label-default <?= $right ? 'pull-right' : '' ?>"><?= Icon::get('info-circle') ?> <?= Yii::t('TasksModule.base', 'Pending'); ?></div>
<?php elseif ($task->status == Task::STATUS_IN_PROGRESS) : ?>
    <div class="label label-info <?= $right ? 'pull-right' : '' ?>"><?= Icon::get('edit') ?> <?= Yii::t('TasksModule.base', 'In Progress'); ?></div>
<?php elseif ($task->status == Task::STATUS_PENDING_REVIEW) : ?>
    <div class="label label-warning <?= $right ? 'pull-right' : '' ?>"><?= Icon::get('eye') ?> <?= Yii::t('TasksModule.base', 'Pending Review'); ?></div>
<?php elseif ($task->status == Task::STATUS_COMPLETED  && $includeCompleted) : ?>
    <div class="label label-success <?= $right ? 'pull-right' : '' ?>"><?= Icon::get('check-square') ?> <?= Yii::t('TasksModule.base', 'Completed'); ?></div>
<?php endif; ?>

<?php if ($task->isOverdue()) : ?>
    <div id="taskDeadlineStatus" class="label label-danger <?= $right ? 'pull-right' : '' ?>" <?= $right ? 'style="margin-right: 3px;"' : '' ?> ><?= Icon::get('exclamation-triangle') ?> <?= Yii::t('TasksModule.base', 'Overdue'); ?></div>
<?php endif; ?>
