<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\libs\Html;

/* @var $task \humhub\modules\tasks\models\Task */
/* @var $filterResult boolean */
?>

<!--    Progress Bar    -->
<?php
$percent = round($task->getPercent());

$divID = ($filterResult)
    ? "task_progress_" . $task->id . "_filter"
    : "task_progress_" . $task->id;

?>
<div class="progress">
    <div id="<?= $divID ?>"
         class="progress-bar progress-bar-info"
         role="progressbar"
         aria-valuenow="<?= $percent; ?>" aria-valuemin="0" aria-valuemax="100"
         style="width: 0">
    </div>
</div>
<?= Html::script("$('#{$divID}').css('width', '{$percent}%');") ?>

