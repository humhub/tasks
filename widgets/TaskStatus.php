<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\widgets;

use humhub\components\Widget;
use humhub\helpers\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\ui\icon\widgets\Icon;
use Yii;

class TaskStatus extends Widget
{
    /**
     * @var Task
     */
    public $task;

    public function run()
    {
        if ($this->task->status == Task::STATUS_PENDING) {
            $result = $this->renderStatus(Yii::t('TasksModule.base', 'Pending'), 'info-circle', 'default');
        } elseif ($this->task->status == Task::STATUS_IN_PROGRESS) {
            $result = $this->renderStatus(Yii::t('TasksModule.base', 'In Progress'), 'pencil-square', 'accent');
        } elseif ($this->task->status == Task::STATUS_PENDING_REVIEW) {
            $result = $this->renderStatus(Yii::t('TasksModule.base', 'Pending Review'), 'eye', 'warning');
        } elseif ($this->task->status == Task::STATUS_COMPLETED) {
            $result = $this->renderStatus(Yii::t('TasksModule.base', 'Completed'), 'check-square', 'success');
        }

        if ($this->task->isOverdue()) {
            $result .= ' &nbsp; ' . $this->renderStatus(Yii::t('TasksModule.base', 'Overdue'), 'exclamation-triangle', 'danger', 'taskDeadlineStatus');
        }

        return $result;
    }

    private function renderStatus(string $text, string $icon, string $style, ?string $id = null): string
    {
        $options = ['class' => 'text-uppercase text-' . $style];

        if ($id !== null) {
            $options['id'] = $id;
        }

        return Html::tag('span', Icon::get($icon) . ' ' .Html::encode($text), $options);
    }

}
