<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2019 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\helpers;

use humhub\modules\rest\definitions\ContentDefinitions;
use humhub\modules\rest\definitions\UserDefinitions;
use humhub\modules\tasks\models\Task;

/**
 * Class RestDefinitions
 *
 * @package humhub\modules\rest\definitions
 */
class RestDefinitions
{
    public static function getTask(Task $task)
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'start_datetime' => $task->start_datetime,
            'end_datetime' => $task->end_datetime,
            'scheduling' => (int)$task->scheduling,
            'all_day' => (int)$task->all_day,
            'reminders' => $task->taskReminder,
            'max_users' => (int)$task->max_users,
            'color' => $task->color,
            'task_list' => static::getTaskList($task->list),
            'cal_mode' => (int)$task->cal_mode,
            'review' => (int)$task->review,
            'request_sent' => $task->request_sent,
            'time_zone' => $task->time_zone,
            'created_at' => $task->content->created_at,
            'created_by' => UserDefinitions::getUserShort($task->getOwner()),
            'content' => ContentDefinitions::getContent($task->content),
            'percentage' => $task->getPercent(),
            'checklist' => array_map(function($item) {return $item->getAttributes();}, $task->items),
            'assigned_users' => static::getUsers($task->taskAssignedUsers),
            'responsible_users' => static::getUsers($task->taskResponsibleUsers)
        ];
    }

    public static function getTaskShort(Task $task)
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'start_datetime' => $task->start_datetime,
            'end_datetime' => $task->end_datetime,
        ];
    }

    public static function getTaskList($list)
    {
        if (! $list) {
            return null;
        }
        return [
            'id' => $list->id,
            'name' => $list->name,
            'contentcontainer_id' => $list->contentcontainer_id,
            'parent_id' => $list->parent_id,
            'color' => $list->color,
            'settings' => static::getListSettings($list->addition)
        ];
    }

    private static function getListSettings($addition)
    {
        return [
            'hide_if_completed' => (int)$addition->hide_if_completed,
            'sort_order' => (int)$addition->sort_order
        ];
    }

    private static function getUsers($users)
    {
        $result = [];

        foreach ($users as $user) {
            $result[] = UserDefinitions::getUserShort($user);
        }

        return $result;
    }
}