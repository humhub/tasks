<?php

use yii\db\Migration;

class uninstall extends Migration
{

    public function up()
    {
        try {
            $this->dropFK('fk-task-list-task-id', 'task');
            $this->dropFK('fk-task-item-task-id', 'task_item');
            $this->dropFK('fk-task-reminder-task-id', 'task_reminder');
            $this->dropFK('fk-task-list-setting-task-id', 'task_list_setting');
        } catch(\Exception $e) {}

        $this->dropTableSave('task');
        $this->dropTableSave('task_list_setting');
        $this->dropTableSave('task_user');
        $this->dropTableSave('task_item');
        $this->dropTableSave('task_reminder');
    }

    public function dropFK($name, $table) {
        try {
            $this->dropForeignKey($name, $table);
        } catch(\Exception $e) {
            Yii::warning($e);
        }
    }

    public function dropTableSave($name) {
        try {
            $this->dropTable($name);
        } catch(\Exception $e) {
            Yii::warning($e);
        }
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
