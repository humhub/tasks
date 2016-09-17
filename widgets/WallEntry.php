<?php

namespace humhub\modules\tasks\widgets;

use Yii;

/**
 * Shows a Task Wall Entry
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{
    
    /**
     * @var type 
     */
    public $task;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $user = $this->contentObject->content->user;

        return $this->render('entry', array(
                    'task' => $this->contentObject,
        ));
    }

}

?>