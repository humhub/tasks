<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\permissions;

use humhub\modules\space\models\Space;

/**
 * CreateTask Permission
 */
class CreateTask extends \humhub\libs\BasePermission
{

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
    protected $title = Yii::t('TasksModule.permissions', 'Create tasks');

    /**
     * @inheritdoc
     */
    protected $description = Yii::t('TasksModule.permissions', 'Allows the user to create new tasks');

    /**
     * @inheritdoc
     */
    protected $moduleId = 'tasks';

}
