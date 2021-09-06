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
use Yii;
use humhub\modules\tasks\helpers\TaskUrl;
use humhub\modules\tasks\models\forms\TaskFilter;
use humhub\modules\tasks\models\state\TaskState;
use humhub\modules\ui\filter\widgets\FilterNavigation;
use humhub\modules\ui\filter\widgets\PickerFilterInput;
use humhub\modules\ui\form\widgets\MultiSelect;
use humhub\modules\ui\filter\widgets\TextFilterInput;

class TaskFilterNavigation extends FilterNavigation
{
    const PANEL_POSITION_TOP = 1;
    const PANEL_POSITION_BOTTOM_LEFT = 2;
    const PANEL_POSITION_BOTTOM_CENTER = 3;
    const PANEL_POSITION_BOTTOM_RIGHT = 4;

    const FILTER_BLOCK_TITLE = 'title';
    const FILTER_BLOCK_CHECKBOX = 'checkbox';
    const FILTER_BLOCK_PICKER = 'picker';
    const FILTER_BLOCK_DATEPICKER = 'datePicker';

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

    public $defaultBlock = self::FILTER_BLOCK_CHECKBOX;

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
        $this->filterPanels[static::PANEL_POSITION_BOTTOM_LEFT] = [];
        $this->filterPanels[static::PANEL_POSITION_BOTTOM_CENTER] = [];
        $this->filterPanels[static::PANEL_POSITION_BOTTOM_RIGHT] = [];
    }

    /**
     * Initialization logic for default filter blocks.
     *
     * This function can make use of the [[addFilterBlock()]] to add filter blocks to the previously initialized panels
     */
    protected function initFilterBlocks()
    {
        $this->addFilterBlock(static::FILTER_BLOCK_TITLE, [], static::PANEL_POSITION_TOP);
        $this->addFilterBlock(static::FILTER_BLOCK_CHECKBOX, [], static::PANEL_POSITION_BOTTOM_LEFT);
        $this->addFilterBlock(static::FILTER_BLOCK_PICKER, [], static::PANEL_POSITION_BOTTOM_CENTER);
        $this->addFilterBlock(static::FILTER_BLOCK_DATEPICKER, [], static::PANEL_POSITION_BOTTOM_RIGHT);
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

        /**
         * Patches an textfilter issue with HumHub < 1.3.9 which uses keypress instead of keydown
         */
        if (version_compare(Yii::$app->version, '1.3.9', 'lt')) {
            $this->addFilter([
                'id' => TaskFilter::FILTER_TITLE,
                'category' => 'title',
                'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'Title'),
                'class' => TextFilterInput::class,
                'changeAction' => null,
                'options' => [
                    'style' => 'width:100%',
                    'data-action-keydown' => 'inputChange',
                    'data-action-keypress' => null,
                    'placeholder' => Yii::t('TasksModule.views_index_index', 'Filter tasks by title')
                ],
                'sortOrder' => 100], static::FILTER_BLOCK_TITLE);
        } else {
            $this->addFilter([
                'id' => TaskFilter::FILTER_TITLE,
                'category' => 'title',
                'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'Title'),
                'class' => TextFilterInput::class,
                'options' => [
                    'style' => 'width:100%',
                    'placeholder' => Yii::t('TasksModule.views_index_index', 'Filter tasks by title')
                ],
                'sortOrder' => 100], static::FILTER_BLOCK_TITLE);
        }


        $this->addFilter([
            'id' => TaskFilter::FILTER_OVERDUE,
            'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_OVERDUE),
            'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'Overdue'),
            'sortOrder' => 100], static::FILTER_BLOCK_CHECKBOX);


        if (!Yii::$app->user->isGuest && (!$this->filter->contentContainer || $this->filter->contentContainer instanceof Space)) {
            $this->addFilter([
                'id' => TaskFilter::FILTER_ASSIGNED,
                'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_ASSIGNED),
                'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'I\'m assigned'),
                'sortOrder' => 200], static::FILTER_BLOCK_CHECKBOX);

            $this->addFilter([
                'id' => TaskFilter::FILTER_RESPONSIBLE,
                'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_RESPONSIBLE),
                'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'I\'m responsible'),
                'sortOrder' => 300], static::FILTER_BLOCK_CHECKBOX);

            $this->addFilter([
                'id' => TaskFilter::FILTER_MINE,
                'checked' => $this->filter->isFilterActive(TaskFilter::FILTER_MINE),
                'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'Created by me'),
                'sortOrder' => 400], static::FILTER_BLOCK_CHECKBOX);
        }

        $this->addFilter([
            'id' => TaskFilter::FILTER_STATE,
            'category' => 'states',
            'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'Status'),
            'class' => PickerFilterInput::class,
            'picker' => MultiSelect::class,
            'pickerOptions' => [
                'items' => TaskState::getStatusItems(),
                'placeholder' =>  Yii::t('TasksModule.models_forms_TaskFilter', 'Filter status'),
                'placeholderMore' =>  Yii::t('TasksModule.models_forms_TaskFilter', 'Filter status'),
                'name' => 'task-filter-state'
            ]],static::FILTER_BLOCK_PICKER);


        if(!$this->filter->contentContainer) {
            $memberships = MembershipSearch::findByUser(Yii::$app->user->identity)->all();

            $spaces = [];
            foreach ($memberships as $membership) {
                /* @var Membership $membership */
                if($membership->space->moduleManager->isEnabled('tasks')) {
                    $spaces[] = $membership->space;
                }
            }


            $this->addFilter([
                'id' => TaskFilter::FILTER_SPACE,
                'category' => 'spaces',
                'title' => Yii::t('TasksModule.models_forms_TaskFilter', 'Spaces'),
                'class' => PickerFilterInput::class,
                'picker' => SpacePickerField::class,
                'pickerOptions' => [
                    'name' => 'task-filter-spaces',
                    'defaultResults' => $spaces,
                ]], static::FILTER_BLOCK_PICKER);
        }

        $this->addFilter([
            'id' => TaskFilter::FILTER_DATE_START,
            'category' => 'date_start',
            'class' => DateFilter::class,
            'filterOptions' => [
                'placeholder' =>  Yii::t('TasksModule.models_forms_TaskFilter', 'Start date'),
            ]], static::FILTER_BLOCK_DATEPICKER);

        $this->addFilter([
            'id' => TaskFilter::FILTER_DATE_END,
            'category' => 'date_end',
            'class' => DateFilter::class,
            'filterOptions' => [
                'placeholder' => Yii::t('TasksModule.models_forms_TaskFilter', 'End date'),
            ]], static::FILTER_BLOCK_DATEPICKER);
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