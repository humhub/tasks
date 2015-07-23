<?php
use humhub\compat\CActiveForm;
use yii\helpers\Html;
use yii\jui\DatePicker;

?>



<?php $form = CActiveForm::begin(); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"
        id="myModalLabel"><?php echo Yii::t('TasksModule.views_task_edit', '<strong>Create</strong> new task'); ?></h4>
</div>


<div class="modal-body">


    <div class="form-group">
        <label
            for="Task_title"><?php echo Yii::t('TasksModule.views_task_edit', 'Task description'); ?></label>
        <?php echo $form->textArea($task, 'title', array('id' => 'itemTask', 'class' => 'form-control autosize', 'rows' => '1', 'placeholder' => Yii::t('TasksModule.views_task_edit', 'What is to do?'))); ?>
        <?php echo $form->error($task, 'title'); ?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <div class="form-group">
                    <label
                        for="Task_preassignedUsers"><?php echo Yii::t('TasksModule.views_task_edit', 'Assign users'); ?></label>
                    <?php echo $form->textField($task, 'assignedUserGuids', array('id' => 'assignedUserGuids', 'class' => 'form-control', 'placeholder' => Yii::t('TasksModule.views_task_edit', 'Preassign user(s) for this task.'))); ?>
                    <?php echo $form->error($task, 'assignedUserGuids'); ?>
                </div>

                <?php
                echo humhub\modules\user\widgets\UserPicker::widget(array(
                    'model' => $task,
                    'inputId' => 'assignedUserGuids',
                    'attribute' => 'assignedUserGuids',
                    'userSearchUrl' => $this->context->contentContainer->createUrl('/space/membership/search', array('keyword' => '-keywordPlaceholder-')),
                    'maxUsers' => 10,
                    'placeholderText' => Yii::t('TasksModule.views_task_edit', 'Assign users to this task')
                ));
                ?>
            </div>
        </div>
        <div class="col-md-4">

            <label
                for="Task_deadline"><?php echo Yii::t('TasksModule.views_task_edit', 'Deadline'); ?></label>
            <?php
            echo DatePicker::widget([
                'model' => $task,
                'attribute' => 'deadline',
                'options' => [
                    'class' => 'form-control',
                    'id' => 'deadline',
                    'placeholder' => Yii::t('TasksModule.views_task_edit', 'Deadline for this task?')
                ]
            ]);
            ?>

            <?php echo $form->error($task, 'deadline'); ?>
        </div>
    </div>
<br>

    <div class="row">
        <div class="col-md-8">
            <?php echo Html::submitButton(Yii::t('TasksModule.views_task_edit', 'Save'), array('class' => 'btn btn-primary')); ?>

            <button type="button" class="btn btn-primary"
                    data-dismiss="modal"><?php echo Yii::t('TasksModule.views_task_edit', 'Cancel'); ?></button>
        </div>
        <div class="col-md-4 text-right">
            <a class="btn btn-danger" href="#"><?php echo Yii::t('TasksModule.views_task_edit', 'Delete'); ?></a>
        </div>
    </div>

</div>

<?php CActiveForm::end(); ?>


<script type="text/javascript">

    $('.autosize').autosize();

    $(document).ready(function () {
        var myInterval = setInterval(function () {
            $('#itemTask').focus();
            clearInterval(myInterval);
        }, 100);
    });

</script>