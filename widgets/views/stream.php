<?php if ($this->showFilters && !Yii::app()->user->isGuest) : ?>
    <ul class="nav nav-tabs wallFilterPanel" id="filter" style="display: none;">
        <li class=" dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo Yii::t('TasksModule.widgets_views_stream', 'Filter'); ?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="#" class="wallFilter" id="filter_tasks_meAssigned"><i class="fa fa-square-o"></i> <?php echo Yii::t('TasksModule.widgets_views_stream', 'Assigned to me'); ?></a></li>
                <li><a href="#" class="wallFilter" id="filter_entry_mine"><i class="fa fa-square-o"></i> <?php echo Yii::t('TasksModule.widgets_views_stream', 'Created by me'); ?></a></li>
                <li><a href="#" class="wallFilter" id="filter_tasks_open"><i class="fa fa-square-oy"></i> <?php echo Yii::t('TasksModule.widgets_views_stream', 'State is open'); ?></a></li>
                <li><a href="#" class="wallFilter" id="filter_tasks_finished"><i class="fa fa-square-o"></i> <?php echo Yii::t('TasksModule.widgets_views_stream', 'State is finished'); ?></a></li>
                <li><a href="#" class="wallFilter" id="filter_tasks_notassigned"><i class="fa fa-square-o"></i> <?php echo Yii::t('TasksModule.widgets_views_stream', 'Nobody assigned'); ?></a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo Yii::t('TasksModule.widgets_views_stream', 'Sorting'); ?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="#" class="wallSorting" id="sorting_c"><i class="fa fa-check-square-o"></i> <?php echo Yii::t('TasksModule.widgets_views_stream', 'Creation time'); ?></a></li>
                <li><a href="#" class="wallSorting" id="sorting_u"><i class="fa fa-square-o"></i> <?php echo Yii::t('TasksModule.widgets_views_stream', 'Last update'); ?></a></li>
            </ul>
        </li>
    </ul>
<?php endif; ?>

<div id="wallStream">

    <!-- DIV for a normal wall stream -->
    <div class="s2_stream" style="display:none">

        <div class="s2_streamContent"></div>
        <div class="loader streamLoader">
            <div class="sk-spinner sk-spinner-three-bounce">
                <div class="sk-bounce1"></div>
                <div class="sk-bounce2"></div>
                <div class="sk-bounce3"></div>
            </div>
        </div>

        <div class="emptyStreamMessage">

            <div class="placeholder <?php echo $this->messageStreamEmptyCss; ?>">
                <div class="panel">
                    <div class="panel-body">
                        <?php echo $this->messageStreamEmpty; ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="emptyFilterStreamMessage">
            <div class="placeholder <?php echo $this->messageStreamEmptyWithFiltersCss; ?>">
                <div class="panel">
                    <div class="panel-body">
                        <?php echo $this->messageStreamEmptyWithFilters; ?>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- DIV for an single wall entry -->
    <div class="s2_single" style="display: none;">
        <div class="back_button_holder">
            <a href="#"
               class="singleBackLink btn btn-primary"><?php echo Yii::t('WallModule.widgets_views_stream', 'Back to stream'); ?></a><br><br>
        </div>
        <div class="p_border"></div>

        <div class="s2_singleContent"></div>
        <div class="loader streamLoaderSingle"></div>
        <div class="test"></div>
    </div>
</div>

<!-- show "Load More" button on mobile devices -->
<div class="col-md-12 text-center visible-xs visible-sm">
    <button id="btn-load-more" class="btn btn-primary btn-lg ">Load more</button>
    <br/><br/>
</div>




