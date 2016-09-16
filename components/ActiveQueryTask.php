<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\components;

use Yii;
use yii\db\Expression;
use humhub\modules\user\models\User;
use humhub\modules\tasks\models\Task;
use humhub\modules\content\components\ActiveQueryContent;

/**
 * Description of TaskActiveQuery
 *
 * @author Luke
 */
class ActiveQueryTask extends ActiveQueryContent
{

    public function init()
    {
        parent::init();
        $this->orderBy([new Expression('ISNULL(start_date)'), 'start_date' => SORT_ASC, 'task.status' => SORT_ASC, 'task.title' => SORT_ASC]);
    }

    public function applyTaskFilters($filters)
    {
        if (empty($filters)) {
            return $this;
        }
        
        if (isset($filters['time'])) {
            $this->applyTimeFilters($filters['time']);
        }
        if (isset($filters['status'])) {
            $this->applyStatusFilters($filters['status']);
        }


        if (isset($filters['showUnassigned']) && $filters['showUnassigned'] == true) {
            $this->applyUnassignedUserFilter();
        } elseif (isset($filters['user'])) {
            $this->applyUserFilters($filters['user']);
        }

        if (isset($filters['showFromOtherSpaces']) && $filters['showFromOtherSpaces'] == true) {
            $this->userRelated([self::USER_RELATED_SCOPE_SPACES]);
        } else {
            $this->contentContainer(Yii::$app->controller->contentContainer);
        }

        return $this;
    }

    public function applyUnassignedUserFilter()
    {
        $this->leftJoin('task_user', 'task_user.task_id=task.id');
        $this->andWhere(['IS', 'task_user.id', new Expression('NULL')]);
    }

    /**
     * Applies filter for users
     * 
     * @param array $userGuids of user guids or user records
     */
    public function applyUserFilters($userGuids = [])
    {

        $userIds = [];
        $conditions = ['OR'];
        foreach (array_unique($userGuids) as $guid) {
            if (!$guid instanceof User) {
                $user = User::findOne(['guid' => $guid]);
            } else {
                $user = $guid;
            }

            $userIds[] = $user->id;
            $this->leftJoin('task_user tu' . $user->id, 'task.id=tu' . $user->id . '.task_id AND tu' . $user->id . '.user_id=:userId' . $user->id, [':userId' . $user->id => $user->id]);
            $conditions[] = ['tu' . $user->id . '.user_id' => $user->id];
        }
        $this->andWhere($conditions);

        #$this->orderBy('xyz');
    }

    public function applyTimeFilters($time)
    {
        if ($time == 'today') {
            $this->andWhere(['between', new Expression('CURDATE()'), new Expression('start_date'), new Expression('deadline')]);
        } elseif ($time == 'week') {
            $day = date('w');
            $weekStartDate = date('Y-m-d', strtotime('-' . $day . ' days'));
            $weekEndDate = date('Y-m-d', strtotime('+' . (6 - $day) . ' days'));
            $this->addTimeRangeFilter($weekStartDate, $weekEndDate);
        } elseif ($time == 'month') {
            $this->addTimeRangeFilter(date('Y-m-01'), date('Y-m-t'));
        } elseif ($time == 'unscheduled') {
            $this->andWhere(['IS', 'deadline', new Expression('NULL')]);
        }
    }

    /**
     * Adds status filter to the task query
     * 
     * @param array $status
     */
    public function applyStatusFilters($status = [])
    {
        $includeStatus = [];
        if (in_array('active', $status)) {
            $includeStatus[] = Task::STATUS_ACTIVE;
        }
        if (in_array('completed', $status)) {
            $includeStatus[] = Task::STATUS_COMPLETED;
        }
        if (in_array('deferred', $status)) {
            $includeStatus[] = Task::STATUS_DEFERRED;
        }
        if (in_array('cancelled', $status)) {
            $includeStatus[] = Task::STATUS_CANCELLED;
        }

        if (count($includeStatus) !== 0) {
            $this->andWhere(['task.status' => $includeStatus]);
        }
    }

    public function addTimeRangeFilter($startDate, $endDate)
    {
        $this->andWhere(
                ['or',
                    ['and',
                        ['>=', 'start_date', $startDate],
                        ['<=', 'start_date', $endDate]
                    ],
                    ['and',
                        ['>=', 'deadline', $startDate],
                        ['<=', 'deadline', $endDate]
                    ]
                ]
        );
        $this->andWhere('deadline IS NOT NULL');
    }

}
