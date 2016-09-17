<?php

use humhub\modules\user\widgets\UserPicker;
use yii\helpers\Json;
use yii\helpers\Html;
use humhub\modules\tasks\Assets;

Assets::register($this);

// Used for user picker handling in tasks.filter.js
$this->registerJsVar('tasksCurrentUserGuid', Yii::$app->user->getIdentity()->guid);
$this->registerJsVar('tasksCurrentUserImage', Yii::$app->user->getIdentity()->getProfileImage()->getUrl());
$this->registerJsVar('tasksCurrentUserDisplayName', Html::encode(Yii::$app->user->getIdentity()->displayName));
?>


<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo Yii::t('TasksModule.filters', '<strong>Filter</strong> tasks'); ?>
    </div>
    <div class="panel-body">

        <p><strong><?= Yii::t('TasksModule.filters', 'Timeframe'); ?></strong></p>
        <?= Html::radioList('tasksTimeFilter', $defaultFilter['time'], $timeFilters, ['separator' => '<br />', 'id' => 'timeFilterSelect']); ?>

        <hr />
        <p><strong><?= Yii::t('TasksModule.filters', 'Assigned user(s)'); ?></strong></p>
        <?= Html::textInput('userFilter', '', ['id' => 'tf_userFilter']); ?>
        <?php
        echo UserPicker::widget(array(
            'inputId' => 'tf_userFilter',
            'userSearchUrl' => Yii::$app->controller->contentContainer->createUrl('/space/membership/search', array('keyword' => '-keywordPlaceholder-')),
            'placeholderText' => Yii::t('TasksModule.filters', 'Add an user'),
        ));
        ?>
        <a href="#" class="pull-right" id='ancShowMyTasks'><small><?= Yii::t('TasksModule.filters', 'Show my tasks'); ?></small></a><br />
        <br />
        <?= Html::checkbox('userFilterUnassigned', $defaultFilter['showUnassigned'], ['label' => Yii::t('TasksModule.filters', 'Show unassigned only')]); ?>

        <hr />
        <p><strong><?= Yii::t('TasksModule.filters', 'Current status'); ?></strong></p>
        <?php
        ?>
        <?= Html::checkboxList('tasksStatusFilter', $defaultFilter['status'], $statusFilters, ['separator' => '<br />', 'encode' => false]); ?>

        <hr />
        <p><strong><?= Yii::t('TasksModule.filters', 'Space scope'); ?></strong></p>
        <?= Html::checkbox('tasksShowFromOtherSpaces', $defaultFilter['showFromOtherSpaces'], ['label' => Yii::t('TasksModule.filters', 'Include all my spaces')]); ?>
    </div>
</div>

<script>
    // Set initial filters
    $('#tasksList').data('filters', <?= Json::encode($defaultFilter); ?>);

    $(document).ready(function () {
<?php
// Add User Picker defaults
if (isset($defaultFilter['user']) && is_array($defaultFilter['user'])) {
    foreach ($defaultFilter['user'] as $guid) {
        $user = humhub\modules\user\models\User::findOne(['guid' => $guid]);
        if ($user !== null) {
            echo '$.fn.userpicker.addUserTag("' . $user->guid . '", "' . $user->getProfileImage()->getUrl() . '", "' . Html::encode($user->displayName) . '", "tf_userFilter");';
        }
    }
}
?>
    });
</script>