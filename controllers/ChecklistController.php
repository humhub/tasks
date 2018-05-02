<?php


namespace humhub\modules\tasks\controllers;


use humhub\modules\tasks\models\checklist\CheckForm;
use Yii;

class ChecklistController extends AbstractTaskController
{
    public function actionCheckItem($id, $taskId)
    {
        $model = new CheckForm(['itemId' => $id, 'taskId' => $taskId]);
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->asJson([
                'success' => true,
                'item' => [
                    'checked' => $model->checked,
                    'statChanged' => $model->statusChanged,
                    'sortOrder' => $model->item->sort_order
                ]
            ]);
        }

        return $this->asJson(['success' => false, 'message' => ($model->hasErrors()) ? $model->getFirstErrors() : '']);
    }
}