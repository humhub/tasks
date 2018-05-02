<?php

use yii\db\Migration;

class uninstall extends Migration
{

    public function up()
    {
        $this->dropForeignKey('fk-task-list-task-id', 'task');
        $this->dropForeignKey('fk-task-item-task-id', 'task_item');
        $this->dropForeignKey('fk-task-reminder-task-id', 'task_reminder');
        $this->dropForeignKey('fk-task-list-setting-task-id', 'task_list_setting');

        $this->dropTable('task');
        $this->dropTable('task_list_setting');
        $this->dropTable('task_user');
        $this->dropTable('task_item');
        $this->dropTable('task_reminder');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
