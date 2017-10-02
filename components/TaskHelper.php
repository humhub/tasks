<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\tasks\components;

use DateTime;
use humhub\modules\tasks\models\Task;

/**
 * Description of TaskHelper
 *
 * @author Luke
 */
class TaskHelper
{

    public static function isOverdue(Task $task)
    {
        if (!$task->hasDeadline()) {
            return false;
        }

        if (new DateTime($task->deadline) > new DateTime()) {
            return false;
        }

        return true;
    }

}
