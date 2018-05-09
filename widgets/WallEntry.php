<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use humhub\modules\content\widgets\WallEntryControlLink;
use humhub\modules\tasks\assets\Assets;
use humhub\modules\content\widgets\EditLink;
use humhub\modules\content\widgets\DeleteLink;
use humhub\modules\tasks\helpers\TaskUrl;
use Yii;

/**
 * @inheritdoc
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{
    public $editMode = self::EDIT_MODE_MODAL;

    public function getEditUrl()
    {
        return TaskUrl::editTask($this->contentObject, 0, 1);
    }

    public function isInModal()
    {
        return Yii::$app->request->get('cal');
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        Assets::register($this->view);
        return $this->render('wallEntry', ['task' => $this->contentObject, 'justEdited' => $this->justEdited]);
    }

    public function getContextMenu()
    {
        if(!$this->isInModal() || !$this->contentObject->content->canEdit()) {
            return parent::getContextMenu();
        }

        // TODO: remove this after simplestream modal edit/delete runs as expected
        $this->controlsOptions['prevent'] = [EditLink::class , DeleteLink::class];
        $result = parent::getContextMenu();

        return $result;
    }

    public function getWallEntryViewParams()
    {
        $params = parent::getWallEntryViewParams();
        if($this->isInModal()) {
            $params['showContentContainer'] = true;
        }
        return $params;
    }

}
