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
        $subQuery = (new Query())->select('contentcontainer_id')->from('contentcontainer_permission scp')->where(['or', ['scp.class' => ManageTasks::class], ['scp.class' => ProcessUnassignedTasks::class]])
            ->andWhere('cp.contentcontainer_id = scp.contentcontainer_id');

        $permissions = (new Query())->select('cp.*')->from('contentcontainer_permission cp')
            ->where(['cp.class' => CreateTask::class])->andWhere(['NOT EXISTS', $subQuery])->all();

        try {
            foreach ($permissions as $row) {
                $this->insertSilent('contentcontainer_permission', array_merge($row, ['class' => ManageTasks::class, 'permission_id' => ManageTasks::class]));
                $this->insertSilent('contentcontainer_permission', array_merge($row, ['class' => ProcessUnassignedTasks::class, 'permission_id' => ProcessUnassignedTasks::class]));
            }
        } catch(Exception $e) {

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
