<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\extensions\custom_pages\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordElement;
use humhub\modules\custom_pages\modules\template\elements\BaseElementVariable;
use humhub\modules\tasks\models\Task;
use Yii;

/**
 * Class to manage content record of the Task
 *
 * @property-read Task|null $record
 */
class TaskElement extends BaseContentRecordElement implements \Stringable
{
    protected const RECORD_CLASS = Task::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('TasksModule.base', 'Task');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contentId' => Yii::t('TasksModule.base', 'Task content ID'),
        ];
    }

    public function __toString(): string
    {
        return Html::encode($this->record?->title);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return TaskElementVariable::instance($this)->setRecord($this->getRecord());
    }
}
