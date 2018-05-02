<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\models\Task;

/** @var $task Task **/
/** @var $this \humhub\components\View **/
/** @var $include array **/
/** @var $includeOverdue boolean **/

?>
<?php if ($task->status == Task::STATUS_PENDING && in_array(TASK::STATUS_PENDING, $include)) : ?>
    <i class="fa fa-info-circle colorSuccess tt" title="<?= Yii::t('TasksModule.views_index_index', 'Pending') ?>"></i>
<?php elseif ($task->status == Task::STATUS_IN_PROGRESS && in_array(TASK::STATUS_IN_PROGRESS, $include)) : ?>
    <i class="fa fa-edit colorPrimary tt" title="<?= Yii::t('TasksModule.views_index_index', 'In Progress') ?>"></i>
<?php elseif ($task->status == Task::STATUS_PENDING_REVIEW && in_array(TASK::STATUS_PENDING_REVIEW, $include)) : ?>
    <i class="fa fa-eye colorPrimary tt" title="<?= Yii::t('TasksModule.views_index_index', 'ending Review') ?>"></i>
<?php elseif ($task->status == Task::STATUS_COMPLETED && in_array(TASK::STATUS_COMPLETED, $include)) : ?>
    <i class="fa fa-check-square colorSuccess tt" title="<?= Yii::t('TasksModule.views_index_index', 'Completed') ?>"></i>
<?php endif; ?>

<?php if ($includeOverdue && $task->isOverdue()) : ?>
    <i class="fa fa-exclamation-triangle colorDanger tt" title="<?= Yii::t('TasksModule.views_index_index', 'Overdue') ?>"></i>
<?php endif; ?>
