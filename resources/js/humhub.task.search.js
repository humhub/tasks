humhub.module('task.search', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;
    var client = require('client');
    var loader = require('ui.loader');


    var TaskFilter = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TaskFilter, Widget);

    TaskFilter.prototype.getDefaultOptions = function () {
        return {
            'delay': 200
        };
    };

    TaskFilter.prototype.init = function () {
        this.$titleFilter = this.$.find('#taskfilter-title');
        this.$entryContainer = $('#filter-tasks-list');
        var that = this;

        this.$titleFilter.on('keypress', function (evt) {
            if (evt.keyCode == 13) {
                evt.preventDefault();
            }
            if (that.title() !== that.lastTitleSearch) {
                if (that.request) {
                    clearTimeout(that.request);
                }

                that.request = setTimeout($.proxy(that.filterCall, that), that.options.delay);
            }
        });

        this.$.find('.checkbox').on('change', function () {
            that.filterCall();
        });

        this.$.find('.field-taskfilter-status').on('change', function () {
            that.filterCall();
        });

        this.$entryContainer.on('click', '.pagination-container a', function (evt) {
            evt.preventDefault();
            that.filterCall($(this).attr('href'));
        });
    };

    TaskFilter.prototype.filterCall = function (url) {
        var that = this;
        this.lastTitleSearch = this.title();
        this.loader();

        url = url || this.$.attr('action');

        // Note: the additional empty objects are given due an bug in v1.2.1 fixed in v1.2.2
        client.submit(this.$, {url: url}).then(function (response) {
            if (response.success) {
                that.$entryContainer.html(response.output);
            }
        }).catch(function (err) {
            module.log.error(err, true);
        }).finally(function () {
            that.loader(false);
        });

    };

    TaskFilter.prototype.loader = function (show) {
        var $node = $('#task-filter-loader');

        if (show === false) {
            loader.reset($node);
        } else {
            loader.set($node, {
                'position': 'left',
                'size': '8px',
                'css': {padding: '0px'}
            });
        }
    };

    TaskFilter.prototype.title = function () {
        return this.$titleFilter.val();
    };

    var TaskSearchListItem = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TaskSearchListItem, Widget);

    module.export({
        TaskFilter: TaskFilter,
        TaskSearchListItem: TaskSearchListItem
    });
});