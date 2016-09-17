<?php

namespace humhub\modules\tasks\widgets;

use Yii;

/**
 * Shows a Task Wall Entry
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        return Task::widget(['model' => $this->contentObject, 'showCommentsColumn' => false]);
    }

}

?>