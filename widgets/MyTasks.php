<?php

namespace humhub\modules\tasks\widgets;

use Yii;
use yii\helpers\Json;
use humhub\modules\tasks\models\Task;
use humhub\components\View;
use humhub\widgets\BaseSidebarItem;

/**
 * Widget for dashboard which shows todays user tasks
 */
class MyTasks extends BaseSidebarItem
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
        $this->title = Yii::t('TasksModule.base', '<strong>My</strong> tasks');

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
        $this->view->registerJs('$("#tasksList").data("filters", ' . Json::encode($filters) . ');', View::POS_END);

        return $this->render('mytasks', [
                    'tasks' => $tasks,
                    'filters' => $filters,
                    'filtersJson' => \yii\helpers\Json::encode($filters),
                    'showAllUrl' => $showAllUrl
        ]);
    }

}

?>