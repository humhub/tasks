<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\models;

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


    public function init()
    {
        $module = Yii::$app->getModule('tasks');
        $this->myTasksSnippetShow = $module->settings->get('myTasksSnippetShow', $this->myTasksSnippetShow);
        $this->myTasksSnippetShowSpace = $module->settings->get('myTasksSnippetShowSpace', $this->myTasksSnippetShowSpace);
        $this->myTasksSnippetMaxItems = $module->settings->get('myTasksSnippetMaxItems', $this->myTasksSnippetMaxItems);
        $this->myTasksSnippetSortOrder = $module->settings->get('myTasksSnippetSortOrder', $this->myTasksSnippetSortOrder);
        $this->showGlobalMenuItem = $module->settings->get('showGlobalMenuItem', $this->showGlobalMenuItem);
        $this->menuSortOrder = $module->settings->get('menuSortOrder', $this->menuSortOrder);
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
            [['myTasksSnippetShow', 'myTasksSnippetShowSpace', 'showGlobalMenuItem'],  'boolean'],
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
            'myTasksSnippetShow' => Yii::t('TasksModule.config', 'Show snippet'),
            'myTasksSnippetShowSpace' => Yii::t('TasksModule.config', 'Show snippet in Space'),
            'myTasksSnippetMaxItems' => Yii::t('TasksModule.config', 'Max tasks items'),
            'myTasksSnippetSortOrder' => Yii::t('TasksModule.config', 'Sort order'),
            'showGlobalMenuItem' => Yii::t('TasksModule.config', 'Show global task menu item'),
            'menuSortOrder' => Yii::t('TasksModule.config', 'Menu Item sort order'),
        ];
    }

    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        $module = Yii::$app->getModule('tasks');
        $module->settings->set('myTasksSnippetShow', $this->myTasksSnippetShow);
        $module->settings->set('myTasksSnippetShowSpace', $this->myTasksSnippetShowSpace);
        $module->settings->set('myTasksSnippetMaxItems', $this->myTasksSnippetMaxItems);
        $module->settings->set('myTasksSnippetSortOrder', $this->myTasksSnippetSortOrder);
        $module->settings->set('showGlobalMenuItem', $this->showGlobalMenuItem);
        $module->settings->set('menuSortOrder', $this->menuSortOrder);
        return true;
    }
}
