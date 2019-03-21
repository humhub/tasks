<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 05.10.2018
 * Time: 20:22
 */

namespace humhub\modules\tasks\controllers;

use humhub\modules\content\components\ContentContainerController;
use Yii;
use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\widgets\search\TaskSearchList;

class GlobalController extends AbstractTaskController
{
    public $requireContainer = false;

    public function getAccessRules()
    {
        return [
            [ControllerAccess::RULE_LOGGED_IN_ONLY]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'filter' =>  new TaskFilter(['filters' => [TaskFilter::FILTER_ASSIGNED]])
        ]);
    }

    public function actionFilter()
    {
        $filter = new TaskFilter(['contentContainer' => $this->contentContainer]);
        $filter->load(Yii::$app->request->get());

        return $this->asJson([
            'success' => true,
            'result' => TaskSearchList::widget(['filter' => $filter])
        ]);
    }

}