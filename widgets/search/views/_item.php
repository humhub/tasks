<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
use humhub\modules\tasks\widgets\search\TaskSearchListEntry;

?>
<?= TaskSearchListEntry::widget(['task' => $model, 'contentContainer' => $contentContainer, 'filterResult' => true])?>

