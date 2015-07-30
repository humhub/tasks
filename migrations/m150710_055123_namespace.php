<?php

use yii\db\Schema;
use humhub\components\Migration;

class m150710_055123_namespace extends Migration
{

    public function up()
    {
        $this->renameClass('Task', humhub\modules\tasks\models\Task::className());

        $this->update('activity', ['class' => 'humhub\modules\content\activities\ContentCreated', 'module' => 'content'], ['class' => 'TaskCreated']);
        $this->update('activity', ['class' => 'humhub\modules\tasks\activities\Finished', 'module' => 'tasks'], ['class' => 'TaskFinished']);

        $this->update('notification', ['class' => 'humhub\modules\tasks\notification\Assigned'], ['class' => 'TaskAssignedNotification']);
        $this->update('notification', ['class' => 'humhub\modules\tasks\notification\Finished'], ['class' => 'TaskFinishedNotification']);

    }

    public function down()
    {
        echo "m150710_055123_namespace cannot be reverted.\n";

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
