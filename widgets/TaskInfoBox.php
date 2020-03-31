<?php


namespace humhub\modules\tasks\widgets;


use humhub\components\Widget;
use humhub\modules\tasks\models\Task;

class TaskInfoBox extends Widget
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string|array
     */
    public $value;

    /**
     * @var $string
     */
    public $icon;

    /**
     * @var string
     */
    public $cssClass;

    public $textClass;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('taskInfoBox', ['cssClass' => $this->cssClass, 'textClass'  => $this->textClass, 'title' => $this->getTitle(), 'value' => $this->getValue(), 'icon' => $this->getIcon()]);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getIcon()
    {
        return $this->icon;
    }

}