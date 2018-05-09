<?php


namespace humhub\modules\tasks\widgets;


use humhub\components\Widget;

class TaskUserList extends Widget
{
    public $users;

    public $style = '';

    public function run()
    {
        if(empty($this->users)) {
            return '';
        }

        return $this->render('userList', ['users' => $this->users, 'style' => $this->style]);
    }
}