<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<div class="modal-dialog modal-dialog-normal animated fadeIn">
    <div class="modal-content">

        <?php $form = ActiveForm::begin(); ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <?php if (Yii::$app->request->get('id') != null) : ?>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t('TasksModule.views_task_edit', '<strong>Edit</strong> task'); ?></h4>
            <?php else :?>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t('TasksModule.views_task_edit', '<strong>Create</strong> new task'); ?></h4>
            <?php endif; ?>
        </div>


        <div class="modal-body">

                <?php echo $form->field($task, 'title')->textarea(['id' => 'itemTask', 'class' => 'form-control autosize', 'rows' => '1', 'placeholder' => Yii::t('TasksModule.views_task_edit', 'What is to do?')]); ?>

            <div class="row">
                <div class="col-md-8">

                    <?php echo $form->field($task, 'assignedUserGuids')->textInput(['id' => 'assignedUserGuids']); ?>

                    <?php
                    // attach mention widget to it
                    echo humhub\modules\user\widgets\UserPicker::widget(array(
                        'model' => $task,
                        'inputId' => 'assignedUserGuids',
                        'attribute' => 'assignedUserGuids',
                        'userSearchUrl' => $this->context->contentContainer->createUrl('/space/membership/search', array('keyword' => '-keywordPlaceholder-')),
                        'maxUsers' => 10,
                        'placeholderText' => Yii::t('TasksModule.views_task_edit', 'Assign users'),
                    ));
                    ?>

                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <?php echo $form->field($task, 'deadline')->widget(yii\jui\DatePicker::className(), ['dateFormat' => Yii::$app->params['formatter']['defaultDateFormat'], 'clientOptions' => [], 'options' => ['class' => 'form-control', 'placeholder' => Yii::t('TasksModule.views_task_edit', 'Deadline')]]); ?>
                    </div>

                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12">
                    <?php
                    echo \humhub\widgets\AjaxButton::widget([
                        'label' => Yii::t('TasksModule.views_task_edit', 'Save'),
                        'ajaxOptions' => [
                            'type' => 'POST',
                            'beforeSend' => new yii\web\JsExpression('function(){ setModalLoader(); }'),
                            'success' => new yii\web\JsExpression('function(html){ $("#globalModal").html(html); }'),
                            'url' => $task->content->container->createUrl('/tasks/task/edit', ['id' => $task->id]),
                        ],
                        'htmlOptions' => [
                            'class' => 'btn btn-primary'
                        ]
                    ]);
                    ?>

                    <button type="button" class="btn btn-primary"
                            data-dismiss="modal"><?php echo Yii::t('TasksModule.views_task_edit', 'Cancel'); ?></button>
                </div>
            </div>

        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>


<script type="text/javascript">

    $('.autosize').autosize();

    $(document).ready(function () {
        var myInterval = setInterval(function () {
            $('#itemTask').focus();
            clearInterval(myInterval);
        }, 100);
    });

</script>