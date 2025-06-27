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
use Yii;

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
        'css/task.min.css',
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
            $view->registerCss('#task-space-menu li.active a{color:var(--link)}'
                . '.task-overview #task-filter-nav .task-bottom-panel .filterInput[data-filter-type=checkbox] .fa.fa-check-square-o{border-color:var(--info);background:var(--info)}');
        }

        $view->registerJsConfig([
            'task' => [
                'text' => [
                    'success.delete' => Yii::t('base', 'Deleted'),
                ],
            ],
        ]);

        return parent::register($view);
    }
}
