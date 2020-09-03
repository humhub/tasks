<?php
/* @var $this \humhub\modules\ui\view\components\View */
/* @var $model \humhub\modules\tasks\models\lists\TaskList */
/* @var $canEdit boolean */

use humhub\modules\tasks\widgets\lists\CompletedTaskListItem;

?>

<?= CompletedTaskListItem::widget(['taskList' => $model, 'canEdit' => $canEdit])?>
