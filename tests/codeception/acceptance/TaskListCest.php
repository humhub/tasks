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
use yii\helpers\Url;

class TaskListCest
{
    public function testInstallAndCreateSpaceEntry(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a task list');
        $I->amGoingTo('install the tasks module for space 1');
        $I->enableModule(1, 'tasks');

        $I->amOnSpace1();
        $I->expectTo('see task entry in the space nav');
        $I->waitForText('Tasks', 10, '.layout-nav-container');

        $I->amGoingTo('create a new task list');
        $I->click('Tasks', '.layout-nav-container');
        $I->waitForText('Overview');

        $I->click('button[data-action-click-url="/s/space-1/tasks/task/edit"]'); // "Add" button
        $I->waitForText('New Task', 10, '#globalModal');
        $I->fillField('Task[title]', 'Test task with new list');
        $I->fillContentTagDropDown('task-task_list_id', 'My New Tasklist');
        $I->click('Save', '#globalModal');

        $I->waitForText('My New Tasklist', 10, '.task-list');

        $I->click(Locator::elementAt('[data-task-list-id=1] [data-action-click="ui.modal.load"]', 1));
        $I->waitForText('New Task', 10, '#globalModal');

        $I->fillField('Task[title]', 'My First Task');
        $I->fillField('#task-description .humhub-ui-richtext', 'This is a test task!');

        $I->click('Save', '#globalModal');
        $I->waitForText('My First Task', 10, '.task-list-task-title-bar');

        $I->jsClick('[data-task-id="2"] .toggleTaskDetails');
        $I->waitForText('This is a test task!');
        $I->see('Begin Task');
        $I->click('Begin Task');
        // Check for in progress badge
        $I->waitForElementVisible('.text-bg-accent.badge .fa-pencil', 10, '[data-task-id="2"]');

        $I->waitForText('Finish Task');
        $I->click('Finish Task');

        $I->waitForText('My First Task', 10, '.tasks-completed');
    }

    public function testInstallAndCreatUserEntry(AcceptanceTester $I)
    {
        $I->amUser1();
        $I->wantToTest('the creation of a task list on user profile');
        $I->amGoingTo('install the tasks module on user profile');
        $this->enableProfileModule($I);

        $I->amOnProfile();
        $I->waitForText('Tasks', 10, '.layout-nav-container');

        $I->amGoingTo('create a new task list');
        $I->click('Tasks', '.layout-nav-container');
        $I->waitForText('Overview');

        $I->click('button[data-action-click-url="/u/user1/tasks/task/edit"]'); // "Add" button
        $I->waitForText('New Task', 10, '#globalModal');
        $I->fillField('Task[title]', 'Test task with new list');
        $I->fillContentTagDropDown('task-task_list_id', 'My New Tasklist');
        $I->click('Save', '#globalModal');

        $I->waitForText('My New Tasklist', 10, '.task-list');

        $I->click(Locator::elementAt('[data-task-list-id=1] [data-action-click="ui.modal.load"]', 1));
        $I->waitForText('New Task', 10, '#globalModal');

        $I->fillField('Task[title]', 'My First Task');
        $I->fillField('#task-description .humhub-ui-richtext', 'This is a test task!');

        $I->click('Save', '#globalModal');
        $I->waitForText('My First Task', 10, '.task-list-task-title-bar');

        $I->jsClick('[data-task-id="2"] .toggleTaskDetails');
        $I->waitForText('This is a test task!');
        $I->see('Begin Task');
        $I->click('Begin Task');
        // Check for in progress badge
        $I->waitForElementVisible('.text-bg-accent.badge .fa-pencil', 10, '[data-task-id="2"]');

        $I->waitForText('Finish Task');
        $I->click('Finish Task');

        $I->waitForText('My First Task', 10, '.tasks-completed');
    }

    private function enableProfileModule(AcceptanceTester $I)
    {
        $I->amOnRoute(Url::toRoute(['/user/account/edit-modules', 'moduleId' => 'tasks']));
        $I->wait(1);
        $I->executeJS('$(\'.enable-module-tasks\').click()');
        $I->wait(2);
    }
}
