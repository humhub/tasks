<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\Task;

/** @var $task Task **/
/** @var $includePending boolean **/
/** @var $includeCompleted boolean **/
/** @var $right boolean **/

?>
<?php if ($task->status == Task::STATUS_PENDING && $includePending) : ?>
    <div class="label label-default <?= $right ? 'pull-right' : '' ?>"><?= '<i class="fa fa-info-circle"></i> ' . Yii::t('TasksModule.views_index_index', 'Pending'); ?></div>
<?php elseif ($task->status == Task::STATUS_IN_PROGRESS) : ?>
    <div class="label label-info <?= $right ? 'pull-right' : '' ?>"><?= '<i class="fa fa-edit"></i> ' . Yii::t('TasksModule.views_index_index', 'In Progress'); ?></div>
<?php elseif ($task->status == Task::STATUS_PENDING_REVIEW) : ?>
    <div class="label label-warning <?= $right ? 'pull-right' : '' ?>"><?= '<i class="fa fa-eye"></i> ' . Yii::t('TasksModule.views_index_index', 'Pending Review'); ?></div>
<?php elseif ($task->status == Task::STATUS_COMPLETED  && $includeCompleted) : ?>
    <div class="label label-success <?= $right ? 'pull-right' : '' ?>"><?= '<i class="fa fa-check-square"></i> ' . Yii::t('TasksModule.views_index_index', 'Completed'); ?></div>
<?php endif; ?>

<?php if ($task->isOverdue()) : ?>
    <div id="taskDeadlineStatus" class="label label-danger <?= $right ? 'pull-right' : '' ?>" <?= $right ? 'style="margin-right: 3px;"' : '' ?> ><?= '<i class="fa fa-exclamation-triangle"></i> ' . Yii::t('TasksModule.views_index_index', 'Overdue'); ?></div>
<?php endif; ?>
