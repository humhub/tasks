<?php
/**
 * Created by PhpStorm.
 * User: buddha
 * Date: 20.04.2018
 * Time: 15:20
 */

namespace humhub\modules\tasks\models\lists;


use humhub\modules\content\components\ActiveQueryContent;

interface TaskListInterface
{

    public function getId();

    /**
     * @return ActiveQueryContent
     */
    public function getTasks();

    /**
     * @return ActiveQueryContent
     */
    public function getNonCompletedTasks();

    /**
     * @param $offset int
     * @param $limit int
     * @return static[]
     */
    public function getShowMoreCompletedTasks($offset, $limit);

    /**
     * @return ActiveQueryContent
     */
    public function getCompletedTasks();

    /**
     * @param $status
     * @return ActiveQueryContent
     */
    public function getTasksByStatus($status);

    public function isHideIfCompleted();

    public function getTitle();
    public function getColor();
    public function getContainer();
}