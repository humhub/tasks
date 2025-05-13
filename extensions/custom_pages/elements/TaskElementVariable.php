<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\extensions\custom_pages\elements;

use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordElementVariable;
use humhub\modules\custom_pages\modules\template\elements\BaseRecordElementVariable;
use humhub\modules\tasks\models\Task;
use yii\db\ActiveRecord;

class TaskElementVariable extends BaseContentRecordElementVariable
{
    public string $title;
    public string $description;

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof Task) {
            $this->title = $record->title;
            $this->description = $record->description;
        }

        return parent::setRecord($record);
    }
}
