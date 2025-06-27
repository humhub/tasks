<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\extensions\custom_pages\elements;

use humhub\modules\custom_pages\modules\template\elements\BaseElementVariableIterator;

class TasksElementVariable extends BaseElementVariableIterator
{
    public function __construct(TasksElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $task) {
            $this->items[] = TaskElementVariable::instance($elementContent)->setRecord($task);
        }
    }
}
