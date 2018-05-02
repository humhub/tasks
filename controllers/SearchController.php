<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\TaskPicker;
use Yii;
use yii\web\Controller;


/**
 * todo.
 * Search Controller provides action for searching tasks.
 *
 * @author davidborn
 */
class SearchController extends ContentContainerController
{

    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'acl' => [
//                'class' => \humhub\components\behaviors\AccessControl::className(),
//            ]
//        ];
//    }

    public function actionIndex()
    {
        return $this->render("index", [
            'canEdit' => $this->contentContainer->getPermissionManager()->can(new ManageTasks()),
            'contentContainer' => $this->contentContainer,
            'filter' => new TaskFilter(['contentContainer' => $this->contentContainer])
        ]);
    }

    public function actionFilterTasks()
    {
        $filter = new TaskFilter(['contentContainer' => $this->contentContainer]);
        $filter->load(Yii::$app->request->post());

        return $this->asJson([
            'success' => true,
            'output' => TaskSearchList::widget(['filter' => $filter])
        ]);
    }

    /**
     * JSON Search for Users
     *
     * Returns an array of users with fields:
     *  - guid
     *  - displayName
     *  - image
     *  - profile link
     */
    public function actionJson()
    {
        Yii::$app->response->format = 'json';
        
        return TaskPicker::filter([
            'keyword' => Yii::$app->request->get('keyword'),
//            'fillUser' => true,
//            'disableFillUser' => false
        ]);
    }

}

?>
