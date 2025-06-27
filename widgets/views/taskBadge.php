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
    <?= Badge::default(Yii::t('TasksModule.base', 'Pending'))->class($cssClass)->icon('info-circle') ?>
<?php elseif ($task->status == Task::STATUS_IN_PROGRESS) : ?>
    <?= Badge::info(Yii::t('TasksModule.base', 'In Progress'))->class($cssClass)->icon('edit') ?>
<?php elseif ($task->status == Task::STATUS_PENDING_REVIEW) : ?>
    <?= Badge::warning(Yii::t('TasksModule.base', 'Pending Review'))->class($cssClass)->icon('eye') ?>
<?php elseif ($task->status == Task::STATUS_COMPLETED  && $includeCompleted) : ?>
    <?= Badge::success(Yii::t('TasksModule.base', 'Completed'))->class($cssClass)->icon('check-square') ?>
<?php endif; ?>

<?php if ($task->isOverdue()) : ?>
    <?= Badge::danger(Yii::t('TasksModule.base', 'Overdue'))->id('taskDeadlineStatus')->class($cssClass . $right ? ' me-1"' : '')->icon('exclamation-triangle') ?>
<?php endif; ?>
