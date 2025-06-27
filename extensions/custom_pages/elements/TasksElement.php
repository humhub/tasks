<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\extensions\custom_pages\elements;

use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordsElement;
use humhub\modules\custom_pages\modules\template\elements\BaseElementVariable;
use humhub\modules\tasks\models\Task;
use Yii;

/**
 * Class to manage content records of the elements with Tasks list
 */
class TasksElement extends BaseContentRecordsElement
{
    public const RECORD_CLASS = Task::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('TasksModule.base', 'Tasks');
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return new TasksElementVariable($this);
    }
}
