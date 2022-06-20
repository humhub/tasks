<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace tasks;

use Facebook\WebDriver\WebDriverKeys;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \AcceptanceTester
{
    use _generated\AcceptanceTesterActions;

    public function fillContentTagDropDown($field, $value)
    {
        $this->seeElement('.field-' . $field);
        $this->click('.field-' . $field . ' .select2');
        $this->waitForElement('input.select2-search__field');
        $select2Input = 'input.select2-search__field[aria-controls=select2-' . $field . '-results]';
        $this->fillField($select2Input, $value);
        $this->pressKey($select2Input, WebDriverKeys::ENTER);
    }
}
