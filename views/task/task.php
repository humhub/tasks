<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

use humhub\components\View;
use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\TaskDetails;
use humhub\modules\tasks\widgets\TaskSubMenu;

/* @var $this View */
/* @var $task Task */

Assets::register($this);

$this->registerJsConfig('task', [
    'text' => [
        'success.notification' => Yii::t('TasksModule.base', 'Task Users have been notified'),
    ],
]);
?>

<?= TaskSubMenu::widget() ?>
<?= TaskDetails::widget(['task' => $task]) ?>
