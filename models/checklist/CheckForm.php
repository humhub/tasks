<?php


namespace humhub\modules\tasks\models\checklist;


use humhub\modules\tasks\models\Task;
use yii\base\Model;
use yii\web\HttpException;

class CheckForm extends Model
{
    public $checked;

    public $taskId;

    public $itemId;

    /**
     * @var TaskItem
     */
    public $item;

    /**
     * @var Task
     */
    private $task;

    public $statusChanged;

    public function init()
    {
        $this->item = TaskItem::findOne(['id' => $this->itemId, 'task_id' => $this->taskId]);
        if($this->item) {
            $this->task = $this->item->task;
        }
    }

    public function rules()
    {
        return [
            [['checked'], 'boolean'],
            [['item'], 'validateCanCheck'],
        ];
    }


    public function validateCanCheck($attribute, $params)
    {
        if(!$this->item || !$this->item->task->canCheckItems()) {
            throw new HttpException(403);
        }
    }

    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        $this->item->updateAttributes(['completed' => $this->checked]);
        if($this->task->isPending()) {
            $this->task->state->proceed(Task::STATUS_IN_PROGRESS);
            $this->statusChanged = true;
        }

        return true;
    }

}