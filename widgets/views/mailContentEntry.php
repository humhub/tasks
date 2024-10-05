<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\Task;
use humhub\modules\ui\mail\DefaultMailStyle;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\models\User;
use yii\helpers\Html;

/* @var $this View */
/* @var $space Space */
/* @var $originator User */
/* @var $content string */
/* @var $isReminder bool */
/* @var $source Task */
/* @var $module ContentContainerModule */
/* @var $date string */

$module = Yii::$app->moduleManager->getModule('tasks');
?>
<table width="100%" style="table-layout:fixed" border="0" cellspacing="0" cellpadding="0" align="left">
    <tr>
        <!-- START: USER IMAGE / MODULE IMAGE COLUMN -->
        <td width="40" valign="top" align="left" style="padding-right:20px">

            <?php if ($isReminder) : ?>
                <a href="<?= $source->getUrl() ?>">
                    <img src="<?= $module->getImage() ?>"
                         width="50"
                         height="50"
                         alt=""
                         title="<?= Html::encode($module->getName()) ?>"
                         style="border-radius: 4px;"
                         border="0" hspace="0" vspace="0"/>
                </a>
            <?php elseif ($originator) : ?>
                <?= humhub\widgets\mails\MailContentContainerImage::widget(['container' => $originator]) ?>
            <?php endif; ?>

        </td>
        <!-- END: USER IMAGE / MODULE IMAGE COLUMN-->

        <!-- START: CONTENT AND ORIGINATOR DESCRIPTION -->
        <td valign="top">
            <?php if ($isReminder) : ?>
                <table width="100%" style="table-layout:fixed" border="0" cellspacing="0" cellpadding="0" align="left">
                    <tr>
                        <td>
                            <a href="<?= $source->getUrl() ?>" style="font-size: 15px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-highlight', '#555') ?>; font-weight:300; text-align:left">
                                <?= Html::encode($source->title) ?>
                            </a>
                            <?php if ($date) : ?>
                                <span style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:300; text-align:left">
                                    <?= \humhub\widgets\TimeAgo::widget(['timestamp' => $date]) ?>
                                </span>
                            <?php endif; ?>
                             <?php if ($space) : ?>
                                <span style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:300; text-align:left">
                                    <?= Yii::t('ContentModule.views_wallLayout', 'in'); ?>
                                </span>
                                <span style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:bold; text-align:left">
                                     <a style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:bold; text-align:left; " href="<?= $space->getUrl() ?>">
                                        <?= Html::encode($space->displayName) ?>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td height="15" style="font-size: 15px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft2', '#aeaeae') ?>; font-weight:300; text-align:left">
                        <?= Yii::t('TasksModule.base', 'Your Reminder for task {task}', [
                            '{task}' => Html::encode($source->title),
                        ]);
                ?>
                        </td>
                    </tr>
                </table>
            <?php elseif ($originator) : ?>
                <table width="100%" style="table-layout:fixed" border="0" cellspacing="0" cellpadding="0" align="left">
                    <tr>
                        <td>
                            <a href="<?= $originator->createUrl('/user/profile', [], true) ?>" style="font-size: 15px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-highlight', '#555') ?>; font-weight:300; text-align:left">
                                <?= Html::encode($originator->displayName) ?>
                            </a>
                            <?php if ($date) : ?>
                                <span style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:300; text-align:left">
                                    <?= \humhub\widgets\TimeAgo::widget(['timestamp' => $date]) ?>
                                </span>
                            <?php endif; ?>
                             <?php if ($space) : ?>
                                <span style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:300; text-align:left">
                                    <?= Yii::t('ContentModule.views_wallLayout', 'in'); ?>
                                </span>
                                <span style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:bold; text-align:left">
                                     <a style="font-size: 11px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft', '#bebebe') ?>; font-weight:bold; text-align:left" href="<?= $space->getUrl() ?>">
                                        <?= Html::encode($space->displayName) ?>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td height="15" style="font-size: 15px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-soft2', '#aeaeae') ?>; font-weight:300; text-align:left">
                            <?= Html::encode($originator->profile->title); ?>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>
        </td>
        <!-- END: CONTENT AND ORIGINATOR DESCRIPTION -->
    </tr>
    <tr>
        <td colspan="2" height="10"></td>
    </tr>
    <tr>
        <td colspan="2" style="word-wrap:break-word;padding-top:5px; padding-bottom:5px; font-size: 14px; line-height: 22px; font-family:<?= $this->theme->variable('mail-font-family', DefaultMailStyle::DEFAULT_FONT_FAMILY) ?>; color:<?= $this->theme->variable('text-color-main', '#777') ?>; font-weight:300; text-align:left; border-top: 1px solid <?= $this->theme->variable('background-color-page', '#ededed') ?>">

            <?= $content ?>

        </td>
    </tr>
</table>
