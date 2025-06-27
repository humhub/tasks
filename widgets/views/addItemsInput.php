<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $name String */
?>

<div class="mb-3">
    <div class="input-group">
        <input type="text" name="<?= $name ?>"
               class="form-control task_item_new_input contentForm"
               placeholder="<?= Yii::t('TasksModule.base', 'Add checkpoint...') ?>">
        <div class="input-group-text addTaskItemButton" data-action-click="addTaskItem" style="cursor:pointer">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        </div>
    </div>
</div>