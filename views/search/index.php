<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\TaskHeader;
use humhub\modules\tasks\widgets\TaskSubMenu;
use humhub\modules\tasks\widgets\search\TaskFilterNavigation;

/* @var $filter TaskFilter */

Assets::register($this);
?>
<?= TaskHeader::widget() ?>

<div class="task-list-tabs">
    <?= TaskSubMenu::widget() ?>
</div>

<div class="task-overview">
    <?= TaskFilterNavigation::widget(['filter' => $filter]) ?>

    <div id="filter-tasks-list">
        <?= TaskSearchList::widget(['filter' => $filter]) ?>
    </div>
</div>