<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace tasks\acceptance;

use Codeception\Util\Locator;
use tasks\AcceptanceTester;

class TaskListCest
{
    
    public function testInstallAndCreatEntry(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a task list');
        $I->amGoingTo('install the calendar module for space 1');
        $I->enableModule(1, 'tasks');

        $I->amOnSpace1();
        $I->expectTo('see task entry in the space nav');
        $I->waitForText('Tasks', null, '.layout-nav-container');

        $I->amGoingTo('create a new task list');
        $I->click('Tasks', '.layout-nav-container');
        $I->waitForText('Task Lists');

        $I->click('Add Task List');
        $I->waitForText('Create task list', null, '#globalModal');
        $I->fillField('#tasklist-name', 'My New Tasklist');
        $I->click('Save', '#globalModal');

        $I->waitForText('My New Tasklist', null, '.task-list');

        $I->click(Locator::elementAt('[data-action-click="task.list.editTask"]',1));
        $I->waitForText('Create new task', null, '#globalModal');

        $I->fillField('Task[title]', 'My First Task');
        $I->fillField('Task[description]', 'This is a test task!');
        //$I->click('[for="task-scheduling"]', '#globalModal');
        //$I->click('Scheduling', '#globalModal');

        $I->click('Save', '#globalModal');
        $I->waitForText('My First Task', null,'.task-list-task-title-bar');

        $I->click('[data-task-id="1"]');
        $I->waitForText('This is a test task!');
        $I->see('Begin Task');
        $I->click('Begin Task');
        // Check for in progress badge
        $I->waitForElementVisible('.label-info .fa-edit', null,'[data-task-id="1"]');

        $I->click('[data-task-id="1"]');
        $I->waitForText('Finish Task');
        $I->click('Finish Task');

        $I->waitForText('My First Task', null,'.tasks-completed');
    }
}