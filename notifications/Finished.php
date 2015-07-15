<?php

namespace module\tasks\notifications;

use humhub\modules\notification\components\BaseNotification;

class Finished extends BaseNotification
{

    public $moduleId = 'tasks';
    public $viewName = "finished";

}

?>
