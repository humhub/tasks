<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $name String */
?>

<div class="form-group">
    <div class="input-group">
        <input type="text" name="<?= $name ?>"
               class="form-control task_item_new_input contentForm"
               placeholder="<?= Yii::t('TasksModule.views_index_edit', 'Add checkpoint...') ?>">
        <div class="input-group-addon addTaskItemButton" data-action-click="addTaskItem" style="cursor:pointer">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        </div>
    </div>
</div>