<?php

use yii\db\Migration;

class m180515_120940_list_settings_updated_at extends Migration
{
    public function safeUp()
    {
        $this->addColumn('task_list_setting', 'updated_at', 'datetime default NULL');
    }

    public function safeDown()
    {
        echo "m180515_120940_list_settings_updated_at cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180515_120940_list_settings_updated_at cannot be reverted.\n";

        return false;
    }
    */
}
