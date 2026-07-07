<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\helpers\Html;
use humhub\modules\notification\models\Notification;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\widgets\bootstrap\Badge;
use humhub\widgets\TimeAgo;

/* @var Space $space */
/* @var User $originator */
/* @var Notification $record */
/* @var boolean $isNew */
/* @var string $content */
/* @var string $url */
/* @var string $relativeUrl */
?>
<a
    class="d-flex<?= $isNew ? ' new' : '' ?>"
    href="<?= $relativeUrl ?? $url ?>"
    data-notification-id="<?= $record->id ?>"
    data-notification-group="<?= !empty($record->baseModel->getGroupkey())
        ? Html::encode($record->baseModel::class . ':' . $record->baseModel->getGroupKey())
        : '' ?>">

    <div class="flex-shrink-0 me-3 pt-1 img-profile-space">
        <?php if ($originator) : ?>
            <?= UserImage::widget([
                'user' => $originator,
                'width' => 32,
                'link' => false,
                'hideOnlineStatus' => true,
            ]) ?>
        <?php endif; ?>
        <?php if ($space instanceof Space) : ?>
            <?= SpaceImage::widget([
                'space' => $space,
                'width' => 20,
                'link' => false,
                'htmlOptions' => ['class' => 'img-space'],
            ]) ?>
        <?php endif; ?>
    </div>

    <div class="flex-grow-1">
        <?= $content ?>
        <br>
        <?= TimeAgo::widget(['timestamp' => $record->created_at]) ?>
        <?= Badge::accent(Yii::t('TasksModule.base', 'Reminder')) ?>
    </div>

    <div class="flex-shrink-0 ms-2 order-last text-center">
        <?php if ($isNew) : ?>
            <span class="badge badge-new"></span>
        <?php endif; ?>
    </div>
</a>
