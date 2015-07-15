<?php

namespace module\tasks\notifications;

use humhub\modules\notification\components\BaseNotification;

class Assigned extends BaseNotification
{

    public $moduleId = 'tasks';
    
    public $viewName = "assigned";

}

?>
