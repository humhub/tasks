<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Fix missing module attributes on some updated humhub installations
 */
class m151224_162440_fix_module_id_notifications extends Migration
{
    public function up()
    {
        // The column was removed with the notification API of the core 1.20
        if ($this->db->getTableSchema('notification', true)->getColumn('module') === null) {
            return;
        }

        $this->update('notification', ['module' => 'tasks'], ['class' => 'humhub\modules\tasks\notifications\Assigned']);
        $this->update('notification', ['module' => 'tasks'], ['class' => 'humhub\modules\tasks\notifications\Finished']);
    }

    public function down()
    {
        echo "m151224_162440_fix_module_id_notifications cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
