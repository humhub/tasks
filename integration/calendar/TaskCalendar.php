<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\integration\calendar;

use humhub\modules\tasks\models\Task;
use Yii;
use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: davidborn
 */

class TaskCalendar extends Component
{
    /**
     * Default color of task type calendar items.
     */
    public const DEFAULT_COLOR = '#F4778E';

    public const ITEM_TYPE_KEY = 'task';

    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemTypesEvent
     * @return mixed
     */
    public static function addItemTypes($event)
    {
        $event->addType(static::ITEM_TYPE_KEY, [
            'title' => Yii::t('TasksModule.base', 'Task'),
            'color' => static::DEFAULT_COLOR,
            'icon' => 'tasks',
        ]);
    }

    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemsEvent
     */
    public static function addItems($event)
    {
        /** @var Task[] $tasks */
        $tasks = TaskCalendarQuery::findForEvent($event);

        $items = [];
        foreach ($tasks as $task) {
            $items[] = $task->schedule->getFullCalendarArray();
        }

        $event->addItems(static::ITEM_TYPE_KEY, $items);
    }

}
