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
 * @property int $id
 * @property int $sort_order
 * @property int $tag_id
 * @property int $hide_if_completed
 * @property int $updated_at
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
            [['hide_if_completed', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'hide_if_completed' => Yii::t('TasksModule.base', 'Hide this list once all tasks are completed'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->hide_if_completed === null) {
            $this->hide_if_completed = 1;
        }
    }
}
