<?php

/**
 * Shows a Task Wall Entry
 */
class TaskWallEntryWidget extends HWidget {

    public $task;

    public function run() {
        $user = $this->task->creator;

        $assignedUsers = $this->task->getAssignedUsers();
        $assignedToCurrentUser = false;

        $assetPrefix = Yii::app()->assetManager->publish(dirname(__FILE__) . '/../resources', true, 0, defined('YII_DEBUG'));
        Yii::app()->clientScript->registerCssFile($assetPrefix . '/tasks.css');

        $this->render('entry', array(
            'task' => $this->task,
            'user' => $user,
            'contentContainer' => $this->task->content->container,
        ));
    }

}

?>