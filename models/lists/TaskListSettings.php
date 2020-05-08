<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models\lists;


use humhub\modules\content\models\ContentTagAddition;
use Yii;

/**
 * @property integer $id
 * @property integer $sort_order
 * @property integer $tag_id
 * @property integer $hide_if_completed
 * @property integer $updated_at
 */
class TaskListSettings extends ContentTagAddition
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'task_list_setting';
    }

    public function rules()
    {
        return [
            [['hide_if_completed', 'updated_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'hide_if_completed' => Yii::t('TasksModule.base', 'Hide this list once all tasks are completed')
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if($this->hide_if_completed === null) {
            $this->hide_if_completed = 1;
        }
    }
}