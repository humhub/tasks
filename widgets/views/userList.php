<?php

use humhub\modules\tasks\models\Task;
use humhub\modules\user\widgets\Image;
use yii\helpers\Html;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $style \humhub\modules\user\models\User[] */
/* @var $type int*/


?>

<?php foreach ($users as $user): ?>
    <?php $tooltip = ($type === Task::USER_RESPONSIBLE)
        ? Yii::t('TasksModule.base', '{displayName} is responsible for this task', ['displayName' => Html::encode($user->displayName)])
        : Yii::t('TasksModule.base', '{displayName} is assigned to this task', ['displayName' => Html::encode($user->displayName)]); ?>
    <?= Image::widget([
        'user' => $user,
        'width' => '24',
        'showTooltip' => true,
        'imageOptions' => ['style' => $style],
        'tooltipText' => $tooltip
    ])?>
<?php endforeach; ?>
