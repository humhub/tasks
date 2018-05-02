<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: davidborn
 */

namespace humhub\modules\tasks\widgets;


use humhub\components\Widget;
use humhub\modules\tasks\models\Task;

class TaskBadge extends Widget
{
    /**
     * @var Task
     */
    public $task;

    /**
     * @var Task
     */
    public $right;

    public $includePending = true;

    public $includeCompleted = true;

    public function run()
    {
        return $this->render('taskBadge', [
            'task' => $this->task,
            'right' => $this->right,
            'includePending' => $this->includePending,
            'includeCompleted' => $this->includeCompleted
        ]);
    }

}