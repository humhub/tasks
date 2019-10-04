humhub.module('task.search', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var Filter = require('ui.filter').Filter;
    var object = require('util').object;
    var client = require('client');
    var loader = require('ui.loader');
    var additions = require('ui.additions');


    var TaskFilter = Filter.extend();

    TaskFilter.prototype.getDefaultOptions = function () {
        return {
            'delay': 200
        };
    };

    TaskFilter.prototype.init = function () {

        additions.observe($('#filter-tasks-list'));

        var that = this;
        this.on('afterChange', function() {
            that.loadUpdate();
        });

        $('#filter-tasks-list').on('click', '.pagination-container a', function (evt) {
            evt.preventDefault();

            that.loadUpdate($(this).attr('href'), {});
        });

        $('#filter-tasks-list').on('click', '[data-key]', function (evt) {
            if(!$(evt.target).closest('a').length) {
                client.pjax.redirect($(this).find('[data-task-url]').attr('data-task-url'));
            }
        });

        $('#task-export-button a').on('click', function(evt) {
            evt.preventDefault();
            var $this = $(this);
            if(!$this.data('originalUrl')) {
                $this.data('originalUrl', $this.attr('href'));
            }

            $params = that.buildRequestFilterData().data;

            if(that.options.containerGuid) {
                $params['cguid'] = that.options.containerGuid;
            }

            window.location = $this.data('originalUrl') + '&' +$.param($params);
        });
    };

    TaskFilter.prototype.loadUpdate = function (url, data) {
        var that = this;
        url = url || this.options.filterUrl;
        data = data || this.buildRequestFilterData();
        data.beforeSend = function(xhr) {
            that.currentXhr = xhr;
        };

        if (this.currentXhr) {
            this.currentXhr.abort();
        }

        that.loader();
        client.get(url, data).then(function(response) {
            if(response.result) {
                $('#filter-tasks-list').html(response.result);
            }
        }).catch(function(e) {
            if(e.errorThrown !== 'abort') {
                module.log.error(e, true);
            }
        }).finally(function() {
            that.loader(false);
        });
    };

    TaskFilter.prototype.buildRequestFilterData = function(key) {
        var that = this;
        var data = {};
        $.each(this.getFilterMap(), function(key, value) {
            data[that.buildRequestDataKey(key)] = value;
        });

        return {data: data};
    };

    TaskFilter.prototype.buildRequestDataKey = function(key) {
        return 'TaskFilter['+key+']';
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
        var $node = $('#filter-tasks-list');

        if (show === false) {
            loader.reset($node);
        } else {
            loader.set($node,);
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