<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\widgets;

use humhub\libs\Html;

class ContentTagDropDown extends \humhub\modules\content\widgets\ContentTagDropDown
{
    public function run()
    {
        $items = $this->getItems();

        $options = $this->getOptions();
        unset($options['id']);

        if ($this->form && $this->hasModel()) {
            return $this->form->field($this->model, $this->attribute)->dropDownList($items, $options);
        } elseif ($this->hasModel()) {
            return Html::activeDropDownList($this->model, $this->attribute, $items, $options);
        } else {
            return Html::dropDownList($this->name, $this->value, $items, $options);
        }
    }
}