<?php


namespace tasks;


use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\notification\models\Notification;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\lists\TaskList;
use tests\codeception\_support\HumHubDbTestCase;
use yii\db\ActiveRecord;

class TaskTestCase extends HumHubDbTestCase
{
    /**
     * @param ContentContainerActiveRecord $contentContainer
     * @param $title
     * @param array $config
     * @return TaskList
     */
    public function createTaskList(ContentContainerActiveRecord $contentContainer, $title, $config = [])
    {
        $taskList = new TaskList($contentContainer, $title, $config);
        $this->assertTrue($taskList->save());
        return $taskList;
    }

    public function createTask(ContentContainerActiveRecord $contentContainer, $title, $taskList = null, $config = [])
    {
        $config['title'] = $title;
        if ($taskList) {
            $config['task_list_id'] = $taskList->id;
        }
        $task = new Task($contentContainer, Content::VISIBILITY_PUBLIC, $config);
        $this->assertTrue($task->save());
        $task->refresh();
        return $task;
    }

    public function assertHasNoNotification($class, ActiveRecord $source, $originator_id = null, $target_id = null, $msg = '')
    {
        $notificationQuery = Notification::find()->where(['class' => $class, 'source_class' => $source->className(), 'source_pk' => $source->getPrimaryKey()]);

        if ($originator_id != null) {
            $notificationQuery->andWhere(['originator_user_id' => $originator_id]);
        }

        if($target_id != null) {
            $notificationQuery->andWhere(['user_id' => $target_id]);
        }

        $this->assertEmpty($notificationQuery->all(), $msg);
    }

    public function assertEqualsNotificationCount($count, $class, ActiveRecord $source, $originator_id = null, $target_id = null, $msg = '')
    {
        $notificationQuery = Notification::find()->where(['class' => $class, 'source_class' => $source->className(), 'source_pk' => $source->getPrimaryKey()]);

        if ($originator_id != null) {
            $notificationQuery->andWhere(['originator_user_id' => $originator_id]);
        }

        if($target_id != null) {
            $notificationQuery->andWhere(['user_id' => $target_id]);
        }

        $this->assertEquals($count, $notificationQuery->count(), $msg);
    }
}