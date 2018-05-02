<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: davidborn
 */

namespace humhub\modules\tasks\models\forms;


use humhub\modules\tasks\models\Sortable;
use humhub\modules\tasks\models\Task;
use yii\base\Model;

class ItemDrop extends Model
{
    /**
     * @var Sortable
     */
    public $model;

    /**
     * @var integer
     */
    public $modelId;

    /**
     * @var Task
     */
    public $modelClass;

    /**
     * @var integer
     */
    public $index;

    /**
     * @var integer
     */
    public $itemId;


    public function getSortableModel()
    {
        if(!$this->model) {
            $this->model = call_user_func("$this->modelClass::findOne", ['id' => $this->modelId]);
        }

        return $this->model;
    }

    public function save()
    {
        $this->getSortableModel()->moveItemIndex($this->itemId, $this->index);
        return true;
    }

    public function rules()
    {
        return [
            [['modelId', 'itemId', 'index'], 'integer']
        ];
    }

    public function formName()
    {
        return 'ItemDrop';
    }

}