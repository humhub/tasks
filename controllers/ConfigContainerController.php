<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\controllers;

use humhub\modules\tasks\models\forms\ConfigureContainerForm;
use humhub\modules\content\components\ContentContainerController;
use Yii;

class ConfigContainerController extends ContentContainerController
{

    public function actionIndex()
    {
        $form = new ConfigureContainerForm(['contentContainer' => $this->contentContainer]);

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            $this->view->saved();
        }

        return $this->render('index', ['model' => $form]);
    }
}
