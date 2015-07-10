<?php

namespace module\tasks;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $sourcePath = '@module/tasks/assets';
    public $css = [
        'tasks.css',
    ];

}
