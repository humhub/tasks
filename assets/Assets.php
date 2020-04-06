<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    /**
     * v1.5 compatibility defer script loading
     *
     * Migrate to HumHub AssetBundle once minVersion is >=1.5
     *
     * @var bool
     */
    public $defer = true;

    /**
     * @inheritDoc
     */
    public $sourcePath = '@tasks/resources';

    /**
     * @inheritDoc
     */
    public $css = [
        'css/task.css',
    ];

    // We have to use the timeentry lib for the duration since the TimePicker widget uses an older version without maxHour setting...
    /**
     * @inheritDoc
     */
    public $js = [
        'js/jquery.ui.touch-punch.min.js',  // Add jQuery fix for using sortable() on mobile devices - Homepage: http://touchpunch.furf.com/
        'js/humhub.task.js',
        'js/humhub.task.list.js',
        'js/humhub.task.checklist.js',
        'js/humhub.task.search.js',
    ];
}
