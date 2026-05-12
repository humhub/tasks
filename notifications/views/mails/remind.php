<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\components\View;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use humhub\modules\tasks\widgets\MailContentEntry;
use humhub\widgets\mails\MailHeadline;
use humhub\widgets\mails\MailButtonList;
use humhub\widgets\mails\MailButton;

/* @var $this View */
/* @var $viewable BaseNotification */
/* @var $date string */
/* @var $originator User */
/* @var $source ActiveRecord */
/* @var $space Space */
/* @var $html string */

$contentRecord = $viewable->source;
?>
<?php $this->beginContent('@notification/views/layouts/mail.php') ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
        <tr>
            <td>
                <?= MailHeadline::widget([
                    'level' => 3,
                    'text' => $contentRecord->getContentName().':',
                    'style' => 'text-transform:capitalize;'
                ]) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= MailContentEntry::widget([
                    'originator' => $originator,
                    'content' => $html,
                    'date' => $date,
                    'space' => $space,
                    'isReminder' => true,
                    'source' => $source
                ]) ?>
            </td>
        </tr>
        <tr>
            <td height="10">
            </td>
        </tr>
        <tr>
            <td>
                <?= MailButtonList::widget(['buttons' => [
                    MailButton::widget([
                        'url' => Url::to(['/content/perma', 'id' => $source->content->id], true),
                        'text' => Yii::t('ContentModule.notifications_mails', 'View Online')
                    ])
                ]]) ?>
            </td>
        </tr>
    </table>
<?php $this->endContent();
