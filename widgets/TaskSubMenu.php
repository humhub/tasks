<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;


use humhub\widgets\SettingsTabs;
use Yii;

class TaskSubMenu extends SettingsTabs
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $contentContainer = Yii::$app->controller->contentContainer;

        $this->items = [
            [
                'label' => Yii::t('TasksModule.base', 'Lists'),
                'url' => $contentContainer->createUrl('/tasks/list'),
                'active' => $this->isCurrentRoute('tasks', 'list')
            ],
            [
                'label' => Yii::t('TasksModule.base', 'Search'),
                'url' => $contentContainer->createUrl('/tasks/search'),
                'active' => $this->isCurrentRoute('tasks', 'search')
            ],
        ];

        parent::init();
    }

}