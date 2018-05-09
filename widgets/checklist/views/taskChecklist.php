<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $this \yii\web\View */
/* @var $task \humhub\modules\tasks\models\Task */
/* @var $items \humhub\modules\tasks\models\checklist\TaskItem[] */
/* @var $options array */

use humhub\libs\Html;
use humhub\modules\tasks\widgets\checklist\TaskChecklistItem;

?>

<div class="task-checklist">
    <label><strong><i class="fa fa-check-square-o"></i> <?= Yii::t('TasksModule.base', 'Checklist:') ?></strong></label>
    <?= Html::beginTag('ul', $options) ?>
        <?php foreach ($items as $item): ?>
            <?= TaskChecklistItem::widget(['item' => $item, 'task' => $task]); ?>
        <?php endforeach; ?>
    <?= Html::endTag('ul') ?>
</div>
