<?php

class TaskController extends ContentContainerController
{

    public function init()
    {

        /**
         * Fallback for older versions
         */
        if (Yii::app()->request->getParam('containerClass') == 'Space') {
            $_GET['sguid'] = Yii::app()->request->getParam('containerGuid');
        } elseif (Yii::app()->request->getParam('containerClass') == 'User') {
            $_GET['uguid'] = Yii::app()->request->getParam('containerGuid');
        }

        return parent::init();
    }

    public function actions()
    {
        return array(
            'stream' => array(
                'class' => 'TasksStreamAction',
                'contentContainer' => $this->contentContainer
            ),
        );
    }

    /**
     * Shows the Tasks tab
     */
    public function actionShow()
    {
        $this->render('show');
    }

    /**
     * Posts a new tasks
     *
     * @return type
     */
    public function actionCreate()
    {

        $this->forcePostRequest();
        $_POST = Yii::app()->input->stripClean($_POST);

        $task = new Task();
        $task->content->populateByForm();
        $task->title = Yii::app()->request->getParam('title');
        $task->max_users = Yii::app()->request->getParam('max_users', 1);
        $task->deadline = Yii::app()->request->getParam('deadline');
        $task->preassignedUsers = Yii::app()->request->getParam('preassignedUsers');

        $task->status = Task::STATUS_OPEN;

        if ($task->validate()) {
            $task->save();
            $this->renderJson(array('wallEntryId' => $task->content->getFirstWallEntryId()));
        } else {
            $this->renderJson(array('errors' => $task->getErrors()), false);
        }
    }

    public function actionAssign()
    {
        $taskId = (int) Yii::app()->request->getParam('taskId');
        $task = Task::model()->contentContainer($this->contentContainer)->findByPk($taskId);

        if ($task->content->canRead()) {
            $task->assignUser();
            $this->printTask($task);
        } else {
            throw new CHttpException(401, 'Could not access task!');
        }
        Yii::app()->end();
    }

    public function actionUnAssign()
    {
        $taskId = Yii::app()->request->getParam('taskId');
        $task = Task::model()->contentContainer($this->contentContainer)->findByPk($taskId);

        if ($task->content->canRead()) {
            $task->unassignUser();
            $this->printTask($task);
        } else {
            throw new CHttpException(401, 'Could not access task!');
        }
        Yii::app()->end();
    }

    public function actionChangePercent()
    {

        $taskId = (int) Yii::app()->request->getParam('taskId');
        $percent = (int) Yii::app()->request->getParam('percent');
        $task = Task::model()->contentContainer($this->contentContainer)->findByPk($taskId);

        if ($task->content->canRead()) {
            $task->changePercent($percent);
            $this->printTask($task);
        } else {
            throw new CHttpException(401, Yii::t('TasksModule.controllers_TaskController', 'Could not access task!'));
        }
        Yii::app()->end();
    }

    public function actionChangeStatus()
    {
        $taskId = (int) Yii::app()->request->getParam('taskId');
        $status = (int) Yii::app()->request->getParam('status');
        $task = Task::model()->contentContainer($this->contentContainer)->findByPk($taskId);

        if ($task->content->canRead()) {

            $task->changeStatus($status);
            $this->printTask($task);
        } else {
            throw new CHttpException(401, 'Could not access task!');
        }
        Yii::app()->end();
    }

    /**
     * Prints the given task wall output include the affected wall entry id
     *
     * @param Task $task
     */
    protected function printTask($task)
    {

        $output = $task->getWallOut();
        Yii::app()->clientScript->render($output);

        $json = array();
        $json['output'] = $output;
        $json['wallEntryId'] = $task->content->getFirstWallEntryId(); // there should be only one
        echo CJSON::encode($json);
        Yii::app()->end();
    }

}
