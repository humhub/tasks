<?php

class m141002_092800_fix_deadline extends EDbMigration
{

    public function up()
    {
        $this->update('task', array('deadline' => new CDbExpression('NULL')), 'deadline = "" OR deadline = "0000-00-00 00:00:00"');
    }

    public function down()
    {
        echo "m141002_092800_fix_deadline does not support migration down.\n";
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
