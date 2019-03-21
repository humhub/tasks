<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use humhub\components\Widget;
use humhub\modules\tasks\models\Task;
use humhub\modules\tasks\models\SnippetModuleSettings;
use humhub\modules\content\components\ContentContainerActiveRecord;

/**
 * MyTasks shows users tasks in sidebar.
 *
 * @author davidborn
 */
class MyTasks extends Widget
{

    /**
     * ContentContainer to limit tasks to. (Optional)
     *
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * How many tasks should be shown?
     *
     * @var int
     */
    public $limit = 5;

    public function run()
    {
        $settings = SnippetModuleSettings::instantiate();
        $taskEntries = Task::findUserTasks(null,  $this->contentContainer, $settings->myTasksSnippetMaxItems);

        if (empty($taskEntries)) {
            return;
        }

        return $this->render('myTasks', ['taskEntries' => $taskEntries]);
    }

}
