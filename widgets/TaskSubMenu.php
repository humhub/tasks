<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;


use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\widgets\SettingsTabs;
use Yii;

class TaskSubMenu extends SettingsTabs
{
    public $options = ['id' => 'task-space-menu', 'style' => 'border-radius:4px;margin-bottom:0px;'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $contentContainer = Yii::$app->controller->contentContainer;

        $this->items = [
            [
                'label' => Yii::t('TasksModule.base', 'Lists'),
                'url' => TaskListUrl::taskListRoot($contentContainer),
                'active' => $this->isCurrentRoute('tasks', 'list')
            ],
            [
                'label' => Yii::t('TasksModule.base', 'Search'),
                'url' => TaskListUrl::searchTask($contentContainer),
                'active' => $this->isCurrentRoute('tasks', 'search')
            ],
        ];

        parent::init();
    }

}