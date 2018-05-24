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


    public function init()
    {
        $module = Yii::$app->getModule('tasks');
        $this->myTasksSnippetShow = $module->settings->get('myTasksSnippetShow', $this->myTasksSnippetShow);
        $this->myTasksSnippetShowSpace = $module->settings->get('myTasksSnippetShowSpace', $this->myTasksSnippetShowSpace);
        $this->myTasksSnippetMaxItems = $module->settings->get('myTasksSnippetMaxItems', $this->myTasksSnippetMaxItems);
        $this->myTasksSnippetSortOrder = $module->settings->get('myTasksSnippetSortOrder', $this->myTasksSnippetSortOrder);
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
            [['myTasksSnippetShow', 'myTasksSnippetShowSpace'],  'boolean'],
            ['myTasksSnippetMaxItems',  'number', 'min' => 1, 'max' => 30],
            ['myTasksSnippetSortOrder',  'number', 'min' => 0],
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
        return true;
    }
}
