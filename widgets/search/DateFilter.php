<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets\search;

use humhub\modules\ui\filter\widgets\FilterInput;
use humhub\libs\Html;

class DateFilter extends FilterInput
{
    /**
     * @inheritdoc
     */
    public $view = 'dateFilter';

    /**
     * @inheritdoc
     */
    public $type = 'text';

    public $filterOptions = [];

    /**
     * @var string data-action-click handler of the input event
     */
    public $changeAction = 'inputChange';

    /**
     * @inheritdoc
     */
    public function prepareOptions()
    {
        parent::prepareOptions();

        $this->options['data-action-change'] = $this->changeAction;
        $this->options = array_merge($this->options, $this->filterOptions);
        Html::addCssClass($this->options, 'task-date-picker');
    }
}
