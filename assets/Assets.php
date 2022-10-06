<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\tasks\controllers\ListController;
use humhub\modules\tasks\controllers\SearchController;

class Assets extends AssetBundle
{

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

    /**
     * @inheritdoc
     */
    public static function register($view)
    {
        if ($view->context instanceof ListController || $view->context instanceof SearchController) {
            $colorLink = $view->theme->variable('link');
            $colorInfo = $view->theme->variable('info');
            $view->registerCss('#task-space-menu li.active a{color:' . $colorLink . '}'
                . '.task-overview #task-filter-nav .task-bottom-panel .filterInput[data-filter-type=checkbox] .fa.fa-check-square-o{border-color:' . $colorInfo . ';background:' . $colorInfo . '}');
        }

        return parent::register($view);
    }
}
