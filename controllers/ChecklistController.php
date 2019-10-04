<?php


namespace humhub\modules\tasks\controllers;


use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
use humhub\modules\tasks\models\checklist\CheckForm;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\user\models\User;
use Yii;

class ChecklistController extends AbstractTaskController
{
    public function getAccessRules()
    {
        return [
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_MEMBER, User::USERGROUP_SELF]]
        ];
    }

    public function actionCheckItem($id, $taskId)
    {
        $model = new CheckForm(['itemId' => $id, 'taskId' => $taskId]);
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson([
                'success' => true,
                'item' => [
                    'checked' => $model->checked,
                    'statusChanged' => $model->statusChanged,
                    'sortOrder' => $model->item->sort_order
                ]
            ]);
        }

        return $this->asJson(['success' => false, 'message' => ($model->hasErrors()) ? $model->getFirstErrors() : '']);
    }
}