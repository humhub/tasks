/*
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
humhub.module('task', function (module, require, $) {

    var modal = require('ui.modal');
    var client = require('client');
    var Widget = require('ui.widget.Widget');
    var object = require('util.object');
    var event = require('event');
    var action = require('action');
    var loader = require('ui.loader');
    var taskList = require('task.list');

    var Form = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(Form, Widget);

    Form.prototype.init = function() {
        this.initTimeInput();
        this.initScheduling();
        this.initAddTaskItem();
        this.initTaskListSelector();
        this.initSubmitAction();
    };

    Form.prototype.initTimeInput = function(evt) {
        var $timeFields = modal.global.$.find('.timeField');
        var $timeInputs =  $timeFields.find('.form-control');
        $timeInputs.each(function() {
            var $this = $(this);
            if($this.prop('disabled')) {
                $this.data('oldVal', $this.val()).val('');
            }
        });
    };

    Form.prototype.initScheduling = function(evt) {
        var $schedulingTab = modal.global.$.find('.tab-scheduling');
        var $checkBox = modal.global.$.find('#task-scheduling');
        var $calMode = modal.global.$.find('.field-task-cal_mode');
        if($checkBox.prop('checked')) {
            $schedulingTab.removeClass('d-none');
            $calMode.removeClass('d-none');
        } else {
            $schedulingTab.addClass('d-none');
            $calMode.addClass('d-none');
        }

        var $startInput  = $('#taskform-start_date');
        var $endInput= $('#taskform-end_date');

        $endInput.on('change', function() {
            if(!$startInput.val()) {
                $startInput.val($endInput.val());
            }
        });

        $startInput.on('change', function() {
            if(!$endInput.val()) {
                $endInput.val($startInput.val());
            }
        });
    };

    Form.prototype.toggleScheduling = function(evt) {
        var $schedulingTab = modal.global.$.find('.tab-scheduling');
        var $calMode = modal.global.$.find('.field-task-cal_mode');
        if (evt.$trigger.prop('checked')) {
            $schedulingTab.removeClass('d-none');
            $calMode.removeClass('d-none')
        } else {
            $schedulingTab.addClass('d-none');
            $calMode.addClass('d-none');
        }
    };

    Form.prototype.toggleDateTime = function(evt) {
        var $timeFields = modal.global.$.find('.timeField');
        var $timeInputs =  $timeFields.find('.form-control');
        if (evt.$trigger.prop('checked')) {
            $timeInputs.prop('disabled', true);
            $timeInputs.each(function() {
                $(this).data('oldVal', $(this).val()).val('');
            });
            $timeFields.css('opacity', '0.2');
        } else {
            $timeInputs.each(function() {
                $this = $(this);
                if($this.data('oldVal')) {
                    $this.val($this.data('oldVal'));
                }
            });
            $timeInputs.prop('disabled', false);
            $timeFields.css('opacity', '1.0');
        }
    };

    Form.prototype.removeTaskItem = function (evt) {
        evt.$trigger.closest('.mb-3').remove();
    };

    Form.prototype.initAddTaskItem = function () {
        var $this = this.$;
        $(document).on('keypress', 'input[name="TaskForm[newItems][]"]', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                if ($(this).data('task-item-added')) {
                    $(this).closest('.mb-3').next().find('input').focus();
                } else {
                    $this.find('[data-action-click=addTaskItem]').trigger('click');
                    $(this).data('task-item-added', true);
                }
            }
        });
    }

    Form.prototype.addTaskItem = function (evt) {
        var $this = evt.$trigger;
        $this.prev('input').tooltip({
            html: true,
            container: 'body'
        });

        var $newInputGroup = $this.closest('.mb-3').clone(false);
        var $input = $newInputGroup.find('input');

        $input.val('');
        $newInputGroup.addClass('d-none');
        $this.closest('.mb-3').after($newInputGroup);
        $this.children('span').removeClass('fa-plus').addClass('fa-trash');
        $this.off('click.humhub-action').on('click', function () {
            $this.closest('.mb-3').remove();
        });
        $this.removeAttr('data-action-click');
        $newInputGroup.removeClass('d-none');
        $newInputGroup.find('input').focus();
    };

    Form.prototype.initTaskListSelector = function () {
        this.$.find('[data-ui-select2][data-ui-select2-placeholder]').on('select2:open', function(e) {
            $('.select2-container input').attr('placeholder', $(e.target).data('ui-select2-placeholder'));
        });
    }

    Form.prototype.initSubmitAction = function () {
        modal.global.$.one('submitted', onTaskFormSubmitted);
    }

    var onTaskFormSubmitted = function (evt, response) {
        if (response.reloadTask) {
            modal.global.close(true);
            var task = taskList.getTaskById(response.reloadTask);
            if (task) {
                task.reload();
            }
        } else if (response.reloadLists) {
            modal.global.close(true);
            response.reloadLists.forEach(function (listId) {
                taskList.reloadList(listId)
            });
        } else if (response.reloadWall) {
            modal.global.close(true);
            event.trigger('humhub:content:newEntry', response.content, this);
            event.trigger('humhub:content:afterSubmit', response.content, this);
        } else {
            modal.global.$.one('submitted', onTaskFormSubmitted);
        }
    };

    var deleteTask = function(evt) {
         var widget = Widget.closest(evt.$trigger);

        widget.$.addClass('d-none');

        client.post(evt).then(function() {
            // in case the modal delete was clicked
            modal.global.close();
            if(widget) {
                widget.$.remove()
            }

            event.trigger('task.afterDelete')
        }).catch(function(e) {
            widget.$.removeClass('d-none');
            module.log.error(e, true);
        });
     };

    var deleteTaskFromContext = function(evt) {
        var widget = Widget.closest(evt.$trigger);
        widget.$.addClass('d-none');

        client.post(evt).then(function() {
            event.trigger('task.afterDelete');
            $('#task-space-menu').find('a:first').click();
            module.log.success(module.text('success.delete'));
        }).catch(function(e) {
            widget.$.removeClass('d-none');
            module.log.error(e, true);
        });
     };

    /**
     * @param evt
     */
    var extensionrequest = function(evt) {
        evt.block = action.BLOCK_MANUAL;
        client.post(evt).then(function(response) {
            if(response.success) {
                var dropdownLink = Widget.closest(evt.$trigger);
                dropdownLink.reload().then(function() {
                    dropdownLink.addClass('d-none');
                    module.log.success('request sent');
                });
            } else {
                module.log.error(e, true);
                evt.finish();
            }
        }).catch(function(e) {
            module.log.error(e, true);
            evt.finish();
        });
    };

    var changeState = function(evt) {
        evt.block = action.BLOCK_MANUAL;
        var widget = Widget.closest(evt.$target);
        if(!widget || !widget.changeState) {
            client.post(evt).then(function(response) {
                if(response.success) {
                    client.reload();
                } else {
                    module.log.error(e, true);
                }
            }).catch(function(e) {
                module.log.error(e, true);
                evt.finish();
            });
        } else {
            widget.changeState(evt);
        }
    };

    var init = function() {
        $(document).on('click', '.task-change-state-button a', function() {
           loader.initLoaderButton($('.task-change-state-button').children().first()[0]);
        });

        event.on('humhub:content:afterMove.tasks', function(evt, resp) {
            if($('#task-space-menu').length) {
                var $task = $('[data-content-id="'+resp.id+'"]');
                if($task.length) {
                    $task.remove();
                } else {
                    $('#task-space-menu').find('a:first').click();
                }
            }
        })

    };

    module.export({
        init: init,
        Form: Form,
        deleteTask: deleteTask,
        deleteTaskFromContext: deleteTaskFromContext,
        changeState: changeState,
        extensionrequest:extensionrequest
    });
})
;
