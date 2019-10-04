<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models\user;

use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\notifications\AddResponsibleNotification;
use humhub\modules\tasks\notifications\AssignedNotification;
use Yii;
use humhub\modules\user\models\User;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "task_user".
 *
 * The followings are the available columns in table 'task_user':
 * @property integer $id
 * @property integer $task_id
 * @property integer $user_id
 * @property integer $user_type
 */

class TaskUser extends ActiveRecord
{

    public $sendNotificationOnCreation = true;

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
        return [
            [['task_id', 'user_type'], 'required'],
            [['task_id', 'user_id', 'user_type'], 'integer'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task',
            'user_id' => 'User',
            'user_type' => 'User Type'
        ];
    }

    public function getUser()
    {
        if ($this->user_id) {
            return User::findOne(['id' => $this->user_id]);
        }
        return null;
    }

    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($insert) {
            $this->notifyCreated();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Notify users about created task
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function notifyCreated()
    {
        if(!$this->sendNotificationOnCreation) {
            return;
        }

        $source = $this->task;
        $target = $this->user;
        $from = Yii::$app->user->getIdentity();

        if($target->is($from)) {
            return;
        }

        if($this->user_type === Task::USER_ASSIGNED) {
            AssignedNotification::instance()->about($source)->delete($target);
            AssignedNotification::instance()->from($from)->about($source)->send($target);
        } else if($this->user_type === Task::USER_RESPONSIBLE) {
            AddResponsibleNotification::instance()->about($source)->delete($target);
            AddResponsibleNotification::instance()->from($from)->about($source)->send($target);
        }
    }

}
