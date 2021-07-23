<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use humhub\modules\content\widgets\PermaLink;
use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;
use humhub\modules\tasks\assets\Assets;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\Task;
use Yii;

/**
 * @inheritdoc
 */
class WallEntry extends WallStreamModuleEntryWidget
{
    public $editMode = self::EDIT_MODE_MODAL;

    /**
     * @var Task
     */
    public $model;

    public function getEditUrl()
    {
        return TaskUrl::editTask($this->model, 0, 1);
    }

    public function isInModal()
    {
        return Yii::$app->request->get('cal');
    }

    /*public function getContextMenu()
    {
        if(!$this->isInModal() || !$this->contentObject->content->canEdit()) {
            return parent::getContextMenu();
        }

        // TODO: remove this after simplestream modal edit/delete runs as expected
        $this->controlsOptions['prevent'] = [EditLink::class , DeleteLink::class];
        $result = parent::getContextMenu();

        return $result;
    }*/

    /**
     * @return string returns the content type specific part of this wall entry (e.g. post content)
     */
    protected function renderContent()
    {
        Assets::register($this->view);
        return $this->render('wallEntry', ['task' => $this->model, 'justEdited' => $this->renderOptions->justEdited]);
    }

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return $this->model->title;
    }

    /**
     * @inheritdoc
     */
    protected function getPermaLink()
    {
        if (!$this->model->canView()) {
            return null;
        }

        return parent::getPermaLink();
    }

    /**
     * @inheritdoc
     */
    public function getControlsMenuEntries()
    {
        $controlsMenuEntries = parent::getControlsMenuEntries();

        if (!$this->model->canView()) {
            foreach ($controlsMenuEntries as $c => $controlsMenuEntry) {
                if (is_array($controlsMenuEntry) && $controlsMenuEntry[0] === PermaLink::class) {
                    unset($controlsMenuEntries[$c]);
                    break;
                }
            }
        }

        return $controlsMenuEntries;
    }
}
