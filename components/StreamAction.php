<?php

namespace humhub\modules\tasks\components;

use Yii;
use humhub\modules\content\components\actions\ContentContainerStream;
use humhub\modules\tasks\models\Task;

class StreamAction extends ContentContainerStream
{

    /**
     * Setup additional filters
     */
    public function setupFilters()
    {
        $this->activeQuery->andWhere(['content.object_model' => Task::className()]);

        if (in_array('tasks_meAssigned', $this->filters) || in_array('tasks_open', $this->filters) || in_array('tasks_finished', $this->filters) || in_array('tasks_notassigned', $this->filters) || in_array('tasks_byme', $this->filters)) {

            $this->activeQuery->leftJoin('task', 'content.object_id=task.id AND content.object_model=:taskModel', [':taskModel' => Task::className()]);

            if (in_array('tasks_meAssigned', $this->filters)) {
                $this->activeQuery->leftJoin('task_user', 'task.id=task_user.task_id AND task_user.user_id=:userId', [':userId' => Yii::$app->user->id]);
                $this->activeQuery->andWhere('task_user.id IS NOT null');
            }

            if (in_array('tasks_notassigned', $this->filters)) {
                $this->activeQuery->andWhere("(SELECT COUNT(*) FROM task_user WHERE task_id=task.id) = 0");
            }

            if (in_array('tasks_byme', $this->filters)) {
                $this->activeQuery->andWhere(['task.created_by' => Yii::$app->user->id]);
            }

            if (in_array('tasks_open', $this->filters)) {
                $this->activeQuery->andWhere(['task.status' => Task::STATUS_OPEN]);
            }

            if (in_array('tasks_finished', $this->filters)) {
                $this->activeQuery->andWhere(['task.status' => Task::STATUS_FINISHED]);
            }
        }
    }

}

?>