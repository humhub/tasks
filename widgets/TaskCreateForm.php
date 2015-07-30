<?php

namespace humhub\modules\tasks\widgets;

use Yii;
use humhub\components\Widget;

class TaskCreateForm extends Widget
{

    public $mode = 'panel'; // or modal

    public function run()
    {
        return $this->render('create', ['mode' => $this->mode]);
    }

}

?>