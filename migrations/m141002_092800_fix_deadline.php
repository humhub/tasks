<?php

use yii\db\Migration;

class m141002_092800_fix_deadline extends Migration
{

    public function up()
    {
        try {
            $this->update('task', array('deadline' => new \yii\db\Expression('NULL')), 'deadline = "" OR deadline = "0000-00-00 00:00:00"');
        } catch (\Exception $ex) {

        }
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
