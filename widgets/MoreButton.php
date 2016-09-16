<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\tasks\widgets;

use yii\web\JsExpression;

/**
 * MoreButton
 *
 * @author Luke
 */
class MoreButton extends \humhub\widgets\ShowMorePager
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->ajaxButtonOptions['ajaxOptions']['success'] = new JsExpression('function(html){ $("#tasksList").find(".pagination-container").remove(); $("#tasksList").append(html); resortTasks() }');
        $this->ajaxButtonOptions['ajaxOptions']['data'] = new JsExpression('{"filter": JSON.stringify($("tasksList").data("filters")) }');
    }

}
