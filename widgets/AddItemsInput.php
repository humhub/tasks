<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use humhub\components\Widget;

/**
 * TaskItemWidget is used to display a task item.
 *
 * This Widget will used by the Task Model in Method getWallOut().
 *
 * @author davidborn
 */
class AddItemsInput extends Widget
{
    
    public $name;

    public function run()
    {
        return $this->render('addItemsInput', ['name' => $this->name]);
    }

}

?>