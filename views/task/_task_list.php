<?php foreach ($tasks as $task) : ?>
	<?php echo $this->render('_task_entry', array('task'=>$task, 'contentContainer' => $contentContainer));?>
<?php endforeach; ?>
