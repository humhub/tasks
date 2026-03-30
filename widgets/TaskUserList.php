<?php

namespace humhub\modules\tasks\widgets;

use humhub\components\Widget;
use humhub\modules\tasks\models\Task;

class TaskUserList extends Widget
{
    public $users;

    public $style = '';

    public $type = Task::USER_ASSIGNED;

    public $size = 24;

    public function run()
    {
        if (empty($this->users)) {
            return '';
        }

        return $this->render('userList', [
            'users' => $this->users,
            'style' => $this->style,
            'type' => $this->type,
            'size' => $this->size,
        ]);
    }
}
