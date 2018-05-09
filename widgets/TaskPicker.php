<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use Yii;
use yii\helpers\Html;
use \yii\helpers\Url;

/**
 * TaskPickerWidget displays a task picker instead of an input field.
 *
 * To use this widget, you may insert the following code in a view:
 * <pre>
 * $this->widget('application.modules_core.task.widgets.TaskPickerWidget',array(
 *     'name'=>'tasks',
 *
 *     // additional javascript options for the date picker plugin
 *     'options'=>array(
 *         'showAnim'=>'fold',
 *     ),
 *     'htmlOptions'=>array(
 *         'style'=>'height:20px;'
 *     ),
 * ));
 * </pre>
 *
 * By configuring the {@link options} property, you may specify the options
 * that need to be passed to the taskpicker plugin. Please refer to
 * the documentation for possible options (name-value pairs).
 *
 * @author davidborn
 */
class TaskPicker extends \yii\base\Widget
{

    /**
     * Id of input element which should replaced
     *
     * @var type
     */
    public $inputId = "";

    /**
     * JSON Search URL - defaults: search/json
     *
     * The token -keywordPlaceholder- will replaced by the current search query.
     *
     * @var String Url with -keywordPlaceholder-
     */
    public $taskSearchUrl = "";

    /**
     * Maximum tasks
     *
     * @var type
     */
    public $maxTasks = 50;

    /**
     * Set guid for the current task
     *
     * @var type int
     */
    public $taskId = null;

    /**
     * Set focus to input or not
     *
     * @var type boolean
     */
    public $focus = false;

    /**
     * @var CModel the data model associated with this widget.
     */
    public $model = null;

    /**
     * @var string the attribute associated with this widget.
     * The name can contain square brackets (e.g. 'name[1]') which is used to collect tabular data input.
     */
    public $attribute = null;

    /**
     * @var string for input placeholder attribute.
     */
    public $placeholderText = "";
    
    /**
     * Used to transfer additional data to the server
     * @var type 
     */
    public $data = null;

    /**
     * Inits the Task Picker
     *
     */
    public function init()
    {
        // Default task search for all tasks
        if ($this->taskSearchUrl == "") {
            // provide the space id if the widget is calling from a space
            if (Yii::$app->controller->id == 'space') {
                $spaceId = Yii::$app->controller->getSpace()->id;
                $this->taskSearchUrl = Url::toRoute(['/tasks/search/json', 'keyword' => '-keywordPlaceholder-', 'space_id' => $spaceId]);
            } else {
                $this->taskSearchUrl = Url::toRoute(['/tasks/search/json', 'keyword' => '-keywordPlaceholder-']);
            }
        }
    }

    /**
     * Displays / Run the Widgets
     */
    public function run()
    {
        // Try to get current field value, when model & attribute attributes are specified.
        $currentValue = "";
        if ($this->model != null && $this->attribute != null) {
            $attribute = $this->attribute;
            $currentValue = $this->model->$attribute;
        }

        return $this->render('taskPicker', [
                    'taskSearchUrl' => $this->taskSearchUrl,
                    'maxTasks' => $this->maxUsers,
                    'currentValue' => $currentValue,
                    'inputId' => $this->inputId,
                    'focus' => $this->focus,
                    'taskId' => $this->taskId,
                    'data' => json_encode($this->data),
                    'placeholderText' => $this->placeholderText,
        ]);
    }
    
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
     * keyword - (string) A keyword which filters task by title and description
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
            'active' => true,
            'maxResult' => 10,
//            'disableFillUser' => true,
            'keyword' => null,
            'permission' => null,
//            'fillQuery' => null,
//            'fillUser' => false
        ];

        $cfg = ($cfg == null) ? $defaultCfg : array_merge($defaultCfg, $cfg);

        //If no initial query is given we use getFriends if friendship module is enabled otherwise all tasks
        if(!isset($cfg['query'])) {
            $cfg['query'] = (Yii::$app->getModule('friendship')->getIsEnabled())
                    ? Yii::$app->task->getIdentity()->getFriends()
                    : UserFilter::find();
        }

        //Filter the initial query and disable task without the given permission
        $task = UserFilter::filter($cfg['query'], $cfg['keyword'], $cfg['maxResult'], null, $cfg['active']);
        $jsonResult = self::asJSON($task, $cfg['permission'], 2);

        //Fill the result with additional tasks if it's allowed and the result count less than maxResult
        if(count($task) < $cfg['maxResult'] && (isset($cfg['fillQuery']) || $cfg['fillUser']) ) {

            //Filter out tasks by means of the fillQuery or default the fillQuery
            $fillQuery = (isset($cfg['fillQuery'])) ? $cfg['fillQuery'] : UserFilter::find();
            UserFilter::addKeywordFilter($fillQuery, $cfg['keyword'], ($cfg['maxResult'] - count($task)));
            $fillQuery->andFilterWhere(['not in', 'id', self::getUserIdArray($task)]);
            $fillUser = $fillQuery->all();

            //Either the additional tasks are disabled (by default) or we disable them by permission
            $disableCondition = (isset($cfg['permission'])) ? $cfg['permission']  : $cfg['disableFillUser'];
            $jsonResult = array_merge($jsonResult, TaskPicker::asJSON($fillUser, $disableCondition, 1));
        }

        return $jsonResult;
    }
    
    /**
     * Assambles all task Ids of the given $tasks into an array
     * 
     * @param array $tasks array of task models
     * @return array task id array
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
     * Creates an json result with task information arrays. A task will be marked
     * as disabled, if the permission check fails on this task.
     * 
     * @param type $tasks
     * @param type $permission
     * @return type
     */
    public static function asJSON($tasks, $permission = null, $priority = null)
    {
        if (is_array($tasks)) {
            $result = [];
            foreach ($tasks as $task) {
                if ($task != null) {
                    $result[] = self::createJSONTaskInfo($task, $permission, $priority);
                }
            }
            return $result;
        } else {
            return self::createJSONTaskInfo($tasks, $permission, $priority);
        }
    }

    /**
     * Creates an single task-information array for a given task. A task will be marked
     * as disabled, if the permission check fails on this task.
     * 
     * @param type $task
     * @param type $permission
     * @return type
     */
    private static function createJSONTaskInfo($task, $permission = null, $priority = null)
    {
        $disabled = false;
        
        if($permission != null && $permission instanceof \humhub\libs\BasePermission) {
            $disabled = !$task->getPermissionManager()->can($permission);
        } else if($permission != null) {
            $disabled = $permission;
        }
        
        $priority = ($priority == null) ? 0 : $priority;
        
        $text = Html::encode($task->title);
        $taskInfo = [];
        $taskInfo['id'] = $task->id;
        $taskInfo['disabled'] = $disabled;
        return $taskInfo;
    }
}

?>
