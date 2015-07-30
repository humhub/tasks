<?php

namespace humhub\modules\tasks\models;

use Yii;
use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use humhub\modules\tasks\models\Task;

/**
 * This is the model class for table "task_user".
 *
 * The followings are the available columns in table 'task_user':
 * @property integer $id
 * @property string $task_id
 * @property string $space_id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class TaskUser extends ActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'task_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(['task_id', 'user_id'], 'required'),
            array(['task_id', 'user_id'], 'integer'),
        );
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $notification = new \humhub\modules\tasks\notifications\Assigned();
            $notification->source = $this->task;
            $notification->originator = Yii::$app->user->getIdentity();
            $notification->send($this->user);
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        $notification = new \humhub\modules\tasks\notifications\Assigned();
        $notification->source = $this->task;
        $notification->send($this->user);

        return parent::beforeDelete();
    }

}
