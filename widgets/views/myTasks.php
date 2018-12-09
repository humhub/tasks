<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\widgets\PanelMenu;
use yii\helpers\Html;
use humhub\libs\Helpers;
use humhub\modules\tasks\helpers\TaskUrl;

/* @var $taskEntries \humhub\modules\tasks\models\Task[] */

?>
<div class="panel task-upcoming-snippet" id="task-my-tasks-snippet">

    <div class="panel-heading">
        <i class="fa fa-tasks"></i> <?= Yii::t('TasksModule.widgets_views_myTasks', '<strong>Your</strong> tasks'); ?>
        <?= PanelMenu::widget(['id' => 'task-my-tasks-snippet']); ?>
    </div>

    <div class="panel-body" style="padding:0px;">
        <hr style="margin:0px">
        <ul class="media-list">
            <a href="<?= TaskUrl::globalView() ?>">
                <li style="background-color:<?= Yii::$app->view->theme->variable('background-color-secondary')?>;border-left:0">
                    <div class="media">
                        <div class="media-body text-break">
                            <strong>
                                <i class="fa fa-globe"></i>     <?= Yii::t('TasksModule.base', 'View all Tasks') ?>
                            </strong>
                        </div>
                    </div>
                </li>
            </a>
            <?php foreach ($taskEntries as $entry) : ?>
                <?php $color = $entry->getColor() ? $entry->getColor() : $this->theme->variable('info') ?>
                <a href="<?= $entry->getUrl() ?>">
                    <li style="border-left: 3px solid <?= $color?>">
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

