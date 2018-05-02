<?php
/**
 * Created by PhpStorm.
 * User: buddha
 * Date: 20.04.2018
 * Time: 15:40
 */

namespace humhub\modules\tasks\models;


interface Sortable
{
    public function moveItemIndex($id, $newIndex);
}