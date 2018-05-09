<?php

use humhub\components\Migration;

use humhub\modules\tasks\activities\TaskCompletedActivity;
use humhub\modules\tasks\notifications\AssignedNotification;
use humhub\modules\tasks\notifications\TaskCompletedNotification;



class m180502_142535_renameclasses extends Migration
{
    public function up()
    {

        $this->renameClass('humhub\modules\tasks\notifications\Assigned', AssignedNotification::class);
        $this->renameClass('humhub\modules\tasks\notifications\Finished', TaskCompletedNotification::class);
        $this->renameClass('humhub\modules\tasks\activities\Finished', TaskCompletedActivity::class);

        $this->updateSilent('notification', ['class' => AssignedNotification::class], ['class' => 'humhub\modules\tasks\notifications\Assigned']);
        $this->updateSilent('notification', ['class' => TaskCompletedNotification::class], ['class' => 'humhub\modules\tasks\notifications\Finished']);
        $this->updateSilent('notification', ['class' => TaskCompletedActivity::class], ['class' => 'humhub\modules\tasks\activities\Finished']);


    }

    public function safeDown()
    {
        echo "m180502_142535_renameclasses cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180502_142535_renameclasses cannot be reverted.\n";

        return false;
    }
    */
}
