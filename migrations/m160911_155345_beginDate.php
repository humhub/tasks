<?php

use yii\db\Query;
use humhub\components\Migration;

class m160911_155345_beginDate extends Migration
{

    public function up()
    {
        $this->addColumn('task', 'duration_days', $this->smallInteger()->defaultValue(1));
        $this->addColumn('task', 'start_date', $this->date());
        $this->alterColumn('task', 'deadline', $this->date());

        $this->dropColumn('task', 'created_at');
        $this->dropColumn('task', 'created_by');
        $this->dropColumn('task', 'updated_at');
        $this->dropColumn('task', 'updated_by');

        $rows = (new Query())
                ->select("*")
                ->from('task')
                ->all();
        foreach ($rows as $row) {
            if ($row['duration_days'] < 1) {
                $row['duration_days'] = 1;
            }

            $date = new \DateTime($row['deadline']);
            $date->sub(new \DateInterval('P' . (int) ($row['duration_days'] - 1) . 'D'));
            $this->updateSilent('task', ['start_date' => $date->format('Y-m-d')], ['id' => $row['id']]);
        }
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
