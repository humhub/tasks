<?php

use yii\db\Migration;

class m140812_093445_rename_deadline_column extends Migration
{

    public function up()
    {
        $this->renameColumn('task', 'deathline', 'deadline');
    }

    public function down()
    {
        echo "m140812_093445_rename_deadline_column does not support migration down.\n";
        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
