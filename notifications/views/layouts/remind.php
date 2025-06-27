<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/** @var \humhub\modules\user\models\User $originator */
/** @var \humhub\modules\space\models\Space $space */
/** @var \humhub\modules\notification\models\Notification $record */
/** @var boolean $isNew */
/** @var string $content */

?>
<li class="<?php if ($isNew) : ?>new<?php endif; ?>" data-notification-id="<?= $record->id ?>">
    <a href="<?= $url; ?>">
        <div class="media">

            <!-- show module image -->
            <img class="media-object img-rounded float-start"
                 data-src="holder.js/32x32" alt="32x32"
                 style="width: 32px; height: 32px;"
                 src="<?= Yii::$app->moduleManager->getModule('tasks')->getImage(); ?>" />

            <!-- show space image -->
            <?php if ($space !== null) : ?>
                <img class="media-object img-rounded img-space float-start"
                     data-src="holder.js/20x20" alt="20x20"
                     style="width: 20px; height: 20px;"
                     src="<?= $space->getProfileImage()->getUrl(); ?>">
                 <?php endif; ?>

            <!-- show content -->
            <div class="media-body">

                <?= $content; ?>

                <br> <?php echo humhub\widgets\TimeAgo::widget(['timestamp' => $record->created_at]); ?> 
                <?php if ($isNew) : ?> <span class="label label-danger"><?= Yii::t('NotificationModule.views_notificationLayout', 'New'); ?></span><?php endif; ?>
                <span class="label label-info"><?= Yii::t('TasksModule.base', 'Reminder'); ?></span>
            </div>

        </div>
    </a>
</li>
