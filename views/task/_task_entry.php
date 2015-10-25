<?php
use yii\helpers\Html;
use humhub\modules\tasks\models\Task;
use humhub\modules\comment\models\Comment;

humhub\modules\tasks\Assets::register($this);
?>

<div class="media task" id="task_<?php echo $task->id; ?>">
<?php
$currentUserAssigned = false;
// Check if current user is assigned to this task
foreach ( $task->assignedUsers as $au ) {
	if ($au->id == Yii::$app->user->id) {
		$currentUserAssigned = true;
		break;
	}
}
?>
	<div class="open-check<?php echo $task->status == Task::STATUS_FINISHED?' hidden':''; ?>">
    	<?php echo \humhub\widgets\AjaxButton::widget ( [ 
				'label' => '<div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="' . Yii::t ( "TasksModule.widgets_views_entry", "Click, to finish this task" ) . '"><i class="fa fa-square-o task-check"> </i></div>',
				'tag' => 'a',
				'ajaxOptions' => [ 
					'dataType' => "json",
					'beforeSend' => "completeTask(" . $task->id . ")",
					'success' => "function(json) {  $('#wallEntry_'+json.wallEntryId).html(parseHtml(json.output)); }",
					'url' => $contentContainer->createUrl ( '/tasks/task/change-status', array (
						'taskId' => $task->id,
						'status' => Task::STATUS_FINISHED 
					) ) 
				],
				'htmlOptions' => [ 
					'id' => "TaskFinishLink_" . $task->id 
				] 
			] ); ?>
	</div>
	<div class="completed-check<?php echo $task->status != Task::STATUS_FINISHED?' hidden':''; ?>">
		<?php echo \humhub\widgets\AjaxButton::widget ( [ 
				'label' => '<div class="tasks-check tt pull-left" style="margin-right: 0;" data-toggle="tooltip" data-placement="top" data-original-title="' . Yii::t ( "TasksModule.widgets_views_entry", "This task is already done. Click to reopen." ) . '"><i class="fa fa-check-square-o task-check"> </i></div>',
				'tag' => 'a',
				'ajaxOptions' => [ 
					'dataType' => "json",
					'beforeSend' => "reopenTask(" . $task->id . ")",
					'success' => "function(json) {  $('#wallEntry_'+json.wallEntryId).html(parseHtml(json.output));}",
					'url' => $contentContainer->createUrl ( '/tasks/task/change-status', array (
						'taskId' => $task->id,
						'status' => Task::STATUS_OPEN 
					) ) 
				],
				'htmlOptions' => [ 
						'id' => "TaskOpenLink_" . $task->id 
				] 
			] ); ?>
	</div>

	<div class="media-body">
		<span class="pull-left">
			<a href="<?php echo $task->creator->getUrl(); ?>" id="user_<?php echo $task->id; ?>"> 
				<img src="<?php echo $task->creator->getProfileImage()->getUrl(); ?>" class="img-rounded tt" height="24" width="24" alt="24x24" style="width: 24px; height: 24px;" data-toggle="tooltip"
					data-placement="top" title="" data-original-title="<?php echo Html::encode($task->creator->displayName); ?>">
			</a>
		</span>
		<span class="task-title pull-left"><?php echo $task->title; ?></span>
		<?php if ($task->hasDeadline()) : ?>
           	<?php
				$timestamp = strtotime ( $task->deadline );
				$class = "label label-default";
				if (date ( "d.m.yy", $timestamp ) <= date ( "d.m.yy", time () )) {
					$class = "label label-danger";
				}
			?>
        	<span class="<?php echo $class; ?>"><?php echo date("d. M", $timestamp); ?></span>
		<?php endif; ?>
		<div class="task-controls end pull-right">
			<a href="<?php echo $contentContainer->createUrl('edit', ['id' => $task->id]); ?>" class="tt" data-target="#globalModal" data-toggle="tooltip" data-placement="top" 
				data-original-title="Edit Task">
				<i class="fa fa-pencil"></i>
			</a>
			<?php echo humhub\widgets\ModalConfirm::widget ( array (
						'uniqueID' => 'modal_delete_task_' . $task->id,
						'linkOutput' => 'a',
						'title' => Yii::t ( 'TasksModule.views_task_show', '<strong>Confirm</strong> deleting' ),
						'message' => Yii::t ( 'TasksModule.views_task_show', 'Do you really want to delete this task?' ),
						'buttonTrue' => Yii::t ( 'TasksModule.views_task_show', 'Delete' ),
						'buttonFalse' => Yii::t ( 'TasksModule.views_task_show', 'Cancel' ),
						'linkContent' => '<i class="fa fa-times-circle-o colorDanger"></i>',
						'linkHref' => $contentContainer->createUrl ( 'delete', array (
							'id' => $task->id 
						) ),
						'confirmJS' => "$('#task_" . $task->id . "').fadeOut('fast')" 
					) ); ?>
		</div>
		<div class="task-controls pull-right">
			<a data-toggle="collapse" href="#task-comment-<?php echo $task->id; ?>" onclick="$('#comment_humhubmodulestasksmodelsTask_<?php echo $task->id; ?>').show();return false;"
				aria-expanded="false" aria-controls="collapseTaskComments"><i class="fa fa-comment-o">
				</i> <?php echo $count = Comment::GetCommentCount($task->className(), $task->id); ?>
			</a>
		</div>
		<div class="task-controls assigned-users pull-right" style="display: inline;">
			<!-- Show assigned user -->
			<?php foreach ($task->assignedUsers as $user): ?>
				<a href="<?php echo $user->getUrl(); ?>" id="user_<?php echo $task->id; ?>"> 
					<img src="<?php echo $user->getProfileImage()->getUrl(); ?>" class="img-rounded tt" height="24" width="24" alt="24x24" style="width: 24px; height: 24px;" data-toggle="tooltip"
						data-placement="top" title="" data-original-title="<?php echo Html::encode($user->displayName); ?>">
				</a>
			<?php endforeach; ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="wall-entry collapse" id="task-comment-<?php echo $task->id; ?>">
		<div class="wall-entry-controls">
			<?php //echo \humhub\modules\comment\widgets\CommentLink::widget(array('object' => $task)); ?>
		</div>
        <?php echo \humhub\modules\comment\widgets\Comments::widget(array('object' => $task)); ?>
	</div>
	<script type="text/javascript">
		$('#task-comment-<?php echo $task->id; ?>').on('shown.bs.collapse', function () {
        	$('#newCommentForm_humhubmodulestasksmodelsTask_<?php echo $task->id; ?>_contenteditable').focus();
		});
	</script>
</div>