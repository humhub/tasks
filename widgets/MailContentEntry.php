<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks\widgets;

use humhub\components\ActiveRecord;
use Yii;
use yii\base\Widget;
use humhub\widgets\RichText;
use humhub\components\rendering\ViewPathRenderer;
use humhub\components\rendering\Viewable;
use humhub\modules\content\interfaces\ContentOwner;

/**
 * MailContentEntry renders a simple mail content with originator information and an
 * content block to simulate a wall entry as good as possible.
 *
 * @author davidborn
 */
class MailContentEntry extends Widget
{

    /**
     * @var \humhub\modules\user\models\User content originator 
     */
    public $originator;
    
    /**
     * @var string|Viewable|ContentOwner content to render 
     */
    public $content;
    
    /**
     * @var \humhub\modules\space\models\Space space of content (optional)
     */
    public $space;

    /**
     * @var ActiveRecord (optional)
     */
    public $source;
    
    /** 
     * @var string content date 
     */
    public $date;
    
    /**
     * @var boolean if Notification is a Reminder
     */
    public $isReminder;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (is_string($this->content)) {
            $content = $this->content;
        } else if ($this->content instanceof Viewable) {
            try {
                $renderer = new ViewPathRenderer(['parent' => true, 'subPath' => 'mail']);
                $content =  $renderer->render($this->content);
            } catch (\yii\base\ViewNotFoundException $e) {
                Yii::error($e);
            }
        } else if ($this->content instanceof \humhub\modules\content\interfaces\ContentOwner) {
            $content = RichText::widget(['text' => $this->content->getContentDescription(), 'minimal' => true]);
            if(!$this->originator) {
                $this->originator = $this->content->content->createdBy;
            }
        }


        return $this->render('mailContentEntry', [
                    'originator' => $this->originator,
                    'content' => $content,
                    'space' => $this->space,
                    'date' => $this->date,
                    'isReminder' => $this->isReminder,
                    'source' => $this->source
        ]);
    }

}

?>