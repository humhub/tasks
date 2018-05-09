<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\permissions;

use Yii;
use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;

/**
 * CreateTask Permission
 */
class CreateTask extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'tasks';

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
    ];
    
    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_USER
    ];


    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Create tasks');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('TasksModule.base', 'Allows the user to create new tasks, the user will only be able to edit and delete own tasks');
    }
}
