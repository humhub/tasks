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
        <?php echo Yii::t('CalendarModule.views_global_index', '<strong>Filter</strong> tasks'); ?>
    </div>
    <div class="panel-body">

        <p><strong>By time</strong></p>
        <?php
        $items['all'] = 'All';
        $items['today'] = 'Today';
        $items['week'] = 'This week';
        $items['month'] = 'This month';
        $items['unscheduled'] = 'Unscheduled';
        ?>
        <?= Html::radioList('tasksTimeFilter', $defaultFilter['time'], $items, ['separator' => '<br />', 'id' => 'timeFilterSelect']); ?>

        <hr />
        <p><strong>By user</strong></p>
        <?= Html::textInput('userFilter', '', ['id' => 'tf_userFilter']); ?>
        <?php
        echo UserPicker::widget(array(
            'inputId' => 'tf_userFilter',
            'userSearchUrl' => Yii::$app->controller->contentContainer->createUrl('/space/membership/search', array('keyword' => '-keywordPlaceholder-')),
            'placeholderText' => Yii::t('SpaceModule.views_space_invite', 'Add an user'),
        ));
        ?>
        <a href="#" class="pull-right" id='ancShowMyTasks'><small>Show my tasks</small></a><br />
        <br />
        <?= Html::checkbox('userFilterUnassigned', $defaultFilter['showUnassigned'], ['label' => 'Show unassigned tasks only']); ?>
        <hr />
        <p><strong>By status</strong></p>
        <?php
        $statusList['active'] = 'Active';
        $statusList['completed'] = 'Completed';
        $statusList['deferred'] = 'Deferred';
        $statusList['cancelled'] = 'Cancelled';
        ?>
        <?= Html::checkboxList('tasksStatusFilter', $defaultFilter['status'], $statusList, ['separator' => '<br />', 'encode' => false]); ?>
        <hr />

        <p><strong>By space</strong></p>
        <?= Html::checkbox('tasksShowFromOtherSpaces', $defaultFilter['showFromOtherSpaces'], ['label' => 'Show tasks from all my spaces']); ?>
    </div>
</div>

<script>
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