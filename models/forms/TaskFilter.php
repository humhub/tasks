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

use humhub\libs\DbDateValidator;
use humhub\modules\tasks\CalendarUtils;
use Yii;
use humhub\modules\tasks\models\Task;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\models\user\TaskUser;
use yii\base\Model;

class TaskFilter extends Model
{
    public const FILTER_TITLE = 'title';
    public const FILTER_OVERDUE = 'overdue';
    public const FILTER_ASSIGNED = 'assigned';
    public const FILTER_RESPONSIBLE = 'responsible';
    public const FILTER_MINE = 'mine';
    public const FILTER_STATE = 'state';
    public const FILTER_SPACE = 'spaces';
    public const FILTER_DATE_START = 'date_start';
    public const FILTER_DATE_END = 'date_end';

    public $filters = [];

    public $states = [];

    public $spaces = [];

    public $date_start;

    public $date_end;


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
            [['title', 'date_start', 'date_end'], 'string'],
            [['filters', 'states', 'spaces'], 'safe'],
            [['date_start'], DbDateValidator::class],
            [['date_end'], DbDateValidator::class],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('TasksModule.base', 'Filter tasks'),
            'status' => Yii::t('TasksModule.base', 'Status'),
            'overdue' => Yii::t('TasksModule.base', 'Overdue'),
            'taskAssigned' => Yii::t('TasksModule.base', 'I\'m assigned'),
            'taskResponsible' => Yii::t('TasksModule.base', 'I\'m responsible'),
            'own' => Yii::t('TasksModule.base', 'Created by me'),
        ];
    }

    public function query()
    {
        $this->validate();

        $query = Task::find()->readable()->andWhere('content.archived = 0');


        if ($this->contentContainer) {
            $query->contentContainer($this->contentContainer);
        } elseif (!empty($this->spaces)) {
            $query->joinWith(['content', 'content.contentContainer', 'content.createdBy']);
            $query->andWhere(['IN', 'contentcontainer.guid', $this->spaces]);
        } else {
            // exclude archived content from global view
            $query->andWhere('space.status = 1 OR space.status IS NULL');
        }

        if ($this->isFilterActive(static::FILTER_OVERDUE)) {
            $query->andWhere('task.end_datetime < DATE(NOW())');
            $query->andWhere(['!=', 'task.status', Task::STATUS_COMPLETED]);
        }

        if (!empty($this->states)) {
            $query->andWhere(['in', 'task.status', $this->states]);
        }

        if (!empty($this->title)) {
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

        if ($this->isFilterActive(static::FILTER_MINE)) {
            $query->andWhere(['content.created_by' => Yii::$app->user->id]);
        }

        if (! empty($this->date_start) && ! empty($this->date_end)) {
            $query->andWhere(
                ['or',
                    ['and',
                        CalendarUtils::getStartCriteria($this->date_start, 'start_datetime', '>='),
                        CalendarUtils::getStartCriteria($this->date_end, 'start_datetime', '<='),
                    ],
                    ['and',
                        CalendarUtils::getEndCriteria($this->date_start, 'end_datetime', '>='),
                        CalendarUtils::getEndCriteria($this->date_end, 'end_datetime', '<='),
                    ],
                ],
            );
        } elseif (! empty($this->date_start)) {
            $query->andWhere(CalendarUtils::getStartCriteria($this->date_start, 'start_datetime'));
        } elseif (! empty($this->date_end)) {
            $query->andWhere(CalendarUtils::getEndCriteria($this->date_end, 'end_datetime'));
        }

        $query->orderBy(['task.status' => SORT_ASC, 'task.scheduling' => SORT_DESC, 'task.end_datetime' => SORT_ASC]);

        return $query;
    }

    public function isFilterActive($filter)
    {
        return in_array($filter, $this->filters);
    }
}
