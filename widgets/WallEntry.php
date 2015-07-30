<?php

namespace humhub\modules\tasks\widgets;

use Yii;
use humhub\components\Widget;

/**
 * Shows a Task Wall Entry
 */
class WallEntry extends Widget
{

    public $task;

    public function run()
    {
        $user = $this->task->content->user;

        $currentUserAssigned = false;

        // Check if current user is assigned to this task
        foreach ($this->task->assignedUsers as $au) {
            if ($au->id == Yii::$app->user->id) {
                $currentUserAssigned = true;
                break;
            }
        }

        return $this->render('entry', array(
                    'task' => $this->task,
                    'user' => $user,
                    'contentContainer' => $this->task->content->container,
                    'assignedUsers' => $this->task->assignedUsers,
                    'currentUserAssigned' => $currentUserAssigned
        ));
    }

}

?>