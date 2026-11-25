<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\libs\Helpers;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\widgets\Icon;
use humhub\widgets\menu\MenuLink;
use humhub\widgets\SidebarCard;

/* @var $taskEntries \humhub\modules\tasks\models\Task[] */

?>
<?php SidebarCard::begin([
    'title' => Icon::get('list-check') . ' ' . Yii::t('TasksModule.base', '<strong>Your</strong> tasks'),
    'encodeTitle' => false,
    'menu' => [new MenuLink(['label' => Yii::t('TasksModule.base', 'Overview'), 'url' => TaskUrl::globalView(), 'icon' => 'list-check'])],
    'options' => ['id' => 'task-my-tasks-snippet', 'class' => 'task-upcoming-snippet'],
    'bodyOptions' => ['class' => 'p-0'],
]) ?>
        <div class="hh-list">
            <?php foreach ($taskEntries as $entry) : ?>
                <a class="d-flex" href="<?= $entry->getUrl() ?>">
                    <div>
                        <div class="d-flex">
                            <div class="flex-grow-1 text-break">
                                <?=  $entry->getBadge() ?>
                                <strong>
                                    <?= Helpers::trimText(Html::encode($entry->getTitle()), 60) ?>
                                </strong>

                                <br />
                                <span class="time"><?= $entry->schedule->getFormattedDateTime() ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        </div>
<?php SidebarCard::end() ?>
