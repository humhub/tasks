<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: buddha
 * Date: 01.07.2017
 * Time: 17:18
 */

namespace humhub\modules\tasks\models\forms;


use Yii;
use humhub\modules\tasks\models\Task;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\models\user\TaskUser;
use yii\base\Model;

class TaskFilter extends Model
{
    const FILTER_TITLE = 'title';
    const FILTER_OVERDUE = 'overdue';
    const FILTER_ASSIGNED = 'assigned';
    const FILTER_RESPONSIBLE = 'responsible';
    const FILTER_MINE = 'mine';
    const FILTER_STATE = 'state';
    const FILTER_SPACE = 'spaces';

    public $filters = [];

    public $states = [];

    public $spaces = [];

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var string
     */
    public $title;

    /**
     * @var int
     */
    public $own;

    public function rules()
    {
        return [
            ['title', 'string'],
            [['filters', 'states', 'spaces'], 'safe'],
            [['taskAssigned', 'taskResponsible', 'own', 'status', 'overdue'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'Filter tasks'),
            'status' => Yii::t('TasksModule.models_forms_TaskFilter', 'Status'),
            'overdue' => Yii::t('TasksModule.models_forms_TaskFilter', 'Overdue'),
            'taskAssigned' => Yii::t('TasksModule.models_forms_TaskFilter', 'I\'m assigned'),
            'taskResponsible' => Yii::t('TasksModule.models_forms_TaskFilter', 'I\'m responsible'),
            'own' => Yii::t('TasksModule.models_forms_TaskFilter', 'Created by me'),
        ];
    }

    public function query()
    {
        $query = Task::find()->readable();

        if($this->contentContainer) {
            $query->contentContainer($this->contentContainer);
        } else if (!empty($this->spaces)) {
            $query->joinWith(['content', 'content.contentContainer', 'content.createdBy']);
            $query->andWhere( ['IN', 'contentcontainer.guid', $this->spaces]);
        }

        if($this->isFilterActive(static::FILTER_OVERDUE)) {
            $query->andWhere('task.end_datetime < DATE(NOW())');
            $query->andWhere(['!=', 'task.status', Task::STATUS_COMPLETED]);
        }

        if(!empty($this->states)) {
             $query->andWhere(['in', 'task.status', $this->states]);
        }

        if(!empty($this->title)) {
            $query->andWhere(['like', 'title', $this->title]);
        }

        if ($this->isFilterActive(static::FILTER_ASSIGNED)) {
            $subQuery = TaskUser::find()
                ->where('task_user.task_id=task.id')
                ->andWhere(['task_user.user_id' => Yii::$app->user->id, 'task_user.user_type' => Task::USER_ASSIGNED]);
            $query->andWhere(['exists', $subQuery]);
        }

        if ($this->isFilterActive(static::FILTER_RESPONSIBLE)) {
            $subQuery = TaskUser::find()
                ->where('task_user.task_id=task.id')
                ->andWhere(['task_user.user_id' => Yii::$app->user->id, 'task_user.user_type' => Task::USER_RESPONSIBLE]);
            $query->andWhere(['exists', $subQuery]);
        }

        if($this->isFilterActive(static::FILTER_MINE)) {
            $query->andWhere(['content.created_by' => Yii::$app->user->identity->contentcontainer_id]);
        }

        $query->orderBy(['task.status' => SORT_ASC, 'task.scheduling' => SORT_DESC, 'task.end_datetime' => SORT_ASC]);

        return $query;
    }

    public function isFilterActive($filter)
    {
        return in_array($filter, $this->filters);
    }
}