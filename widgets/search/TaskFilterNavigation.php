<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 05.10.2018
 * Time: 20:51
 */

namespace humhub\modules\tasks\widgets\search;

use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\space\modules\manage\models\MembershipSearch;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\models\lists\TaskList;
use humhub\modules\tasks\models\state\TaskState;
use humhub\modules\ui\filter\widgets\FilterNavigation;
use humhub\modules\ui\form\widgets\MultiSelect;
use Yii;

class TaskFilterNavigation extends FilterNavigation
{
    public const PANEL_POSITION_TOP = 0;
    public const PANEL_POSITION_COL1 = 1;
    public const PANEL_POSITION_COL2 = 2;
    public const PANEL_POSITION_COL3 = 3;
    public const PANEL_POSITION_COL4 = 4;

    public const FILTER_BLOCK_TITLE = 'title';
    public const FILTER_BLOCK_COL1 = 'col1';
    public const FILTER_BLOCK_COL2 = 'col2';
    public const FILTER_BLOCK_COL3 = 'col3';
    public const FILTER_BLOCK_COL4 = 'col4';

    public $jsWidget = 'task.search.TaskFilter';

    /**
     * @var string view
     */
    public $view = 'taskFilterNavigation';

    /**
     * @inheritdoc
     */
    public $id = 'task-filter-nav';

    public $init = true;

    public $defaultBlock = self::FILTER_BLOCK_COL1;

    /**
     * @var TaskFilter
     */
    public $filter;

    /**
     * Initialization logic for default filter panels
     */
    protected function initFilterPanels()
    {
        $this->filterPanels[static::PANEL_POSITION_TOP] = [];
        $this->filterPanels[static::PANEL_POSITION_COL1] = [];
        $this->filterPanels[static::PANEL_POSITION_COL2] = [];
        $this->filterPanels[static::PANEL_POSITION_COL3] = [];
        $this->filterPanels[static::PANEL_POSITION_COL4] = [];
    }

    /**
     * Initialization logic for default filter blocks.
     *
     * This function can make use of the [[addFilterBlock()]] to add filter blocks to the previously initialized panels
     */
    protected function initFilterBlocks()
    {
        $this->addFilterBlock(static::FILTER_BLOCK_TITLE, [], static::PANEL_POSITION_TOP);
        $this->addFilterBlock(static::FILTER_BLOCK_COL1, [], static::PANEL_POSITION_COL1);
        $this->addFilterBlock(static::FILTER_BLOCK_COL2, [], static::PANEL_POSITION_COL2);
        $this->addFilterBlock(static::FILTER_BLOCK_COL3, [], static::PANEL_POSITION_COL3);
        $this->addFilterBlock(static::FILTER_BLOCK_COL4, [], static::PANEL_POSITION_COL4);
    }

