<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\controllers;

use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\widgets\search\TaskSearchList;
use humhub\modules\tasks\widgets\TaskPicker;
use humhub\modules\user\models\User;
use Yii;

/**
 * todo.
 * Search Controller provides action for searching tasks.
 *
 * @author davidborn
 */
class SearchController extends AbstractTaskController
{

    public function getAccessRules()
    {
        return [
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_MEMBER, User::USERGROUP_SELF]]
        ];
    }

    public function actionIndex()
    {
        return $this->render("index", [
            'canEdit' =>$this->canManageTasks(),
            'contentContainer' => $this->contentContainer,
            'filter' => new TaskFilter(['contentContainer' => $this->contentContainer, 'filters' => [TaskFilter::FILTER_ASSIGNED]])
        ]);
    }

    public function actionFilterTasks()
    {
        $filter = new TaskFilter(['contentContainer' => $this->contentContainer]);
        $filter->load(Yii::$app->request->post());

        return $this->asJson([
            'success' => true,
            'output' => TaskSearchList::widget(['filter' => $filter, 'canEdit' => $this->canManageTasks()])
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
        return $this->asJson(TaskPicker::filter([
            'keyword' => Yii::$app->request->get('keyword'),
        ]));
    }

}

