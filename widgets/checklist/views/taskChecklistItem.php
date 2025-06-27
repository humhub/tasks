<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $this \humhub\components\View */
/* @var $task \humhub\modules\tasks\models\Task */
/* @var $item \humhub\modules\tasks\models\checklist\TaskItem */

/* @var $options array */

use humhub\helpers\Html;

$disabled = ($task->canCheckItems()) ? false : 'true';
?>
<?= Html::beginTag('li', $options) ?>

<div class="task-item" id="item-<?= $item->id ?>">

    <div class="row">
        <div class="col-md-12" style="padding-right: 0;">

            <div class="task-item-content">

                <?= Html::checkBox('item[' . $item->id . ']', $item->completed, [
                    'label' => $item->title,
                    'itemId' => $item->id,
                    'data-action-change' => 'check',
                    'disabled' => $disabled,
                    'labelOptions' => [
                        'class' => $item->completed ? 'item-finished' : '',
                    ]
                ]); ?>

                <span class="task-drag-icon tt" title="<?= Yii::t('TasksModule.base', 'Drag entry')?>" style="display:none">
                    <i class="fa fa-arrows"></i>&nbsp;
                </span>

            </div>
        </div>
    </div>

</div>

<?= Html::endTag('li') ?>
