<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: buddha
 * Date: 01.07.2017
 * Time: 12:22
 */

namespace humhub\modules\tasks\widgets\lists;


use humhub\components\Widget;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\meeting\models\forms\MeetingFilter;
use humhub\modules\tasks\helpers\TaskListUrl;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\permissions\ManageTasks;
use Yii;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

class CompletedTaskListView extends Widget
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    public function run()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TaskList::findHiddenLists($this->contentContainer),
            'pagination' => [
                'pageSize' => 10,
                'route' => TaskListUrl::ROUTE_LOAD_CLOSED_LISTS
            ],
        ]);

        return  ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '@tasks/widgets/lists/views/_closedItem',
            'viewParams' => [
                'contentContainer' => $this->contentContainer,
                'canEdit' => $this->contentContainer->can(ManageTasks::class)
            ],
            'options' => [
                'tag' => 'ul',
                'class' => 'media-list'
            ],
            'itemOptions' => [
                'tag' => 'li'
            ],
            'layout' => "{items}\n<li class=\"pagination-container\">{pager}</li>"
        ]);
    }

}