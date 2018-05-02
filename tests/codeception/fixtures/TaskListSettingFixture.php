<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\tests\codeception\fixtures;

use humhub\modules\tasks\models\checklist\TaskItem;
use humhub\modules\tasks\models\lists\TaskListSettings;
use yii\test\ActiveFixture;

class TaskListSettingFixture extends ActiveFixture
{
    public $modelClass = TaskListSettings::class;
    public $dataFile = '@tasks/tests/codeception/fixtures/data/taskListSetting.php';
   
}
