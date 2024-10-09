<?php

namespace humhub\modules\tasks\helpers;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\tasks\models\checklist\TaskItem;
use humhub\modules\tasks\models\lists\TaskListInterface;
use humhub\modules\tasks\models\Task;
use Yii;
use yii\helpers\Url;

class TaskUrl extends Url
{
    public const ROUTE_VIEW_TASK = '/tasks/task/view';
    public const ROUTE_VIEW_TASK_MODAL = '/tasks/task/modal';
    public const ROUTE_RELOAD_TASK = '/tasks/task/load-ajax-task';

    public const ROUTE_DELETE_TASK = '/tasks/task/delete';
    public const ROUTE_EDIT_TASK = '/tasks/task/edit';

    public const ROUTE_PROCEED_TASK = '/tasks/task/proceed';
    public const ROUTE_REVERT_TASK = '/tasks/task/revert';

    public const ROUTE_TASK_SEARCH_FILTER = '/tasks/search/filter-tasks';
    public const ROUTE_TASK_SEARCH_ROOT = '/tasks/search';

    public const ROUTE_PICKER_ASSIGNED = '/tasks/task/task-assigned-picker';

    public const ROUTE_PICKER_RESPONSIBLE = '/tasks/task/task-responsible-picker';

    public const ROUTE_REQUEST_EXTENSION = '/tasks/task/extend';

    public const ROUTE_RESET_TASK = '/tasks/task/reset';

    public const ROUTE_DROP_CHECKLIST_ITEM = '/tasks/task/drop';
    public const ROUTE_CHECK_CHECKLIST_ITEM = '/tasks/checklist/check-item';

    public const ROUTE_GLOBAL = '/tasks/global';
    public const ROUTE_GLOBAL_FILTER = '/tasks/global/filter';

    public const ROUTE_EXPORT = 'export';

    public const ROUTE_CONFIG = '/tasks/config';
    public const ROUTE_CONTAINER_CONFIG = '/tasks/config-container';

    public static function toConfig()
    {
        return static::toRoute(static::ROUTE_CONFIG);
    }

    public static function toContainerConfig(ContentContainerActiveRecord $container)
    {
        return $container->createUrl(static::ROUTE_CONTAINER_CONFIG);
    }

    public static function globalView()
    {
        return static::toRoute(static::ROUTE_GLOBAL);
    }

    public static function globalFilter($container = null)
    {
        return static::toRoute([static::ROUTE_GLOBAL_FILTER, 'container' => $container]);
    }

    public static function exportCsv($container = null)
    {
        return static::toRoute([static::ROUTE_EXPORT, 'format' => 'csv', 'container' => $container]);
    }

    public static function exportXlsx($container = null)
    {
        return static::toRoute([static::ROUTE_EXPORT, 'format' => 'xlsx', 'container' => $container]);
    }

    public static function viewTask(Task $task, $scheme = false)
    {
        return static::container($task)->createUrl(static::ROUTE_VIEW_TASK, ['id' => $task->id], $scheme);
    }

    public static function viewTaskModal(Task $task, $cal = null)
    {
        return static::container($task)->createUrl(static::ROUTE_VIEW_TASK_MODAL, ['id' => $task->id, 'cal' => $cal]);
    }

    public static function reloadTask(Task $task)
    {
        return static::container($task)->createUrl(static::ROUTE_RELOAD_TASK, ['id' => $task->id]);
    }

    public static function deleteTask(Task $task, $cal = null, $redirect = null)
    {
        return static::container($task)->createUrl(static::ROUTE_DELETE_TASK, ['id' => $task->id,'cal' => $cal, 'redirect' => $redirect]);
    }

    public static function editTask(Task $task, $cal = null, $redirect = null, $listId = null, $wall = null)
    {
        return static::container($task)->createUrl(static::ROUTE_EDIT_TASK, ['id' => $task->id, 'cal' => $cal, 'redirect' => $redirect, 'listId' => $listId, 'wall' => $wall]);
    }

    public static function proceedTask(Task $task, $status)
    {
        return static::container($task)->createUrl(static::ROUTE_PROCEED_TASK, ['id' => $task->id, 'status' => $status]);
    }

    public static function revertTask(Task $task, $status)
    {
        return static::container($task)->createUrl(static::ROUTE_REVERT_TASK, ['id' => $task->id, 'status' => $status]);
    }

    public static function filterTasks(ContentContainerActiveRecord $container)
    {
        return $container->createUrl(static::ROUTE_TASK_SEARCH_FILTER);
    }

    public static function pickerAssigned(Task $task)
    {
        return static::container($task)->createUrl(static::ROUTE_PICKER_ASSIGNED, ['id' => $task->id]);
    }

    public static function pickerResponsible(Task $task)
    {
        return static::container($task)->createUrl(static::ROUTE_PICKER_RESPONSIBLE, ['id' => $task->id]);
    }

    public static function requestExtension(Task $task)
    {
        return static::container($task)->createUrl(static::ROUTE_REQUEST_EXTENSION, ['id' => $task->id]);
    }

    public static function resetTask(Task $task)
    {
        return static::container($task)->createUrl(static::ROUTE_RESET_TASK, ['id' => $task->id]);
    }

    public static function searchTask(ContentContainerActiveRecord $container)
    {
        return $container->createUrl(static::ROUTE_TASK_SEARCH_ROOT);
    }

    public static function dropChecklistItem(Task $task)
    {
        return static::container($task)->createUrl(static::ROUTE_DROP_CHECKLIST_ITEM, ['taskId' => $task->id]);
    }

    public static function checkChecklistItem(Task $task, TaskItem $item)
    {
        return static::container($task)->createUrl(static::ROUTE_CHECK_CHECKLIST_ITEM, ['id' => $item->id, 'taskId' => $task->id]);
    }


    /**
     * @param $obj
     * @return ContentContainerActiveRecord
     */
    public static function container($obj)
    {
        $result = null;

        /** @var $result ContentContainerActiveRecord */
        if (Yii::$app->controller instanceof ContentContainerController) {
            $result = Yii::$app->controller->contentContainer;
        }

        if ($obj instanceof ContentActiveRecord) {
            // We prefer using the cached controller container, in the future the contents container should be cached
            if (!$result || $result->contentcontainer_id !== $obj->content->contentcontainer_id) {
                $result = $obj->content->container;
            }
        } elseif ($obj instanceof TaskListInterface) {
            $result = $obj->container;
        }

        return $result;
    }
}
