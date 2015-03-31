<?php

/**
 * PollsStreamAction is modified version of the StreamAction to show only objects
 * of type Poll.
 *
 * This Action is inserted in PollController and shows with interaction of the
 * PollStreamWidget the Poll Stream.
 *
 * @package humhub.modules.polls
 * @since 0.5
 * @author Luke
 */
class TasksStreamAction extends ContentContainerStreamAction
{

    /**
     * Setup additional filters
     */
    public function setupFilters()
    {
        $this->criteria->condition .= " AND object_model='Task'";


        if (in_array('tasks_meAssigned', $this->filters) || in_array('tasks_open', $this->filters) || in_array('tasks_finished', $this->filters) || in_array('tasks_notassigned', $this->filters) || in_array('tasks_byme', $this->filters)) {

            $this->criteria->join .= " LEFT JOIN task ON content.object_id=task.id AND content.object_model = 'Task'";

            if (in_array('tasks_meAssigned', $this->filters)) {
                $this->criteria->join .= " LEFT JOIN task_user ON task.id=task_user.task_id AND task_user.user_id= '" . Yii::app()->user->id . "'";
                $this->criteria->condition .= " AND task_user.id is not null";
            }

            if (in_array('tasks_notassigned', $this->filters)) {
                $this->criteria->condition .= " AND (SELECT COUNT(*) FROM task_user WHERE task_id=task.id) = 0 ";
            }

            if (in_array('tasks_byme', $this->filters)) {
                $this->criteria->condition .= " AND task.created_by = '" . Yii::app()->user->id . "'";
            }

            if (in_array('tasks_open', $this->filters)) {
                $this->criteria->condition .= " AND task.status = '" . Task::STATUS_OPEN . "'";
            }

            if (in_array('tasks_finished', $this->filters)) {
                $this->criteria->condition .= " AND task.status = '" . Task::STATUS_FINISHED . "'";
            }
        }
    }

}
?>