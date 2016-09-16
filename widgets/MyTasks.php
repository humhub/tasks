<?php

namespace humhub\modules\tasks\widgets;

use Yii;
use humhub\components\Widget;
use humhub\modules\tasks\models\Task;
use yii\helpers\Json;

/**
 * Widget for dashboard which shows todays user tasks
 */
class MyTasks extends Widget
{

    /**
     * @var int limit of tasks
     */
    public $limit = 5;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $filters = [];
        $filters['status'][] = 'active';
        $filters['user'][] = Yii::$app->user->getIdentity()->guid;
        $filters['time'] = 'all';
        $filters['showFromOtherSpaces'] = true;

        $tasks = Task::find()->applyTaskFilters($filters)->limit($this->limit)->all();

        if (count($tasks) === 0) {
            return;
        }

        $showAllUrl = $tasks[0]->content->container->createUrl('/tasks/task/show', ['filters' => Json::encode($filters)]);

        return $this->render('mytasks', [
                    'tasks' => $tasks,
                    'filters' => $filters,
                    'filtersJson' => \yii\helpers\Json::encode($filters),
                    'showAllUrl' => $showAllUrl
        ]);
    }

}

?>