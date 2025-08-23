<?php
/* @var $this View */
/* @var $model TaskList */
/* @var $canEdit boolean */

use humhub\components\View;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\widgets\lists\CompletedTaskListItem;

?>

<?= CompletedTaskListItem::widget(['taskList' => $model, 'canEdit' => $canEdit])?>
