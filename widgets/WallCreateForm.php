<?php

namespace module\tasks\widgets;

class WallCreateForm extends \humhub\modules\content\widgets\WallCreateContentForm
{

    public $submitUrl = '/tasks/task/create';

    public function renderForm()
    {
        return $this->render('taskForm', array('contentContainer' => $this->contentContainer));
    }

}

?>