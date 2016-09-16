<?php

use yii\db\Migration;

class m160911_155343_defaultValue extends Migration
{

    public function up()
    {
        $this->alterColumn('task', 'max_users', $this->integer()->defaultValue(5));
    }

    public function down()
    {
        echo "m160911_155343_defaultValue cannot be reverted.\n";

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
