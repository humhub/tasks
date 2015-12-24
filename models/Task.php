<?php

namespace humhub\modules\tasks\models;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\tasks\models\TaskUser;

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property integer $id
 * @property string $title
 * @property string $deadline
 * @property integer $max_users
 * @property integer $precent
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Task extends ContentActiveRecord implements \humhub\modules\search\interfaces\Searchable
{

    public $assignedUserGuids = "";

    // Status
    const STATUS_OPEN = 1;
    const STATUS_FINISHED = 5;

    public $wallEntryClass = 'humhub\modules\tasks\widgets\WallEntry';
    public $autoAddToWall = true;

    public static function tableName()
    {
        return 'task';
    }

    public function rules()
    {
        return array(
            [['title'], 'required'],
            [['max_users', 'percent'], 'integer'],
            [['deadline'], \humhub\libs\DbDateValidator::className(), 'format' => Yii::$app->params['formatter']['defaultDateFormat']],
            [['max_users', 'assignedUserGuids'], 'safe'],
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array(
            'title' => Yii::t('TasksModule.base','Title'),
            'assignedUserGuids' => Yii::t('TasksModule.base','Assigned user(s)'),
            'deadline' => Yii::t('TasksModule.base','Deadline'),
        );
    }

    public function getTaskUsers()
    {
        $query = $this->hasMany(TaskUser::className(), ['task_id' => 'id']);
        return $query;
    }

    public function getAssignedUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
                        ->viaTable('task_user', ['task_id' => 'id']);
    }

    public function beforeDelete()
    {
        foreach ($this->taskUsers as $taskUser) {
            $taskUser->delete();
        }

        return parent::beforeDelete();
    }

    public function getUrl()
    {
        return $this->content->container->createUrl('/tasks/task/show', array('id' => $this->id));
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);


        foreach (explode(",", $this->assignedUserGuids) as $userGuid) {
            $f = false;
            foreach ($this->assignedUsers as $user) {
                if ($user->guid == trim($userGuid)) {
                    $f = true;
                }
            }

            if ($f == false) {
                $this->assignUser(User::findOne(['guid' => trim($userGuid)]));
            }
        }


        foreach ($this->assignedUsers as $user) {
            if (strpos($this->assignedUserGuids, $user->guid) === false) {
                $this->unassignUser($user);
            }
        }
    }

    public function afterFind()
    {

        foreach ($this->assignedUsers as $user) {
            $this->assignedUserGuids .= $user->guid . ",";
        }


        return parent::afterFind();
    }

    public function assignUser($user = "")
    {
        if ($user != "") {

            $au = TaskUser::findOne(array('task_id' => $this->id, 'user_id' => $user->id));
            if ($au == null) {

                $au = new TaskUser;
                $au->task_id = $this->id;
                $au->user_id = $user->id;
                $au->save();

                return true;
            }
        }
        return false;
    }

    public function unassignUser($user = "")
    {
        if ($user == "")
            $user = Yii::$app->user->getIdentity();

        $au = TaskUser::findOne(array('task_id' => $this->id, 'user_id' => $user->id));
        if ($au != null && $au->delete()) {
            return true;
        }
        return false;
    }

    public function changePercent($newPercent)
    {
        if ($this->percent != $newPercent) {
            $this->percent = $newPercent;
            $this->save();
        }

        if ($newPercent == 100) {
            $this->changeStatus(Task::STATUS_FINISHED);
        }

        if ($this->percent != 100 && $this->status == Task::STATUS_FINISHED) {
            $this->changeStatus(Task::STATUS_OPEN);
        }

        return true;
    }

    public function changeStatus($newStatus)
    {
        $this->status = $newStatus;

        if ($newStatus == Task::STATUS_FINISHED) {

            $activity = new \humhub\modules\tasks\activities\Finished();
            $activity->source = $this;
            $activity->originator = Yii::$app->user->getIdentity();
            $activity->create();

            if ($this->created_by != Yii::$app->user->id) {
                $notification = new \humhub\modules\tasks\notifications\Finished();
                $notification->source = $this;
                $notification->originator = Yii::$app->user->getIdentity();
                $notification->send($this->content->user);
            }

            $this->percent = 100;
        } else {
            // Try to delete TaskFinishedNotification if exists
            $notification = new \humhub\modules\tasks\notifications\Finished();
            $notification->source = $this;
            $notification->delete($this->content->user);
        }

        $this->save();

        return true;
    }

    public function hasDeadline()
    {
        if ($this->deadline != '0000-00-00 00:00:00' && $this->deadline != '' && $this->deadline != 'NULL') {
            return true;
        }
        return false;
    }

    public static function GetUsersOpenTasks()
    {
        $query = self::find();
        $query->leftJoin('task_user', 'task.id=task_user.task_id');
        $query->where(['task_user.user_id' => Yii::$app->user->id, 'task.status' => self::STATUS_OPEN]);

        return $query->all();
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return Yii::t('TasksModule.models_Task', "Task");
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {
        return array(
            'title' => $this->title,
        );
    }

    public function isOverdue()
    {
        if (!$this->hasDeadline()) {
            return false;
        }

        return (strtotime($this->deadline) < time());
    }

}
