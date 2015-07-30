<?php

use humhub\modules\space\widgets\Menu;
use humhub\modules\dashboard\widgets\Sidebar;
use humhub\modules\user\models\User;

return array(
    'id' => 'tasks',
    'class' => 'humhub\modules\tasks\Module',
    'namespace' => 'humhub\modules\tasks',
    'events' => array(
        array('class' => Menu::className(), 'event' => Menu::EVENT_INIT, 'callback' => array('humhub\modules\tasks\Module', 'onSpaceMenuInit')),
        array('class' => Sidebar::className(), 'event' => Sidebar::EVENT_INIT, 'callback' => array('humhub\modules\tasks\Module', 'onDashboardSidebarInit')),
        array('class' => User::className(), 'event' => User::EVENT_BEFORE_DELETE, 'callback' => array('humhub\modules\tasks\Module', 'onUserDelete')),
    ),
);
?>