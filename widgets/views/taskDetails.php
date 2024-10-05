<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\lists\TaskListDetails;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $task Task */
/* @var $options array */
?>
<?= Html::beginTag('div', $options) ?>
    <?= $this->render('@tasks/views/task/task_header', ['task' => $task]) ?>

    <div class="panel-body task-list-items">
        <div class="cleafix task-list-item">
            <?= TaskListDetails::widget(['task' => $task]) ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
