<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models\search;

use humhub\modules\tasks\models\Task;

/**
 * Special user model class for the purpose of searching users.
 *
 * @author Julian Harrer
 */
class TaskFilter extends Task
{

    /**
     * Default implementation for user picker filter.
     * 
     * @param type $keywords
     * @param type $maxResults
     * @param type $friendsOnly
     * @param type $permission
     * @deprecated since 1.2 use 
     * @return type
     */
    public function getTaskPickerResult($keywords = null, $maxResults = null, $permission = null)
    {
            //We don't use the permission here for filtering since we include user with no permission as disabled in the result.
            //The problem here is we do not prefer users with permission in the query.
            $tasks = $this->getTaskByFilter($keywords, $maxResults);
            return TaskPicker::asJSON($tasks, $permission);
    }
    
//    private function containsUser($userArr, $user)
//    {
//        foreach($userArr as $currentUser) {
//            if($currentUser->id === $user->id) {
//                return true;
//            }
//        }
//        return false;
//    }

    /**
     * Searches for all active users by the given keyword and permission.
     * 
     * @param type $keywords
     * @param type $maxResults
     * @param type $permission
     * @return type
     */
    public static function getTaskByFilter($keywords = null, $maxResults = null, $permission = null)
    {
        return static::filter(Task::find(), $keywords, $maxResults, $permission);
    }

    
    /**
     * Returns an array of user models filtered by a $keyword and $permission. These filters
     * are added to the provided $query. The $keyword filter can be used to filter the users
     * by email, username, firstname, lastname and title. By default this functions does not
     * consider inactive user.
     * 
     * @param type $query
     * @param type $keywords
     * @param type $maxResults
     * @param type $permission
//     * @param type $active
     * @return type
     */
    public static function filter($query, $keywords = null, $maxResults = null, $permission = null)
    {
        $task = static::addQueryFilter($query, $keywords, $maxResults)->all();
        return static::filterByPermission($task, $permission);
    }

    public static function addQueryFilter($query, $keywords = null, $maxResults = null)
    {
        
        if ($maxResults != null) {
            $query->limit($maxResults);
        }
        
        return $query;
    }


    /**
     * Returns a subset of the given array containing all users of the given set
     * which are permitted. If the permission is null this method returns the
     * 
     * @param type $users
     * @param type $permission
     * @return type
     */
    public static function filterByPermission($tasks, $permission)
    {
        if ($permission === null) {
            return $tasks;
        }

        $result = [];

        foreach ($tasks as $task) {
            if ($task->getPermissionManager()->can($permission)) {
                $result[] = $task;
            }
        }

        return $result;
    }
}
