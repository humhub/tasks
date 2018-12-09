<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 05.10.2018
 * Time: 21:14
 */

namespace humhub\modules\tasks\widgets\search;

use humhub\modules\ui\filter\widgets\FilterInput;
use humhub\libs\Html;

class TextFilterInput extends FilterInput
{
    /**
     * @inheritdoc
     */
    public $view = 'textInput';

    /**
     * @inheritdoc
     */
    public $type = 'textInput';

    /**
     * @var string data-action-click handler of the input event
     */
    public $changeAction = 'parent.inputChange';

    /**
     * @inheritdoc
     */
    public function prepareOptions()
    {
        parent::prepareOptions();

        $this->options['data-action-change'] = $this->changeAction;
        Html::addCssClass($this->options, 'form-control');
    }
}
