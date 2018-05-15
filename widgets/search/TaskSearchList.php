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
        $tasksProvider = new ActiveDataProvider([
            'query' => $this->filter->query(),
            'pagination' => [
                'pageSize' => 30,
                'route' => '/tasks/search/filter-tasks'
            ],
        ]);

        return  ListView::widget([
            'dataProvider' => $tasksProvider,
            'itemView' => '@tasks/widgets/search/views/_item',
            'viewParams' => [
                'contentContainer' => $this->filter->contentContainer,
                'canEdit' => $this->canEdit
            ],
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