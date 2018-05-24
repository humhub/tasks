<?php


namespace humhub\modules\tasks\widgets;


use humhub\components\Widget;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\user\TaskUser;
use Yii;

class TaskUserList extends Widget
{
    public $users;

    public $style = '';

    public $type = Task::USER_ASSIGNED;

    public function run()
    {
        if(empty($this->users)) {
            return '';
        }

        return $this->render('userList', ['users' => $this->users, 'style' => $this->style, 'type' => $this->type]);
    }
}