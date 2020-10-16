<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use yii\helpers\Url;
use humhub\modules\tasks\widgets\MailContentEntry;
use humhub\widgets\mails\MailHeadline;
use humhub\widgets\mails\MailButtonList;
use humhub\widgets\mails\MailButton;

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/* @var $this yii\web\View */
/* @var $viewable \humhub\modules\notification\components\BaseNotification */
/* @var $url string */
/* @var $date string */
/* @var $isNew boolean */
/* @var $originator \humhub\modules\user\models\User */
/* @var $source yii\db\ActiveRecord */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $space humhub\modules\space\models\Space */
/* @var $record \humhub\modules\notification\models\Notification */
/* @var $html string */
/* @var $text string */
?>
<?php $this->beginContent('@notification/views/layouts/mail.php', $_params_); ?>

<?php $contentRecord = $viewable->source ?>

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
                <?=  MailContentEntry::widget([
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
                ]]);
                ?>
            </td>
        </tr>
    </table>
<?php $this->endContent();
