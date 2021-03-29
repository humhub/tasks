<?php

namespace tasks\api;

use tasks\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class TaskListCest extends HumHubApiTestCest
{
    public function testEmptyList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see empty task list');
        $I->amAdmin();
        $I->seePaginationTasksResponse('tasks/lists/container/1', []);
    }

    public function testFilledTaskList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see sample created task lists');
        $I->amAdmin();
        $I->createTaskList('First task list');
        $I->createTaskList('Second task list');
        $I->createTaskList('Third task list');
        $I->createTaskList('Fourth task list');
        $I->seePaginationTaskListsResponse('tasks/lists/container/1', [1, 2, 3, 4]);
    }

    public function testCreateTaskList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('create a task list');
        $I->amAdmin();
        $I->createSampleTaskList();
        $I->seeLastCreatedTaskListDefinition();
    }

    public function testGetTaskListById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see task list by id');
        $I->amAdmin();
        $I->createSampleTaskList();
        $I->sendGet('tasks/list/1');
        $I->seeTaskListDefinitionById(1);
    }

    public function testUpdateTaskById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('update task list by id');
        $I->amAdmin();

        $I->sendPut('tasks/task/1');
        $I->seeNotFoundMessage('Task not found!');

        $I->createSampleTaskList();
        $I->sendPut('tasks/list/1', [
            'TaskList' => [
                'name' => 'Updated task list title',
                'color' => '#7b9102',
            ],
            'TaskListSettings' => [
                'hide_if_completed' => 1,
            ],
        ]);
        $I->seeTaskListDefinitionById(1);
    }

    public function testDeleteTaskListById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('delete task list by id');
        $I->amAdmin();

        $I->sendDelete('tasks/list/1');
        $I->seeNotFoundMessage('Task list not found!');

        $I->createSampleTaskList();
        $I->sendDelete('tasks/list/1');
        $I->seeSuccessMessage('Task list successfully deleted!');
    }
}
