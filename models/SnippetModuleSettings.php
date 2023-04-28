<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models;

use humhub\components\SettingsManager;
use humhub\modules\tasks\Module;
use Yii;
use \yii\base\Model;

class SnippetModuleSettings extends Model
{
    /**
     * @var boolean determines if the dashboard widget should be shown or not (default true)
     */
    public $myTasksSnippetShow = true;

    /**
     * @var boolean determines if the space sidebar widget should be shown or not (default true)
     */
    public $myTasksSnippetShowSpace = true;

    /**
     * @var int maximum amount of dashboard event items
     */
    public $myTasksSnippetMaxItems = 5;

    /**
     * @var int defines the snippet widgets sort order
     */
    public $myTasksSnippetSortOrder = 1;

    /**
     * @var int defines if the global task menu item should be displayed
     */
    public $showGlobalMenuItem = 1;
    public $menuSortOrder = 500;

    /**
     * @var bool Default setting to hide tasks on stream
     */
    public bool $contentHiddenDefault = false;

    public function init()
    {
        $settings = $this->getSettings();

        $this->myTasksSnippetShow = $settings->get('myTasksSnippetShow', $this->myTasksSnippetShow);
        $this->myTasksSnippetShowSpace = $settings->get('myTasksSnippetShowSpace', $this->myTasksSnippetShowSpace);
        $this->myTasksSnippetMaxItems = $settings->get('myTasksSnippetMaxItems', $this->myTasksSnippetMaxItems);
        $this->myTasksSnippetSortOrder = $settings->get('myTasksSnippetSortOrder', $this->myTasksSnippetSortOrder);
        $this->showGlobalMenuItem = $settings->get('showGlobalMenuItem', $this->showGlobalMenuItem);
        $this->menuSortOrder = $settings->get('menuSortOrder', $this->menuSortOrder);
        $this->contentHiddenDefault = $settings->get('contentHiddenGlobalDefault', $this->contentHiddenDefault);
    }

    public function showMyTasksSnippet()
    {
        return $this->myTasksSnippetShow;
    }

    public function showMyTasksSnippetSpace()
    {
        return $this->myTasksSnippetShowSpace;
    }

    /**
     * Static initializer
     * @return \self
     */
    public static function instantiate()
    {
        return new self;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['myTasksSnippetShow', 'myTasksSnippetShowSpace', 'showGlobalMenuItem', 'contentHiddenDefault'],  'boolean'],
            ['myTasksSnippetMaxItems',  'number', 'min' => 1, 'max' => 30],
            [['myTasksSnippetSortOrder', 'menuSortOrder'],  'number', 'min' => 0],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'myTasksSnippetShow' => Yii::t('TasksModule.base', 'Show widget on Dashboard'),
            'myTasksSnippetShowSpace' => Yii::t('TasksModule.base', 'Show widget in Spaces'),
            'myTasksSnippetMaxItems' => Yii::t('TasksModule.base', 'Maximum number of entries in widget'),
            'myTasksSnippetSortOrder' => Yii::t('TasksModule.base', 'Sort order'),
            'showGlobalMenuItem' => Yii::t('TasksModule.base', 'Add entry to main navigation'),
            'menuSortOrder' => Yii::t('TasksModule.base', 'Sort Order'),
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $settings = $this->getSettings();

        $settings->set('myTasksSnippetShow', $this->myTasksSnippetShow);
        $settings->set('myTasksSnippetShowSpace', $this->myTasksSnippetShowSpace);
        $settings->set('myTasksSnippetMaxItems', $this->myTasksSnippetMaxItems);
        $settings->set('myTasksSnippetSortOrder', $this->myTasksSnippetSortOrder);
        $settings->set('showGlobalMenuItem', $this->showGlobalMenuItem);
        $settings->set('menuSortOrder', $this->menuSortOrder);
        $settings->set('contentHiddenGlobalDefault', $this->contentHiddenDefault);

        return true;
    }

    protected function getSettings(): SettingsManager
    {
        /* @var Module $module */
        $module = Yii::$app->getModule('tasks');

        return $module->settings;
    }
}
