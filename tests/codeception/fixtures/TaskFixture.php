<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\tests\codeception\fixtures;

use humhub\modules\tasks\models\Task;
use yii\test\ActiveFixture;

class TaskFixture extends ActiveFixture
{
    public $modelClass = Task::class;
    public $dataFile = '@tasks/tests/codeception/fixtures/data/task.php';
    
     public $depends = [
        TaskItemFixture::class,
        TaskListSettingFixture::class,
        TaskReminderFixture::class,
        TaskUserFixture::class,
    ];
}
