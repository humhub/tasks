<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\models\forms;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerSettingsManager;
use humhub\modules\tasks\Module;
use Yii;
use yii\base\Model;

class ConfigureContainerForm extends Model
{
    public ContentContainerActiveRecord $contentContainer;
    public bool $contentHiddenDefault;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->contentHiddenDefault = (bool) $this->getSettings()->get('contentHiddenDefault',
            $this->getModule()->getContentHiddenGlobalDefault());
    }

    public function getModule(): Module
    {
        return Yii::$app->getModule('tasks');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contentHiddenDefault'], 'boolean'],
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->getSettings()->set('contentHiddenDefault', $this->contentHiddenDefault);

        return true;
    }

    private function getSettings(): ContentContainerSettingsManager
    {
        return $this->getModule()->settings->contentContainer($this->contentContainer);
    }

}
