<?php

use yii\db\Migration;

class m170122_091531_missingdefault extends Migration
{
    public function up()
    {
        $this->alterColumn('task', 'max_users', $this->integer()->null());
    }

    public function down()
    {
        echo "m170122_091531_missingdefault cannot be reverted.\n";

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
