<?php


namespace humhub\modules\tasks\models\lists;


use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\tasks\models\Sortable;
use humhub\modules\tasks\models\Task;
use Yii;
use yii\base\Model;

class UnsortedTaskList extends Model implements TaskListInterface, Sortable
{
    public $contentContainer;

    /**
     * @return ActiveQueryContent
     */
    public function getTasks()
    {
        return Task::findUnsorted($this->contentContainer);
    }

    /**
     * @return ActiveQueryContent
     */
    public function getNonCompletedTasks()
    {
        return $this->getTasks()->where(['!=', 'task.status', Task::STATUS_COMPLETED])->orderBy(['sort_order' => SORT_ASC, 'task.updated_at' => SORT_DESC]);
    }

    /**
     * @param $offset int
     * @param $limit int
     * @return static[]
     */
    public function getShowMoreCompletedTasks($offset, $limit)
    {
        return $this->getCompletedTasks()->orderBy(['task.updated_at' => SORT_DESC])->offset($offset)->limit($limit)->all();
    }

    /**
     * @return ActiveQueryContent
     */
    public function getCompletedTasks()
    {
        return $this->getTasksByStatus(Task::STATUS_COMPLETED)->orderBy(['task.updated_at' => SORT_DESC]);
    }

    public function moveItemIndex($taskId, $newIndex)
    {
        /** @var $task Task */
        $transaction = Task::getDb()->beginTransaction();

        try {
            $task = Task::findOne(['id' => $taskId]);
            $oldListId = $task->task_list_id;
            $task->updateAttributes(['task_list_id' => null]);

            $tasks = $this->getTasks()->all();

            // make sure no invalid index is given
            if ($newIndex < 0) {
                $newIndex = 0;
            } else if ($newIndex >= count($tasks) + 1) {
                $newIndex = count($tasks) - 1;
            }

            if($oldListId === null) {
                array_splice($tasks, $task->sort_order, 1);
            }
            array_splice($tasks, $newIndex, 0, [$task]);

            foreach ($tasks as $index => $item) {
                $item->updateAttributes(['sort_order' => $index]);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param $status
     * @return ActiveQueryContent
     */
    public function getTasksByStatus($status)
    {
        return $this->getTasks()->where(['task.status' => $status]);
    }

    public function isHideIfCompleted()
    {
        return true;
    }

    public function getId()
    {
       return null;
    }

    public function getTitle()
    {
        return Yii::t('TasksModule.base', 'Other Tasks');
    }

    public function getColor()
    {
        return '#708fa0';
    }

    public function getContainer()
    {
        return $this->contentContainer;
    }
}