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

class TaskPercentageBar extends Widget
{
    /**
     * @var Task
     */
    public $task;

    /**
     * Needed to show progress_bar in upcoming tasks and in filtered results
     * @var $filterResult
     */
    public $filterResult = false;

    public function run()
    {
        return $this->render('taskPercentageBar', [
            'task' => $this->task,
            'filterResult' => $this->filterResult
        ]);
    }

}