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


use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\user\TaskUser;
use Yii;
use yii\base\Model;

class TaskFilter extends Model
{
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
    public $status = Task::STATUS_ALL;

    /**
     * @var int
     */
    public $overdue = false;

    /**
     * @var int
     */
    public $taskAssigned = 1;

    /**
     * @var int
     */
    public $taskResponsible;

    /**
     * @var int
     */
    public $own;

    public function rules()
    {
        return [
            ['title', 'string'],
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
        $user = Yii::$app->user->getIdentity();

        $query = Task::find()->contentContainer($this->contentContainer);

        if($this->overdue) {
            $query->andWhere('task.end_datetime < DATE(NOW())');
            $query->andWhere(['!=', 'task.status', Task::STATUS_COMPLETED]);
        }

        if($this->status != Task::STATUS_ALL) {
            $query->andWhere(['task.status' => $this->status]);
        }

        if(!empty($this->title)) {
            $query->andWhere(['like', 'title', $this->title]);
        }

        if ($this->taskAssigned) {
            $subQuery = TaskUser::find()
                ->where('task_user.task_id=task.id')
                ->andWhere(['task_user.user_id' => $user->id, 'task_user.user_type' => Task::USER_ASSIGNED]);
            $query->andWhere(['exists', $subQuery]);
        }

        if ($this->taskResponsible) {
            $subQuery = TaskUser::find()
                ->where('task_user.task_id=task.id')
                ->andWhere(['task_user.user_id' => $user->id, 'task_user.user_type' => Task::USER_RESPONSIBLE]);
            $query->andWhere(['exists', $subQuery]);
        }

        if($this->own) {
            $query->andWhere(['content.created_by' => $user->contentcontainer_id]);
        }

        $query->orderBy(['task.status' => SORT_ASC, 'task.scheduling' => SORT_DESC, 'task.end_datetime' => SORT_ASC]);

        return $query;
    }
}