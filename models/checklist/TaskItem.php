<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models\checklist;

use Yii;
use humhub\components\ActiveRecord;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\permissions\ManageTasks;
/**
 * This is the model class for table "task_item".
 *
 * The followings are the available columns in table 'task_item':
 * @property integer $id
 * @property integer $task_id
 * @property string $title
 * @property string $description
 * @property integer $completed
 * @property integer $sort_order
 *
 * @property Task $task
 */
class TaskItem extends ActiveRecord
{
    /**
     * @inheritdocs
     */
    protected $managePermission = ManageTasks::class;

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'task_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'title'], 'required'],
            [['task_id', 'sort_order', 'completed'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['description', 'notes'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Yii::t('TasksModule.models_taskitem', 'Title'),
            'completed' => Yii::t('TasksModule.models_taskitem', 'Completed'),
        ];
    }

    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }


    public static function filterValidItems($itemArr)
    {
        if($itemArr == null) {
            return [];
        }

        $result = [];
        foreach ($itemArr as $key => $itemText) {
            if($itemText != null && $itemText !== '') {
                $result[$key] = $itemText;
            }
        }
        return $result;
    }
}