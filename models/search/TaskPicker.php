<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models\search;

use Yii;
use yii\helpers\Html;

/**
 * This class can be used to filter results for a task picker search query by calling the static
 * filter method.
 *
 * @since 1.2
 * @author buddha
 */
class TaskPicker
{
    
    /**
     * Creates a json task array used in the taskpicker js frontend.
     * The $cfg is used to specify the filter values the following values are available:
     * 
     * query - (ActiveQuery) The initial query which is used to append additional filters. - default = User Friends if friendship module is enabled else User::find()
     * 
     * active - (boolean) Specifies if only active task should be included in the result - default = true
     * 
     * maxResults - (int) The max number of entries returned in the array - default = 10
     * 
     * keyword - (string) A keyword which filters task by taskname, firstname, lastname, email and title
     * 
     * permission - (BasePermission) An additional permission filter
     * 
     * fillQuery - (ActiveQuery) Can be used to fill the result array if the initial query does not return the maxResults, these results will have a lower priority
     * 
     * fillUser - (boolean) When set to true and no fillQuery is given the result is filled with User::find() results
     * 
     * disableFillUser - Specifies if the results of the fillQuery should be disabled in the taskpicker results - default = true
     * 
     * @param type $cfg filter configuration
     * @return type json representation used by the taskpicker
     */
    public static function filter($cfg = null)
    {
        $defaultCfg = [
//            'active' => true,
            'maxResult' => 10,
//            'disableFillUser' => true,
            'keyword' => null,
            'permission' => null,
            'fillQuery' => null,
            'disabledText' => null,
//            'fillUser' => false,
            'filter' => null
        ];
        
        $cfg = ($cfg == null) ? $defaultCfg : array_merge($defaultCfg, $cfg);
        
        //If no initial query is given we use getFriends if friendship module is enabled otherwise all tasks
        if(!isset($cfg['query'])) {
            $cfg['query'] = TaskFilter::find();
        }
        
        //Filter the initial query and disable task without the given permission
//        $task = TaskFilter::filter($cfg['query'], $cfg['keyword'], $cfg['maxResult'], null, $cfg['active']);
        $task = TaskFilter::filter($cfg['query'], $cfg['keyword'], $cfg['maxResult'], null);
        $jsonResult = self::asJSON($task, $cfg['permission'], 2, $cfg['disabledText']);
        
//        //Fill the result with additional users if it's allowed and the result count less than maxResult
//        if(count($task) < $cfg['maxResult'] && (isset($cfg['fillQuery']) || $cfg['fillUser']) ) {
//
//            //Filter out users by means of the fillQuery or default the fillQuery
//            $fillQuery = (isset($cfg['fillQuery'])) ? $cfg['fillQuery'] : UserFilter::find();
//            UserFilter::addKeywordFilter($fillQuery, $cfg['keyword'], ($cfg['maxResult'] - count($user)));
//            $fillQuery->andFilterWhere(['not in', 'id', self::getUserIdArray($user)]);
//            $fillUser = $fillQuery->all();
//
//            //Either the additional users are disabled (by default) or we disable them by permission
//            $disableCondition = (isset($cfg['permission'])) ? $cfg['permission']  : $cfg['disableFillUser'];
//            $jsonResult = array_merge($jsonResult, self::asJSON($fillUser, $disableCondition, 1, $cfg['disabledText']));
//        }
        
        if($cfg['filter'] != null) {
            array_walk($jsonResult, $cfg['filter']);
        }
        
        return $jsonResult;
    }
    
    /**
     * Assambles all user Ids of the given $users into an array
     * 
     * @param array $users array of user models
     * @return array user id array
     */
    private static function getTaskIdArray($tasks)
    {
        $result = [];
        foreach($tasks as $task) {
            $result[] = $task->id;
        }
        return $result;
    }
        
    /**
     * Creates an json result with user information arrays. A user will be marked
     * as disabled, if the permission check fails on this user.
     * 
     * @param type $tasks
     * @param type $permission
     * @return type
     */
    public static function asJSON($tasks, $permission = null, $priority = null, $disabledText = null)
    {
        if (is_array($tasks)) {
            $result = [];
            foreach ($tasks as $task) {
                if ($task != null) {
                    $result[] = self::createJSONTaskInfo($task, $permission, $priority, $disabledText);
                }
            }
            return $result;
        } else {
            return self::createJSONTaskInfo($tasks, $permission, $priority, $disabledText);
        }
    }

    /**
     * Creates a single user-information array for the given $user. A user will be marked
     * as disabled, if the given $permission check fails on this user. If the second argument
     * is of type boolean, the it will define the disabled field of the result directly.
     * 
     * @param type $user
     * @param \humhub\libs\BasePermission|boolean|null if boolean is given
     * @return type
     */
    private static function createJSONTaskInfo($task, $permission = null, $priority = null, $disabledText = null)
    {
        $disabled = false;
        
        if($permission != null && $permission instanceof \humhub\libs\BasePermission) {
            $disabled = !$task->getPermissionManager()->can($permission);
        } else if($permission != null) {
            $disabled = $permission;
        }

        return [
            'id' => $task->id,
            'disabled' => $disabled,
            'disabledText' => ($disabled) ? $disabledText : null,
            'text' => Html::encode($task->title),
//            'image' => $task->getProfileImage()->getUrl(),
            'priority' => ($priority == null) ? 0 : $priority,
            'link' => $task->getUrl()
        ];
    }
}