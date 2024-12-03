<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\widgets;

use humhub\libs\Html;
use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use humhub\modules\ui\menu\MenuLink;
use humhub\widgets\Link;
use Yii;

/**
 * Widget for rendering the menu buttons for a Task.
 * @author davidborn
 */
class TaskContextMenu extends WallEntryControls
{
    /**
     * @inheritdoc
     */
    public $template = 'taskContextMenu';

    public ?Task $task = null;

    public string $mode = 'details';

    public ?string $align = null;

    /**
     * @inheritdoc
     */
    public function beforeRun()
    {
        return parent::beforeRun() && $this->task->content->canEdit();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->object = $this->task;
        $this->wallEntryWidget = new WallEntry(['model' => $this->task]);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        $attrs = parent::getAttributes();
        Html::addCssClass($attrs, 'task-preferences');
        return $attrs;
    }

    /**
     * @inheritdoc
     */
    protected function getViewParams()
    {
        $params = parent::getViewParams();
        $params['toggler'] = $this->getToggler();
        $params['task'] = $this->task;
        return $params;
    }

    public function initControls()
    {
        $this->renderOptions->disableControlsEntryEdit();
        $this->renderOptions->disableControlsEntryDelete();
        $this->renderOptions->disableControlsEntryPin();

        if ($this->task->content->canEdit()) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('TasksModule.base', 'Edit'),
                'url' => '#',
                'icon' => 'pencil',
                'sortOrder' => 100,
                'htmlOptions' => [
                    'data-action-click' => 'ui.modal.post',
                    'data-action-click-url' => TaskUrl::editTask($this->task),
                ],
            ]));

            $this->addEntry(new MenuLink([
                'label' => Yii::t('TasksModule.base', 'Delete'),
                'url' => '#',
                'icon' => 'trash',
                'sortOrder' => 300,
                'htmlOptions' => [
                    'data-action-click' => 'task.deleteTaskFromContext',
                    'data-action-click-url' => TaskUrl::deleteTask($this->task),
                    'data-action-confirm-header' => Yii::t('TasksModule.base', '<strong>Confirm</strong> task deletion'),
                    'data-action-confirm' => Yii::t('TasksModule.base', 'Do you really want to delete this task?'),
                    'data-action-confirm-text' => Yii::t('TasksModule.base', 'Delete'),
                ],
            ]));
        }

        parent::initControls();
    }

    private function getToggler(): Link
    {
        if ($this->mode === 'details') {
            return Link::asLink('<span class="caret"></span>')->icon('cog');
        }

        return Link::asLink()->icon('ellipsis-v');
    }
}
