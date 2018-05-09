<?php

use humhub\components\Migration;
use humhub\modules\tasks\permissions\ProcessUnassignedTasks;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use yii\db\Query;

class m180504_092302_permission_name_and_list_updated_at extends Migration
{
    public function safeUp()
    {
        $permissions = (new Query())->select(['contentcontainer_id', 'group_id'])->from('contentcontainer_permission')->where(['class' => CreateTask::class])->all();

        foreach ($permissions as $row) {
            $this->insertSilent('contentcontainer_permission', array_merge($row, ['class' => ManageTasks::class, 'permission_id' => ManageTasks::class]));
            $this->insertSilent('contentcontainer_permission', array_merge($row, ['class' => ProcessUnassignedTasks::class, 'permission_id' => ProcessUnassignedTasks::class]));
        }
    }

    public function safeDown()
    {
        echo "m180504_092302_permission_name_and_list_updated_at cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_092302_permission_name_and_list_updated_at cannot be reverted.\n";

        return false;
    }
    */
}
