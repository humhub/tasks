<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\widgets\PanelMenu;
use humhub\helpers\Html;
use humhub\libs\Helpers;
use humhub\modules\tasks\helpers\TaskUrl;

/* @var $taskEntries \humhub\modules\tasks\models\Task[] */

?>
<div class="panel task-upcoming-snippet" id="task-my-tasks-snippet">

    <div class="panel-heading">
        <i class="fa fa-tasks"></i> <?= Yii::t('TasksModule.base', '<strong>Your</strong> tasks'); ?>
        <small><a style="font-size:0.9em;color:var(--info)" href="<?=  TaskUrl::globalView() ?>">(<?= Yii::t('TasksModule.base', 'view all'); ?>)</a></small>
        <?= PanelMenu::widget(['id' => 'task-my-tasks-snippet']); ?>
    </div>

    <div class="panel-body" style="padding:0px">
        <hr style="margin:0px">
        <ul class="media-list">
            <?php foreach ($taskEntries as $entry) : ?>
                <a href="<?= $entry->getUrl() ?>">
                    <li style="border-left: 3px solid <?= Html::encode($entry->getColor('var(--info)')) ?>">
                        <div class="media">
                            <div class="media-body text-break">
                                <?=  $entry->getBadge() ?>
                                <strong>
                                    <?= Helpers::trimText(Html::encode($entry->getTitle()), 60) ?>
                                </strong>

                                <br />
                                <span class="time"><?= $entry->schedule->getFormattedDateTime() ?></span>
                            </div>
                        </div>
                    </li>
                </a>
            <?php endforeach; ?>

        </ul>
    </div>

</div>
