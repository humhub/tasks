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
/** @var $this \humhub\components\View **/
/** @var $include array **/
/** @var $includeOverdue boolean **/

?>
<?php if ($task->status == Task::STATUS_PENDING && in_array(TASK::STATUS_PENDING, $include)) : ?>
    <?= Icon::get('info-circle')->class('colorSuccess')->tooltip(Yii::t('TasksModule.base', 'Pending')) ?>
<?php elseif ($task->status == Task::STATUS_IN_PROGRESS && in_array(TASK::STATUS_IN_PROGRESS, $include)) : ?>
    <?= Icon::get('edit')->class('colorPrimary')->tooltip(Yii::t('TasksModule.base', 'In Progress')) ?>
<?php elseif ($task->status == Task::STATUS_PENDING_REVIEW && in_array(TASK::STATUS_PENDING_REVIEW, $include)) : ?>
    <?= Icon::get('eye')->class('colorPrimary')->tooltip(Yii::t('TasksModule.base', 'Pending Review')) ?>
<?php elseif ($task->status == Task::STATUS_COMPLETED && in_array(TASK::STATUS_COMPLETED, $include)) : ?>
    <?= Icon::get('check-square')->class('colorSuccess')->tooltip(Yii::t('TasksModule.base', 'Completed')) ?>
<?php endif; ?>

<?php if ($includeOverdue && $task->isOverdue()) : ?>
    <?= Icon::get('exclamation-triangle')->class('colorDanger')->tooltip(Yii::t('TasksModule.base', 'Overdue')) ?>
<?php endif; ?>
