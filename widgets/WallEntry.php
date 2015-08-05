<?php

namespace humhub\modules\tasks\widgets;

use Yii;

/**
 * Shows a Task Wall Entry
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    public $task;

    public function run()
    {
        $user = $this->contentObject->content->user;

        $currentUserAssigned = false;

        // Check if current user is assigned to this task
        foreach ($this->contentObject->assignedUsers as $au) {
            if ($au->id == Yii::$app->user->id) {
                $currentUserAssigned = true;
                break;
            }
        }

        return $this->render('entry', array(
                    'task' => $this->contentObject,
                    'user' => $user,
                    'contentContainer' => $this->contentObject->content->container,
                    'assignedUsers' => $this->contentObject->assignedUsers,
                    'currentUserAssigned' => $currentUserAssigned
        ));
    }

}

?>