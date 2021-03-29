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
    
    public function testInstallAndCreatSpaceEntry(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a task list');
        $I->amGoingTo('install the tasks module for space 1');
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
        $I->fillField('#task-description .humhub-ui-richtext', 'This is a test task!');

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

    public function testInstallAndCreatUserEntry(AcceptanceTester $I)
    {
        $I->amUser1();
        $I->wantToTest('the creation of a task list on user profile');
        $I->amGoingTo('install the tasks module on user profile');
        $this->enableProfileModule($I);

        $I->amOnProfile();
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
        $I->fillField('#task-description .humhub-ui-richtext', 'This is a test task!');

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

    private function enableProfileModule(AcceptanceTester $I)
    {
        $I->amOnRoute(Url::toRoute(['/user/account/edit-modules', 'moduleId' => 'tasks']));
        $I->wait(1);
        $I->executeJS('$(\'.media-heading\').each(function() {if($(this).text() === \'Tasks\') {$(this).siblings(\'a:visible\').click()}});');
        $I->wait(2);
    }
}