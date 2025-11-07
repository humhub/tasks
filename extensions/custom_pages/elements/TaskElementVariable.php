<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\extensions\custom_pages\elements;

use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordElementVariable;
use humhub\modules\custom_pages\modules\template\elements\BaseRecordElementVariable;
use humhub\modules\custom_pages\modules\template\elements\UserElementVariable;
use humhub\modules\tasks\models\Task;
use yii\db\ActiveRecord;

class TaskElementVariable extends BaseContentRecordElementVariable
{
    public string $title;
    public string $description;
    public bool $isScheduled;
    public bool $allDay;
    public ?string $startDateTime = null;
    public ?string $endDateTime = null;
    public bool $isAddedToCalendar;
    public string $timeZone;
    public ?string $listName = null;
    public ?string $listColor = null;
    public array $checkPoints = [];

    /**
     * @var UserElementVariable[]
     */
    public array $assignedUsers = [];
    /**
     * @var UserElementVariable[]
     */
    public array $responsibleUsers = [];
    public bool $isReviewRequired;

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof Task) {
            $this->title = $record->title;
            $this->description = $record->description;
            $this->isScheduled = $record->scheduling;
            $this->allDay = $record->all_day;
            $this->startDateTime = $record->start_datetime;
            $this->endDateTime = $record->end_datetime;
            $this->isAddedToCalendar = $record->cal_mode;
            $this->timeZone = $record->time_zone;
            $this->listName = $record->list?->name;
            $this->listColor = $record->list?->color;

            foreach ($record->items as $item) {
                $this->checkPoints[] = [
                    'title' => $item->title,
                    'description' => $item->description,
                    'completed' => $item->completed,
                    'sortOrder' => $item->sort_order,
                ];
            }

            foreach ($record->taskAssignedUsers as $user) {
                $this->assignedUsers[] = UserElementVariable::instance($this->elementContent)->setRecord($user);
            }
            foreach ($record->taskResponsibleUsers as $user) {
                $this->responsibleUsers[] = UserElementVariable::instance($this->elementContent)->setRecord($user);
            }
            $this->isReviewRequired = $record->review;
        }

        return parent::setRecord($record);
    }
}
