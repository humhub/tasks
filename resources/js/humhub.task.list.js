/*
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
humhub.module('task.list', function (module, require, $) {

    var Widget = require('ui.widget').Widget;
    var object = require('util').object;
    var client = require('client');
    var loader = require('ui.loader');
    var modal = require('ui.modal');
    var additions = require('ui.additions');
    var event = require('event');
    var view = require('ui.view');

    var STATUS_PENDING = 1;
    var STATUS_IN_PROGRESS = 2;
    var STATUS_PENDING_REVIEW = 3;
    var STATUS_COMPLETED = 5;
    var STATUS_ALL = 4;

    var DELAY_DRAG_SMALL_DEVICES = 250;

    var Root = function (node, options) {
        Widget.call(this, node, options);
        additions.observe(this.$);
    };

    object.inherits(Root, Widget);

    Root.prototype.init = function () {
        var that = this;
        $('.task-list-ul:not(.task-list-unsorted)').sortable({
            delay: (view.isSmall()) ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.task-moving-handler',
            helper: 'clone',
            placeholder: "task-list-state-highlight",
            update: $.proxy(this.dropItem, this)
        });
    };

    Root.prototype.dropItem = function (event, ui) {
        var item = ui.item;
        var $taskList = item.find('[data-task-list-id]');
        var taskListId = $taskList.data('task-list-id');

        var data = {
            'ItemDrop[index]': item.index(),
            'ItemDrop[itemId]': taskListId
        };

        var that = this;
        client.post(this.options.dropListUrl, {data: data}).then(function(response) {
            if (!response.success) {
                that.getItemsRoot().sortable('cancel');
                module.log.error('', true);
            }
        }).catch(function(e) {
            module.log.error(e, true);
            that.getItemsRoot().sortable('cancel');
        });
    };

    Root.prototype.loadClosed = function(evt) {
        client.html(evt).then(function(response) {

        }).catch(function(e) {
            module.log.error(e, true);
        })
    };

    Root.prototype.collapseAll = function(evt) {
        var $visible = this.$.find('.task-list-items.ui-sortable:visible');
        if($visible.length) {
            $visible.siblings('.task-list-title-bar').click();
            $('#toggle-lists').find('i').removeClass('fa-toggle-on').addClass('fa-toggle-off');
        } else {
            this.$.find('.task-list-items.ui-sortable:hidden').siblings('.task-list-title-bar').click();
            $('#toggle-lists').find('i').removeClass('fa-toggle-off').addClass('fa-toggle-on');
        }
    };

    var TaskList = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TaskList, Widget);

    TaskList.prototype.init = function () {
        this.getItemsRoot().sortable({
            delay: (view.isSmall()) ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.task-moving-handler',
            helper: 'clone',
            connectWith: '.task-list-items',
            placeholder: "task-state-highlight",
            update: $.proxy(this.dropItem, this)
        });

        $('.task-list').on('mouseover', '.task-moving-handler', function () {
            $(this).closest('.task-list-task-title-bar').addClass('highlighter')
        });
        $('.task-list').on('mouseout', '.task-moving-handler', function () {
            $(this).closest('.task-list-task-title-bar').removeClass('highlighter')
        });

        var $taskTitleBar = this.$.find('.task-list-title-bar');
        $taskTitleBar.off('click').on('click', $.proxy(this.toggleItems, this))
            .hover($.proxy(this.mouseOver, this), $.proxy(this.mouseOut, this)).disableSelection();

        if(view.isSmall() || view.isMedium()) {
            // show elements
            this.$.find('.task-list-edit').show();
        }

        this.updated();
    };

    TaskList.prototype.mouseOver = function (event, ui) {
        this.$.find('.task-list-edit').show();
    };

    TaskList.prototype.mouseOut = function (event, ui) {
        if(!view.isSmall() || view.isMedium()) {
            this.$.find('.task-list-edit').hide();
        }
    };

    TaskList.prototype.toggleItems = function (evt) {
        var $target = $(evt.target);
        if(!$target.is('.task-list-title-bar') && !$target.closest('.toggleItems').length) {
            return;
        }

        var $items = this.getItemsRoot();

        var downIcon =  'fa-caret-up';
        var upIcon ='fa-caret-down';

        if($items.is(':visible')) {
            this.$.find('.toggleItems').removeClass(downIcon).addClass(upIcon);
        } else {
            this.$.find('.toggleItems').removeClass(upIcon).addClass(downIcon);
        }

        $items.add(this.getItemsCompletedRoot()).slideToggle(100,"linear");
    };


    TaskList.prototype.dropItem = function (event, ui) {
        var item = ui.item;
        var taskId = item.data('task-id');

        var itemWidget = Widget.instance(item);
        var targetList = itemWidget.parent();

        var data = {
            'ItemDrop[modelId]': targetList.options.taskListId,
            'ItemDrop[index]': item.index(),
            'ItemDrop[itemId]': taskId
        };

        var that = this;
        client.post(this.options.dropTaskUrl, {data: data}).then(function(response) {
            if (!response.success) {
                that.getItemsRoot().sortable('cancel');
                module.log.error('', true);
            }
            that.updated();
            targetList.updated();
        }).catch(function(e) {
            module.log.error(e, true);
            that.getItemsRoot().sortable('cancel');
        });
    };

    TaskList.prototype.getItemsRoot = function ($includeCompleted) {
        return this.$.find('.task-list-items:not(.tasks-completed)');
    };

    TaskList.prototype.getItemsCompletedRoot = function ($includeCompleted) {
        return this.$.find('.tasks-completed');
    };

    TaskList.prototype.showMoreCompleted = function (evt) {
        var that = this;
        var $tasksCompleted = this.$.find('.tasks-completed');
        var offset = $tasksCompleted.find('.task-list-item').length ;

        client.get(evt, {data: {offset: offset}}).then(function(response) {
            response.tasks.forEach(function(task) {
                that.appendCompleted($(task));
            });

            if(response.remainingCount) {
                that.$.find('.showMoreCompleted').text(response.showMoreMessage);
            } else {
                that.$.find('.task-list-task-completed-show-more').remove();
            }
        }).catch(function(e) {
            module.log.error(e, true);
        });
    };

    TaskList.prototype.loader = function (show) {
        if (show !== false) {
            loader.set(this.$.find('.task-list-title'), {
                size: '10px',
                'css': {padding: '0px', display: 'inline-block'}
            });
        } else {
            loader.reset(this.$.find('.task-list-title'));
        }
    };

    TaskList.prototype.reload = function () {
        var that = this;
        this.loader();
        client.html(this.options.reloadUrl).then(function (response) {
            that.$.html($(response.html).html());
            that.init();
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TaskList.prototype.prependPending = function (task) {
        var $task = task instanceof $ ? task : task.$;
        $task.hide();
        var $pendingContainer = this.getItemsRoot();
        $pendingContainer.prepend($task);
        $pendingContainer.show();
        $task.fadeIn();
    };

    TaskList.prototype.prependCompleted = function (task) {
        var $task = task instanceof $ ? task : task.$;
        $task.hide();
        var $completedContainer = this.getItemsCompletedRoot();
        $completedContainer.prepend($task);

        $completedContainer.show();
        $task.fadeIn();
    };

    TaskList.prototype.appendCompleted = function (task) {
        var $task = task instanceof $ ? task : task.$;
        $task.hide();
        var $completedContainer = this.getItemsCompletedRoot();
        var $lastCompleted = $completedContainer.find('.task-list-item').last();

        if($lastCompleted.length) {
            $task.insertAfter($lastCompleted);
        } else {
            $completedContainer.prepend($task)
        }

        $completedContainer.show();
        $task.fadeIn();
    };

    TaskList.prototype.deleteList = function(evt) {
        var that = this;
        this.loader();
        client.post(evt).then(function(response) {
            if(response.success) {
                that.remove();
                reloadList(); // reload unsorted
            } else {
                module.log.error(null, true);
            }
        }).catch(function(e) {
            module.log.error(e, true);
        }).finally(function() {
            that.loader(false);
        });
    };

    TaskList.prototype.updated = function() {
        var $itemRoot = this.getItemsRoot();
        if(!$itemRoot.find('.task-list-item, .task-list-empty').length) {
            var $empty = $('.task-list-empty:first').clone().show();
            $itemRoot.append($empty);
        } else if($itemRoot.find('.task-list-item').length) {
            $itemRoot.find('.task-list-empty').remove();
        }
    };

    TaskList.prototype.remove = function(evt) {
        var that = this;
        this.$.closest('.task-list-li').fadeOut('fast', function() {$(this).remove()});
    };

    var Task = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(Task, Widget);

    Task.prototype.init = function (evt) {
        this.$.find('.task-list-task-title-bar').off('click').on('click', $.proxy(this.toggleDetails, this));
        this.updated();
    };

    Task.prototype.changeState = function(evt) {
        var that = this;
        this.loader();
        client.post(evt).then(function(response) {
            if(response.success) {
                that.reload();
            } else {
                that.loader(false);
                module.log.error(null, true);
            }
        }).catch(function(e) {
            that.loader(false);
            module.log.error(e, true);
        });
    };

    Task.prototype.updated = function(evt) {
        var parent = this.parent();
        if(parent && parent.updated) {
            parent.updated();
        }
    };

    Task.prototype.toggleDetails = function (evt) {
        var that = this;

        var $target = $(evt.target);
        if((!$target.is('.task-list-task-title-bar') && !$target.closest('.toggleTaskDetails').length)) {
            return;
        }

        var $details = that.$.find('.task-list-task-details');
        if(!$details.length && !that.loadDetailsBlock) {
            // Prevent double click events
            that.loadDetailsBlock = true;
            that.loadDetails();
        } else if($details.length) {
            $details.slideToggle('fast');
        }
    };

    Task.prototype.loadDetails = function (evt) {
        var that = this;
        loader.append(that.$);

        client.html(this.options.loadDetailsUrl).then(function(response) {
            that.$.append(response.html);
            // currently the comments are hidden by default
            that.$.find('.comment-container').show();
        }).catch(function(e) {
            module.log.error(e, true);
        }).finally(function() {
            loader.remove(that.$);
        });
    };

    Task.prototype.loader = function (show) {
        if (show !== false) {
            loader.set(this.$.find('.task-list-item-title'), {
                size: '10px',
                'css': {padding: '0px', display: 'inline-block'}
            });
        } else {
            loader.reset(this.$.find('.task-list-item-title'));
        }
    };

    Task.prototype.reload = function (evt) {
        var that = this;
        this.loader();
        var detailsVisible = this.$.find('.task-list-task-details:visible').length;

        client.html(this.options.reloadUrl).then(function (response) {
            if (response.html) {
                that.$.fadeOut();
                var $newRoot = $(response.html).hide();
                that.$.replaceWith($newRoot);
                that.$ = $newRoot;

                if(!detailsVisible) {
                    that.$.find('.task-list-task-details').hide();
                }

                if (that.isCompleted()) {
                    that.parent().prependCompleted(that);
                } else if(that.$.closest('.tasks-completed')) {
                    that.parent().prependPending(that);
                }  else {
                    that.$.fadeIn();
                }
                that.init();
            }
        }).finally(function () {
            that.loader(false);
            that.updated();
        });
    };

    Task.prototype.isCompleted = function () {
        return this.isStatus(STATUS_COMPLETED);
    };

    Task.prototype.isStatus = function (status) {
        return this.$.is('[data-task-status="' + status + '"]')
    };

    var CompletedTaskListView = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(CompletedTaskListView, Widget);

    CompletedTaskListView.prototype.init = function() {
        var that = this;
        that.$.on('click', '.pagination-container a', function (evt) {
            evt.preventDefault();
            that.changePage($(this).attr('href'));
        });
    };


    CompletedTaskListView.prototype.changePage = function (url) {
        var that = this;
        url = url || this.$.attr('action');

        // Note: the additional empty objects are given due an bug in v1.2.1 fixed in v1.2.2
        client.html(url).then(function (response) {
            that.$.find('.closed-task-list-view').html(response.html);
        }).catch(function (err) {
            module.log.error(err, true);
        }).finally(function () {
            //that.loader(false);
        });

    };

    var CompletedTaskListViewItem = function (node, options) {
        Task.call(this, node, options);
    };

    object.inherits(CompletedTaskListViewItem, TaskList);

    CompletedTaskListViewItem.prototype.remove = function(evt) {
        var that = this;
        this.$.closest('li').fadeOut('fast', function() {$(this).remove()});
    };

    var create = function (evt) {
        modal.load(evt).then(function () {
            modal.global.$.one('hidden.bs.modal', function() {
                client.reload();
            });
        });
    };

    var edit = function (evt) {
        modal.load(evt).then(function () {
            modal.global.$.one('hidden.bs.modal', function() {
                Widget.closest(evt.$trigger).reload();
            });
        }).catch(function(e) {
            module.log.error(e,true);
        });
    };

    var editTask = function (evt) {
        modal.load(evt).then(function () {
            modal.global.$.one('submitted', onEditTaskSubmitted);
        });
    };

    var onEditTaskSubmitted = function (evt, response) {
        if(response.reloadTask) {
            modal.global.close(true);
            var task = getTaskById(response.reloadTask);
            if(task) {
                task.reload();
            }
        } else if (response.reloadLists) {
            modal.global.close(true);
            response.reloadLists.forEach(function (listId) {
                reloadList(listId)
            });
        } else {
            modal.global.$.one('submitted', onEditTaskSubmitted);
        }
    };

    var reloadList = function (id) {
        var list = getListById(id);
        if (list) {
            list.reload();
        } else {
            client.reload();
        }
    };

    var getListById = function (id) {
        var $node = id ? $('[data-task-list-id="' + id + '"]') : $('[data-task-list-unsored]');
        return Widget.instance($node);
    };

    var getTaskById = function (id) {
        var $node = $('[data-task-id="' + id + '"]');
        return $node.length ? Widget.instance($node) : null;
    };


    module.export({
        TaskList: TaskList,
        CompletedTaskListView: CompletedTaskListView,
        CompletedTaskListViewItem: CompletedTaskListViewItem,
        Task: Task,
        Root: Root,
        edit: edit,
        create: create,
        editTask: editTask
    });
})
;
