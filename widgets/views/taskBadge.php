<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\Task;
use humhub\widgets\bootstrap\Badge;

/** @var $task Task **/
/** @var $includePending boolean **/
/** @var $includeCompleted boolean **/
/** @var $right boolean **/

$cssClass = $right ? 'float-end' : '';
?>
<?php if ($task->status == Task::STATUS_PENDING && $includePending) : ?>
    <?= Badge::default(Yii::t('TasksModule.base', 'Pending'))->cssClass($cssClass)->icon('info-circle') ?>
<?php elseif ($task->status == Task::STATUS_IN_PROGRESS) : ?>
    <?= Badge::info(Yii::t('TasksModule.base', 'In Progress'))->cssClass($cssClass)->icon('edit') ?>
<?php elseif ($task->status == Task::STATUS_PENDING_REVIEW) : ?>
    <?= Badge::warning(Yii::t('TasksModule.base', 'Pending Review'))->cssClass($cssClass)->icon('eye') ?>
<?php elseif ($task->status == Task::STATUS_COMPLETED  && $includeCompleted) : ?>
    <?= Badge::success(Yii::t('TasksModule.base', 'Completed'))->cssClass($cssClass)->icon('check-square') ?>
<?php endif; ?>

<?php if ($task->isOverdue()) : ?>
    <?= Badge::danger(Yii::t('TasksModule.base', 'Overdue'))->id('taskDeadlineStatus')->cssClass($cssClass . $right ? ' me-1"' : '')->icon('exclamation-triangle') ?>
<?php endif; ?>