    /**
     * Initialization logic for default filter blocks.
     *
     * This function can make use of the [[addFilter()]] to add filters the previously initialized blocks
     */
    protected function initFilters()
    {
        if (!$this->filter) {
            $this->filter = new TaskFilter();
        }

        $this->addFilter([
            'id' => TaskFilter::FILTER_TITLE,
            'category' => 'title',
            'title' => Yii::t('TasksModule.base', 'Title'),
            'class' => TextFilterInput::class,
            'changeAction' => null,
            'type' => 'text',
            'options' => [
                'label' => Yii::t('TasksModule.base', 'Title'),
                'style' => 'width:100%',
                'data-action-keydown' => 'inputChange',
                'data-action-keypress' => null,
                'placeholder' => Yii::t('TasksModule.base', 'Filter by title'),
            ],
            'sortOrder' => 100], static::FILTER_BLOCK_TITLE);

        $this->addFilter([
            'id' => TaskFilter::FILTER_DEADLINE,
            'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_DEADLINE),
            'title' => Yii::t('TasksModule.base', 'Deadline today'),
            'options' => ['label' => Yii::t('TasksModule.base', 'Filter')],
            'sortOrder' => 100], static::FILTER_BLOCK_COL1);

        $this->addFilter([
            'id' => TaskFilter::FILTER_OVERDUE,
            'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_OVERDUE),
            'title' => Yii::t('TasksModule.base', 'Overdue'),
            'options' => ['label' => Yii::t('TasksModule.base', 'Filter')],
            'sortOrder' => 100], static::FILTER_BLOCK_COL1);

        if (!Yii::$app->user->isGuest && (!$this->filter->contentContainer || $this->filter->contentContainer instanceof Space)) {
            $this->addFilter([
                'id' => TaskFilter::FILTER_ASSIGNED,
                'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_ASSIGNED),
                'title' => Yii::t('TasksModule.base', 'I\'m assigned'),
                'sortOrder' => 200], static::FILTER_BLOCK_COL1);

            $this->addFilter([
                'id' => TaskFilter::FILTER_RESPONSIBLE,
                'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_RESPONSIBLE),
                'title' => Yii::t('TasksModule.base', 'I\'m responsible'),
                'sortOrder' => 300], static::FILTER_BLOCK_COL1);

            $this->addFilter([
                'id' => TaskFilter::FILTER_MINE,
                'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_MINE),
                'title' => Yii::t('TasksModule.base', 'Created by me'),
                'sortOrder' => 400], static::FILTER_BLOCK_COL1);
        }

        $this->addFilter([
            'id' => TaskFilter::FILTER_SORT,
            'category' => 'sort',
            'class' => DropdownFilterInput::class,
            'items' => [
                '' => Yii::t('TasksModule.base', 'Default'),
                'deadline' => Yii::t('TasksModule.base', 'Deadline'),
                'new' => Yii::t('TasksModule.base', 'Newest first'),
                'old' => Yii::t('TasksModule.base', 'Oldest first'),
            ],
            'options' => [
                'label' => Yii::t('TasksModule.base', 'Sort'),
            ]], static::FILTER_BLOCK_COL3);

        $this->addFilter([
            'id' => TaskFilter::FILTER_STATE,
            'category' => 'states',
            'title' => Yii::t('TasksModule.base', 'Status'),
            'class' => PickerFilterInput::class,
            'options' => [
                'label' => Yii::t('TasksModule.base', 'Status'),
            ],
            'picker' => MultiSelect::class,
            'pickerOptions' => [
                'items' => TaskState::getStatusItems(),
                'placeholderMore' =>  Yii::t('TasksModule.base', 'Filter by status'),
                'name' => 'task-filter-state',
            ]], static::FILTER_BLOCK_COL3);

        if (!$this->filter->contentContainer) {
            $memberships = MembershipSearch::findByUser(Yii::$app->user->identity)->all();

            $spaces = [];
            foreach ($memberships as $membership) {
                /* @var Membership $membership */
                if ($membership->space->moduleManager->isEnabled('tasks')) {
                    $spaces[] = $membership->space;
                }
            }


            $this->addFilter([
                'id' => TaskFilter::FILTER_SPACE,
                'category' => 'spaces',
                'class' => PickerFilterInput::class,
                'picker' => SpacePickerField::class,
                'options' => [
                    'label' => Yii::t('TasksModule.base', 'Space'),
                ],
                'pickerOptions' => [
                    'name' => 'task-filter-spaces',
                    'defaultResults' => $spaces,
                ]], static::FILTER_BLOCK_COL4);
        }

        $this->addFilter([
            'id' => TaskFilter::FILTER_LIST,
            'category' => 'list',
            'class' => PickerFilterInput::class,
            'options' => [
                'label' => Yii::t('TasksModule.base', 'Task List'),
            ],
            'picker' => MultiSelect::class,
            'pickerOptions' => [
                'items' => TaskList::find()
                    ->select('content_tag.name')
                    ->indexBy('content_tag.id')
                    ->orderBy('content_tag.name')
                    ->column(),
                'placeholderMore' =>  Yii::t('TasksModule.base', 'Select (All)'),
                'name' => 'task-filter-list',
            ]], static::FILTER_BLOCK_COL4);

        $this->addFilter([
            'id' => TaskFilter::FILTER_DATE_START,
            'category' => 'date_start',
            'class' => DateFilter::class,
            'filterOptions' => [
                'label' => Yii::t('TasksModule.base', 'Start date'),
                'placeholder' => Yii::t('TasksModule.base', 'Start date'),
            ]], static::FILTER_BLOCK_COL2);

        $this->addFilter([
            'id' => TaskFilter::FILTER_DATE_END,
            'category' => 'date_end',
            'class' => DateFilter::class,
            'filterOptions' => [
                'label' => Yii::t('TasksModule.base', 'End date'),
                'placeholder' => Yii::t('TasksModule.base', 'End date'),
            ]], static::FILTER_BLOCK_COL2);
    }

    public function getData()
    {
        $container = ContentContainerHelper::getCurrent();
        return [
            'container-guid' => $container ? $container->guid : null,
            'filter-url' => TaskUrl::globalFilter($this->filter->contentContainer),
            'csv-export-url' => TaskUrl::exportCsv($this->filter->contentContainer),
            'xlsx-export-url' => TaskUrl::exportXlsx($this->filter->contentContainer),
        ];
    }
}
