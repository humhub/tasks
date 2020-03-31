<?php

use humhub\components\Migration;
use humhub\modules\tasks\activities\TaskCompletedActivity;
use humhub\modules\tasks\notifications\Assigned;
use humhub\modules\tasks\notifications\AssignedNotification;
use humhub\modules\tasks\notifications\Finished;
use humhub\modules\tasks\notifications\TaskCompletedNotification;

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

class m180411_093623_checklist_and_revision extends Migration
{
    /**
     *
     */
    public function safeUp()
    {
        try {
            $this->dropColumn('task', 'percent');
        } catch(\Throwable $e) {
            Yii::error($e);
        }

        $this->renameColumn('task', 'deadline', 'end_datetime');
        $this->addColumn('task', 'color', 'varchar(7)');
        $this->addColumn('task', 'review', 'tinyint(4) NOT NULL');
        $this->addColumn('task', 'description', 'TEXT NULL');
        $this->addColumn('task', 'scheduling', 'tinyint(4) NOT NULL');
        $this->addColumn('task', 'all_day', 'tinyint(4) NOT NULL');
        $this->addColumn('task', 'start_datetime', 'datetime DEFAULT NULL');
        $this->addColumn('task', 'cal_mode', 'tinyint(4) NOT NULL DEFAULT 0');
        $this->addColumn('task', 'time_zone', 'varchar(60) DEFAULT NULL');
        $this->addColumn('task', 'request_sent', 'tinyint(4) DEFAULT 0');
        $this->addColumn('task', 'task_list_id', 'int(11) DEFAULT NULL');
        $this->addColumn('task', 'sort_order', 'int(11) DEFAULT 0');

        $this->addForeignKey('fk-task-list-task-id', 'task', 'task_list_id', 'content_tag', 'id', 'SET NULL');


        $this->addColumn('task_user', 'user_type', 'tinyint(4) NOT NULL');

        $this->createTable('task_item', [
            'id' => 'pk',
            'task_id' => 'int(11) NOT NULL',
            'title' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT NULL',
            'completed' => 'tinyint(4) DEFAULT 0',
            'sort_order' => 'int(11) DEFAULT 0',
        ], '');

        $this->addForeignKey('fk-task-item-task-id', 'task_item', 'task_id', 'task', 'id', 'CASCADE');

        $this->createTable('task_reminder', [
            'id' => 'pk',
            'task_id' => 'int(11) NOT NULL',
            'remind_mode' => 'tinyint(4) DEFAULT 0',
            'start_reminder_sent' => 'tinyint(4) NOT NULL DEFAULT 0',
            'end_reminder_sent' => 'tinyint(4) NOT NULL DEFAULT 0'
        ], '');

        $this->addForeignKey('fk-task-reminder-task-id', 'task_reminder', 'task_id', 'task', 'id', 'CASCADE');

        $this->createTable('task_list_setting', [
            'id' => 'pk',
            'tag_id' => 'int(11) NOT NULL',
            'hide_if_completed' => 'tinyint(4) DEFAULT 1',
            'sort_order' => 'int(11) DEFAULT 0',
        ], '');

        $this->addForeignKey('fk-task-list-setting-task-id', 'task_list_setting', 'tag_id', 'content_tag', 'id', 'CASCADE');

    }

    public function safeDown()
    {
        echo "m180411_093623_checklist_and_revision cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180411_093623_checklist_and_revision cannot be reverted.\n";

        return false;
    }
    */
}
