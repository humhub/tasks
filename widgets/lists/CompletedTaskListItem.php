<?php


namespace humhub\modules\tasks\widgets\lists;


use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\widgets\JsWidget;

class CompletedTaskListItem extends JsWidget
{
    public $jsWidget = 'task.list.CompletedTaskListViewItem';

    /**
     * @var TaskList
     */
    public $taskList;

    /**
     * @var boolean
     */
    public $canEdit;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if($this->canEdit === null && $this->contentContainer) {
            $this->canEdit = $this->contentContainer->can(ManageTasks::class);
        }

        return $this->render('completedTaskListItem', [
            'canEdit' => $this->canEdit,
            'taskList' => $this->taskList,
            'options' => $this->getOptions()
        ]);
    }

    public function getAttributes()
    {
        return [
            'class' => 'media'
        ];
    }

    public function getData()
    {
        return [
            'reload-url' => TaskListUrl::reloadCompletedTaskList($this->taskList)
        ];
    }

}