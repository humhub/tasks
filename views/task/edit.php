<?php

use humhub\modules\tasks\models\Task;
use humhub\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="modal-dialog modal-dialog-normal animated fadeIn">
    <div class="modal-content">

        <?php $form = ActiveForm::begin(['id' => 'tasksEditForm']); ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <?php if (Yii::$app->request->get('id') != null) : ?>
                <h4 class="modal-title"
                    id="myModalLabel"><?php echo Yii::t('TasksModule.base', '<strong>Edit</strong> task'); ?></h4>
                <?php else : ?>
                <h4 class="modal-title"
                    id="myModalLabel"><?php echo Yii::t('TasksModule.base', '<strong>Create</strong> new task'); ?></h4>
                <?php endif; ?>
        </div>

        <div class="modal-body">

            <?php echo $form->field($task, 'title')->textarea(['id' => 'taskTitleInput', 'class' => 'form-control autosize', 'rows' => '1', 'placeholder' => Yii::t('TasksModule.base', 'What is to do?')]); ?>

            <div class="row">
                <div class="col-md-8">

                    <?php echo $form->field($task, 'assignedUserGuids')->textInput(['id' => 'assignedUserGuids'])->hint(Html::a(Yii::t('TasksModule.base', 'Assign myself'), null, ['id' => 'ancSelfAssign', 'class' => 'pull-right'])); ?>

                    <?php
                    // attach mention widget to it
                    echo humhub\modules\user\widgets\UserPicker::widget(array(
                        'model' => $task,
                        'inputId' => 'assignedUserGuids',
                        'attribute' => 'assignedUserGuids',
                        'userSearchUrl' => $this->context->contentContainer->createUrl('/space/membership/search', array('keyword' => '-keywordPlaceholder-')),
                        'maxUsers' => 10,
                        'placeholderText' => Yii::t('TasksModule.base', 'Assign users'),
                    ));
                    ?>

                    <?php echo $form->field($task, 'status')->dropDownList(Task::getStatusTexts()); ?>


                </div>
                <div class="col-md-4">

                    <div class="form-group">
                        <?php echo $form->field($task, 'deadline')->widget(yii\jui\DatePicker::className(), ['dateFormat' => Yii::$app->params['formatter']['defaultDateFormat'], 'clientOptions' => [], 'options' => ['class' => 'form-control', 'placeholder' => Yii::t('TasksModule.base', 'Deadline')]]); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $form->field($task, 'duration_days')->textInput(['class' => 'form-control', 'placeholder' => Yii::t('TasksModule.base', 'Duration (days)')])->hint(Yii::t('TasksModule.base', 'The number of planned days.')); ?>
                    </div>


                </div>
            </div>
            <br>
        </div>
        <div class="modal-footer">

            <?php
            echo \humhub\widgets\AjaxButton::widget([
                'label' => Yii::t('TasksModule.base', 'Save'),
                'ajaxOptions' => [
                    'type' => 'POST',
                    'dataType' => 'json',
                    'beforeSend' => new yii\web\JsExpression('function(){ setModalLoader(); }'),
                    'success' => new yii\web\JsExpression('function(json) { handleTaskEditSubmit(json) }'),
                    'url' => $task->content->container->createUrl('/tasks/task/edit', ['id' => $task->id]),
                ],
                'htmlOptions' => [
                    'class' => 'btn btn-success pull-left'
                ]
            ]);
            ?>

            <?php
            echo \humhub\widgets\AjaxButton::widget([
                'label' => Yii::t('TasksModule.base', 'Delete'),
                'ajaxOptions' => [
                    'type' => 'POST',
                    'dataType' => 'json',
                    'beforeSend' => new yii\web\JsExpression('function(){ setModalLoader(); }'),
                    'success' => new yii\web\JsExpression('function(json) { handleTaskDeleteSubmit(json) }'),
                    'url' => $task->content->container->createUrl('/tasks/task/delete', ['id' => $task->id]),
                ],
                'htmlOptions' => [
                    'class' => 'btn btn-danger pull-right'
                ]
            ]);
            ?>            

            <?php echo \humhub\widgets\LoaderWidget::widget(['id' => 'default-loader', 'cssClass' => 'loader-modal hidden']); ?>                    

        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>


<script type="text/javascript">

    $('.autosize').autosize();

    /**
     * Focus on title field
     */
    $(document).ready(function () {
        var myInterval = setInterval(function () {
            $('#taskTitleInput').focus();
            clearInterval(myInterval);
        }, 100);
    });

    /**
     * Add current filters as hidden field to form - required for response handling
     */
    $('#filterEditSubmit').remove();
    $('<input />').attr('type', 'hidden')
            .attr('name', "filters")
            .attr('id', "filterEditSubmit")
            .attr('value', JSON.stringify($('#tasksList').data('filters')))
            .prependTo('#tasksEditForm');


    /**
     * Anchor self assign
     */
    $('#ancSelfAssign').on('click', function () {

        guid = "<?= Yii::$app->user->getIdentity()->guid; ?>";
        imageUrl = "<?= Yii::$app->user->getIdentity()->getProfileImage()->getUrl(); ?>";
        name = "<?= Html::encode(Yii::$app->user->getIdentity()->displayName); ?>";
        id = "assignedUserGuids";
        if (!$('#assignedUserGuids_invite_tags').find('li#assignedUserGuids_<?= Yii::$app->user->getIdentity()->guid; ?>').length) {
            $.fn.userpicker.addUserTag(guid, imageUrl, name, id);
        }
    });



</script>