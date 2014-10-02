<?php

class m141002_084750_fix_default extends EDbMigration
{

    public function up()
    {
        $this->alterColumn('task', 'percent', "smallint(6) NOT NULL DEFAULT 0");
    }

    public function down()
    {
        echo "m141002_084750_fix_default does not support migration down.\n";
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
