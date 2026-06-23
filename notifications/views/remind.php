<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\modules\notification\models\Notification;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/* @var View $this */
/* @var string $html */
/* @var bool $isNew */
/* @var Space $space */
/* @var User $originator */
/* @var Notification $record */
/* @var string $url */
?>
<?php $this->beginContent('@tasks/notifications/views/layouts/remind.php', [
    'isNew' => $isNew,
    'space' => $space,
    'url' => $url,
    'record' => $record,
    'originator' => $originator,
]) ?>
    <?= $html ?>
<?php $this->endContent() ?>