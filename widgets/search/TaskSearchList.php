<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: davidborn
 */

namespace humhub\modules\tasks\widgets\search;


use humhub\components\Widget;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\Module;
use Yii;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

class TaskSearchList extends Widget
{
    /**
     * @var TaskFilter
     */
    public $filter;

    public $canEdit;

    public function run()
    {
        /* @var $module Module */
        $module = Yii::$app->getModule('tasks');

        $tasksProvider = new ActiveDataProvider([
            'query' => $this->filter->query(),
            'pagination' => [
                'pageSize' => $module->searchPaginationSize,
                'route' => '/tasks/global/filter'
            ],
        ]);

        return  ListView::widget([
            'dataProvider' => $tasksProvider,
            'emptyText' => Yii::t('TasksModule.base', 'No results found for the given filter.'),
            'itemView' => '@tasks/widgets/search/views/_item',
            'viewParams' => [
                'contentContainer' => $this->filter->contentContainer,
                'canEdit' => $this->canEdit
            ],
            'summary'=>'',
            'options' => [
                'tag' => 'ul',
                'class' => 'media-list'
            ],
            'itemOptions' => [
                'tag' => 'li'
            ],
            'layout' => "{summary}\n{items}\n<div class=\"pagination-container\">{pager}</div>"
        ]);
    }

}