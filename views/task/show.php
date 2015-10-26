<?php
use yii\helpers\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\comment\models\Comment;

humhub\modules\tasks\Assets::register($this);
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div id="open-tasks">
		    <?php foreach ($tasks as $task) : ?>
				<?php echo $this->render('_task_entry', array('task'=>$task, 'contentContainer' => $contentContainer));?>
		    <?php endforeach; ?>
		    <?php if (count($tasks) == 0) : ?>
		        <em><?php echo Yii::t('TasksModule.views_task_show', 'No open tasks...'); ?></em>
		    <?php endif; ?>
		</div>
		<br>
		<a href="<?php echo $contentContainer->createUrl('edit'); ?>" class="btn btn-primary" data-target="#globalModal">
			<i class="fa fa-plus"></i> <?php echo Yii::t('TasksModule.views_task_show', 'Add Task'); ?>
		</a>
		<a data-toggle="collapse" id="completed-task-link" href="#completed-tasks" class="show-completed-tasks-link" style="display: none;">
			<i class="fa fa-check"></i>
		</a>
		<br><br>
		<div class="collapse <?php if (Yii::$app->request->get('completed') != null) : ?>in<?php endif; ?>" id="completed-tasks">
			<i class="fa fa-spinner fa-spin fa-3x"></i>
		</div>
	</div>
</div>
<script type="text/javascript">
    var _id = <?php echo (int) Yii::$app->request->get('id'); ?>;
    var _completedTaskCount = <?php echo $completedTaskCount; ?>;
    var _completedTaskButtonText = "<?php echo Yii::t('TasksModule.views_task_show', 'completed tasks'); ?>";

    if (_id > 0) {
        $('#task_' + _id).addClass('highlight');
        $('#task_' + _id).animate({
            backgroundColor: "#fff"
        }, 6000);
    }
    function completeTask(id) {
        $('#task_' + id + ' .open-check').addClass('hidden');
        $('#task_' + id + ' .completed-check').removeClass('hidden');
        $('#task_' + id + ' .task-title').addClass('task-completed');
        $('#task_' + id + ' .assigned-users').addClass('task-completed-controls');
        $('#task_' + id + ' .label').addClass('task-completed-controls');
        $('#task_' + id).appendTo('#completed-tasks');
        _completedTaskCount++;
        handleCompletedTasks();
    }
    function reopenTask(id) {
        $('#task_' + id + ' .open-check').removeClass('hidden');
        $('#task_' + id + ' .completed-check').addClass('hidden');
        $('#task_' + id + ' .task-title').removeClass('task-completed');
        $('#task_' + id + ' .assigned-users').removeClass('task-completed-controls');
        $('#task_' + id + ' .label').removeClass('task-completed-controls');
        $('#task_' + id).appendTo('#open-tasks');
        _completedTaskCount--;
        handleCompletedTasks();
    }
    function handleCompletedTasks() {
        $('#completed-task-link').html('<i class="fa fa-check"></i> ' + _completedTaskCount + ' ' + _completedTaskButtonText);

        if (_completedTaskCount != 0) {
            $('#completed-task-link').fadeIn('fast');
        } else {
            $('#completed-task-link').fadeOut('fast');
            $('#completed-tasks').removeClass('in');
        }
    }
    $(document).ready(function () {
        handleCompletedTasks();
    });
    $('#completed-task-link').on('click', function () {
    	$.ajax({ 
			url: "<?php echo $contentContainer->createUrl('show-completed'); ?>",
			success: function(response){
				$('#completed-tasks').html(response);
				$('#completed-task-link').off('click');
			}
		});
    });
</script>
