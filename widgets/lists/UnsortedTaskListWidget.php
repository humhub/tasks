<?php


namespace humhub\modules\tasks\widgets\lists;


use humhub\modules\tasks\models\lists\UnsortedTaskList;
use Yii;

class UnsortedTaskListWidget extends TaskListWidget
{
    /**
     * @var null
     */
    public $id;

    public $renderIfEmpty = true;

    public function init()
    {
        parent::init();
        $this->list = new UnsortedTaskList(['contentContainer' => $this->contentContainer]);
    }

    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Unsorted');
    }

    public function getData()
    {
        $result = parent::getData();
        $result['task-list-unsored'] = true;
        return $result;
    }

    protected function getEditListUrl()
    {
        return null;
    }

    protected  function getColor()
    {
        return 'inherit';
    }

    protected  function getListId()
    {
        return null;
    }

}