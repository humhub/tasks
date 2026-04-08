<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 05.10.2018
 * Time: 20:22
 */

namespace humhub\modules\tasks\controllers;

use humhub\components\access\ControllerAccess;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\search\TaskSearchListEntry;
use Yii;
use yii\web\NotFoundHttpException;

class GlobalController extends AbstractTaskController
{
    public $requireContainer = false;

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            [ControllerAccess::RULE_LOGGED_IN_ONLY],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'filter' =>  new TaskFilter(['filters' => [TaskFilter::FILTER_ASSIGNED]]),
        ]);
    }

    public function actionFilter()
    {
        $filter = new TaskFilter(['contentContainer' => $this->contentContainer]);
        $filter->load(Yii::$app->request->get());

        return $this->asJson([
            'success' => true,
            'result' => TaskSearchList::widget(['filter' => $filter]),
        ]);
    }

    public function actionReloadFilterTask(int $id)
    {
        $task = Task::findOne($id);
        if (!$task) {
            throw new NotFoundHttpException('Task not found!');
        }

        return TaskSearchListEntry::widget([
            'task' => $task,
            'contentContainer' => $this->contentContainer,
            'filterResult' => true,
        ]);
    }

}
