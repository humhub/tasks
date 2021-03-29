<?php

namespace tasks\api;

use tasks\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class ListCest extends HumHubApiTestCest
{
    public function testEmptyList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see empty tasks');
        $I->amAdmin();
        $I->seePaginationTasksResponse('tasks', []);
    }

    public function testFilledList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see sample created tasks');
        $I->amAdmin();
        $I->createTask('First task', 'Sample description for the first task.');
        $I->createTask('Second task', 'Sample description for the second task.');
        $I->createTask('Third task', 'Sample description for the third task.');
        $I->createTask('Fourth task', 'Sample description for the fourth task.');
        $I->seePaginationTasksResponse('tasks', [1, 2, 3, 4]);
    }

    public function testListByContainer(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see tasks by container');
        $I->amAdmin();
        $I->sendGet('tasks/container/123');
        $I->seeNotFoundMessage('Content container not found!');

        $I->createTask('Sample task title 1', 'Sample task content 1', ['containerId' => 1]);
        $I->createTask('Sample task title 2', 'Sample task content 2', ['containerId' => 4]);
        $I->createTask('Sample task title 3', 'Sample task content 3', ['containerId' => 6]);
        $I->createTask('Sample task title 4', 'Sample task content 4', ['containerId' => 4]);
        $I->createTask('Sample task title 5', 'Sample task content 5', ['containerId' => 7]);
        $I->createTask('Sample task title 6', 'Sample task content 6', ['containerId' => 4]);

        $I->seePaginationTasksResponse('tasks/container/1', [1]);
        $I->seePaginationTasksResponse('tasks/container/4', [2, 4, 6]);
        $I->seePaginationTasksResponse('tasks/container/6', [3]);
        $I->seePaginationTasksResponse('tasks/container/7', [5]);
    }

    public function testDeleteByContainer(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('delete tasks by container');
        $I->amAdmin();

        $I->createTask('Sample task title 1', 'Sample task content 1', ['containerId' => 4]);
        $I->createTask('Sample task title 2', 'Sample task content 2', ['containerId' => 4]);
        $I->createTask('Sample task title 3', 'Sample task content 3', ['containerId' => 4]);

        $I->seePaginationTasksResponse('tasks/container/4', [1, 2, 3]);
        $I->sendDelete('tasks/container/4');
        $I->seeSuccessMessage('3 records successfully deleted!');
        $I->seePaginationTasksResponse('tasks/container/4', []);
    }
}
