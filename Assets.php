<?php

namespace humhub\modules\tasks;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    public $css = [
        'tasks.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }

}
