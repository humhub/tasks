<?php

namespace tasks\api;

use tasks\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;
use yii\web\UploadedFile;

class TaskCest extends HumHubApiTestCest
{
    public function testCreateTask(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('create a task');
        $I->amAdmin();
        $I->createSampleTask();
        $I->seeLastCreatedTaskDefinition();

        $I->amGoingTo('create a task with error');
        $I->sendPost('tasks/container/1', ['Task' => ['scheduling' => 1]]);
        $I->seeBadMessage('Start date cannot be blank');
    }

    public function testGetTaskById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see task by id');
        $I->amAdmin();
        $I->createSampleTask();
        $I->sendGet('tasks/task/1');
        $I->seeTaskDefinitionById(1);
    }

    public function testUpdateTaskById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('update task by id');
        $I->amAdmin();

        $I->sendPut('tasks/task/1');
        $I->seeNotFoundMessage('Task not found!');

        $I->createSampleTask();
        $I->sendPut('tasks/task/1', [
            'Task' => [
                'title' => 'Updated title',
                'description' => 'Updated description',
                'task_list_id' => null,
                'scheduling' => 0,
                'all_day' => 1,
                'selectedReminders' => [],
                'cal_mode' => 1,
                'assignedUsers' => ['01e50e0d-82cd-41fc-8b0c-552392f5839c'],
                'responsibleUsers' => ['01e50e0d-82cd-41fc-8b0c-552392f5839c'],
                'review' => 1,
            ],
            'TaskForm' => [
                'start_date' => '2021-03-30',
                'start_time' => '16:00',
                'end_date' => '2021-03-30',
                'end_time' => '21:00',
                'timeZone' => 'Europe/Helsinki',
                'topics' => '',
                'newItems' => ['1st checkpoint', '2nd  checkpoint'],
            ],
        ]);
        $I->seeTaskDefinitionById(1);
    }

    public function testDeleteTaskById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('delete task by id');
        $I->amAdmin();

        $I->sendDelete('tasks/task/1');
        $I->seeNotFoundMessage('Content record not found!');

        $I->createSampleTask();
        $I->sendDelete('tasks/task/1');
        $I->seeSuccessMessage('Successfully deleted!');
    }

    public function testCompleteRevertTask(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('complete and revert task');
        $I->amAdmin();
        $I->createSampleTask();

        $I->sendPatch('tasks/task/1/processed');
        $I->seeSuccessMessage('Status successfully changed.');

        $I->sendPatch('tasks/task/1/revert');
        $I->seeSuccessMessage('Task successfully reverted.');
    }

    public function testTaskFiles(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('upload/remove files to the task');
        $I->amAdmin();

        $I->createSampleTask();
        $I->sendPost('tasks/task/1/upload-files');
        $I->seeBadMessage('No files to upload.');
        UploadedFile::reset();

        $I->sendPost('tasks/task/1/upload-files', [], [
            'files' => [
                codecept_data_dir('test1.txt'),
                codecept_data_dir('test2.txt'),
            ],
        ]);
        $I->seeSuccessMessage('Files successfully uploaded.');

        $I->amGoingTo('remove a file from the task');
        $I->sendDelete('tasks/task/1/remove-file/2');
        $I->seeSuccessMessage('File successfully removed.');

        $I->sendDelete('tasks/task/1/remove-file/2');
        $I->seeNotFoundMessage('Could not find requested content record or file!');
    }
}
