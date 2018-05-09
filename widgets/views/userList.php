<?php
use humhub\modules\user\widgets\Image;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $style \humhub\modules\user\models\User[] */
?>

<?php foreach ($users as $user): ?>
    <?= Image::widget([
        'user' => $user,
        'width' => '24',
        'showTooltip' => true,
        'imageOptions' => ['style' => $style],
        'tooltipText' =>  Yii::t('TasksModule.base', '{displayName} is responsible for this task', ['displayName' => Html::encode($user->displayName)])
    ])?>
<?php endforeach; ?>
