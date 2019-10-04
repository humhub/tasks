<?php

namespace humhub\modules\tasks\controllers;

use humhub\components\export\DateTimeColumn;
use humhub\components\export\SpreadsheetExport;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\permissions\CreateTask;
use humhub\modules\tasks\permissions\ManageTasks;
use humhub\modules\tasks\traits\DataExport;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;

abstract class AbstractTaskController extends ContentContainerController
{
    use DataExport;

    protected function getTaskById($id)
    {
        $task = Task::find()->contentContainer($this->contentContainer)->readable()->where(['task.id' => $id])->one();
        if ($task === null) {
            throw new HttpException(404, "Could not load task!");
        }
        return $task;
    }

    protected function canCreateTask()
    {
        return $this->contentContainer->can(CreateTask::class);
    }

    protected function canManageTasks()
    {
        return $this->contentContainer->can(ManageTasks::class);
    }

    /**
     * Export user list as csv or xlsx
     * @param string $format supported format by phpspreadsheet
     * @return \yii\web\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\base\Exception
     */
    public function actionExport($format)
    {
        $filterModel = new TaskFilter(['contentContainer' => $this->contentContainer]);

        $filterModel->load(Yii::$app->request->get());
        $query = $filterModel->query();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $exporter = new SpreadsheetExport([
            'dataProvider' => $dataProvider,
            'columns' => $this->collectExportColumns(clone $query),
            'resultConfig' => [
                'fileBaseName' => 'humhub_tasks',
                'writerType' => $format,
            ],
        ]);

        return $exporter->export()->send();
    }

    /**
     * Return array with columns for data export
     * @param $query
     * @return array
     */
    private function collectExportColumns($query)
    {
        $relationsData = $this->prepareData('id', $query->with(['items', 'users.profile'])->asArray()->all());
        return [
            'id',
            'title',
            'description',
            [
                'attribute' => 'Container',
                'value' => $this->getRelatedContainer()
            ],
            [
                'attribute' => 'ContainerType',
                'value' =>  function ($model) {
                    /* @var $model \humhub\modules\tasks\models\Task */
                    return (new \ReflectionClass($model->content->container))->getShortName();
                }
            ],
            [
                'attribute' => 'ContainerId',
                'value' =>  function ($model) {
                    return $model->content->container->id;
                }
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'created_at',
            ],
            'created_by',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'updated_at',
            ],
            'updated_by',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'start_datetime',
                'label' => 'Start Date Time',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'end_datetime',
                'label' => 'End Date Time',
            ],
            'max_users',
            'status',
            [
                'attribute' => 'Checklist',
                'value' => $this->getTaskItems($relationsData)
            ],
            [
                'attribute' => 'Assigned Users',
                'value' => $this->getAssignedUsers($relationsData)
            ],
            [
                'attribute' => 'Responsible Users',
                'value' => $this->getResponsibleUsers($relationsData)
            ],
            [
                'attribute' => 'Comments',
                'value' => $this->getComments()
            ],
            'color',
            [
                'attribute' => 'review',
                'label' => 'Review required'
            ],
            'scheduling',
            'all_day',
            'cal_mode',
            'time_zone',
            'request_sent',
            [
                'attribute' => 'task_list_id',
                'label' => 'Task List'
            ],
        ];
    }
}