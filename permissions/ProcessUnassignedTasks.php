<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\permissions;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\libs\BasePermission;

/**
 * Manage task permission for a content container
 *
 * @author buddh4
 */
class ProcessUnassignedTasks extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'tasks';

    /**
     * @inheritdoc
     */
    protected $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        User::USERGROUP_SELF,
        Space::USERGROUP_MEMBER,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_GUEST,
        Space::USERGROUP_USER,
        User::USERGROUP_SELF,
        User::USERGROUP_FRIEND,
        User::USERGROUP_USER,
        User::USERGROUP_GUEST,
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Process unassigned tasks');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('TasksModule.base', 'Allows the user to process unassigned tasks');
    }
}
