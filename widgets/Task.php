<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\widgets;

use Yii;

/**
 * Description of Task
 *
 * @author Luke
 */
class Task extends \humhub\components\Widget
{

    /**
     * @var \humhub\modules\tasks\models\Task
     */
    public $model;

    /**
     * @var boolean show comments column
     */
    public $showCommentsColumn = true;

    /**
     * @inheritdoc
     */
    public function run()
    {

        $currentUserAssigned = false;

        // Check if current user is assigned to this task
        foreach ($this->model->assignedUsers as $au) {
            if ($au->id == Yii::$app->user->id) {
                $currentUserAssigned = true;
                break;
            }
        }

        /**
         * Status flags used in javascript
         */
        $statusFlags = [];
        $statusFlags[\humhub\modules\tasks\models\Task::STATUS_ACTIVE] = 'active';
        $statusFlags[\humhub\modules\tasks\models\Task::STATUS_COMPLETED] = 'completed';
        $statusFlags[\humhub\modules\tasks\models\Task::STATUS_DEFERRED] = 'deferred';
        $statusFlags[\humhub\modules\tasks\models\Task::STATUS_CANCELLED] = 'cancelled';


        return $this->render('task', [
                    'task' => $this->model,
                    'currentUserAssigned' => $currentUserAssigned,
                    'contentContainer' => $this->model->content->contentContainer->getPolymorphicRelation(),
                    'statusTexts' => \humhub\modules\tasks\models\Task::getStatusTexts(),
                    'statusFlags' => $statusFlags,
                    'showCommentsColumn' => $this->showCommentsColumn
        ]);
    }

}
