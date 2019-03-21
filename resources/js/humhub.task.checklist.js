humhub.module('task.checklist', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;
    var client = require('client');

    var Item = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(Item, Widget);

    Item.prototype.init = function () {

        var label = this.$.find('label');
        var checkbox = this.$.find('input[type="checkbox"]');

        if (checkbox.prop('checked')) {
            label.addClass("item-finished");
        } else {
            label.removeClass("item-finished");
        }
    };

    Item.prototype.index = function () {
        return this.$.index();
    };

    Item.prototype.loader = function () {
        // debugger;
    };

    // Item.prototype.confirm = function (submitEvent) {
    //     this.update(client.submit(submitEvent));
    // };

    Item.prototype.check = function () {
        var that = this;

        var checked = that.$.find('input[type="checkbox"]').prop('checked') ? 1 : 0;

        var data = {
            'CheckForm[checked]': checked
        };

        client.post(that.options.checkUrl, {data: data}).then(function (response) {
            if (response.success) {
                that.setData(response.item);
            } else {
                module.log.error(null, true);
            }
        }).catch(function (err) {
            module.log.error(err, true);
        });
    };

    Item.prototype.setData = function (itemData) {
        if(itemData.checked) {
            this.$.find('label').addClass("item-finished");
        } else {
            this.$.find('label').removeClass("item-finished");
        }

        if(itemData.statusChanged) {
            this.parent().reload();
        }

        this.options.sortOrder = itemData.sortOrder;
        this.$.attr('data-sort-order', itemData.sortOrder);
    };

    var ItemList = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(ItemList, Widget);

    ItemList.prototype.init = function () {
        var that = this;
        if (this.options.canResort && this.$.find('li[data-item-id]').length > 1 && this.$.find('.legacyFlag').length == 0) {
            this.$.imagesLoaded(function() {
                that.initSortableList();
            });
        }
    };

    ItemList.prototype.reload = function () {
        var parent = this.parent();
        if(parent && parent.reload) {
            parent.reload();
        } else {
            client.reload();
        }
    };

    ItemList.prototype.refresh = function() {
        //this.$.sortable('refresh');
    };


    ItemList.prototype.initSortableList = function (evt) {
        var that = this;
        this.$.sortable({
            create: function () {
                jQuery(this).height(jQuery(this).height());
            },
            revert: 50,
            update: function (evt, ui) {
                var item = Item.instance(ui.item);

                var data = {
                    'ItemDrop[taskId]': that.options.taskId,
                    'ItemDrop[itemId]': item.options.itemId,
                    'ItemDrop[index]': item.index()
                };

                item.loader();
                client.post(that.options.dropUrl, {data: data}).then(function (response) {
                    if (response.success) {
                        that.updateItems(response.items);
                    } else {
                        module.log.error(err, true);
                        that.cancelDrop();
                    }
                }).catch(function (err) {
                    module.log.error(err, true);
                    that.cancelDrop();
                }).finally(function () {
                    item.loader(false);
                });
            },
            stop: function () {
                // that.updateViewByItemOrder();
            }
        });
        this.$.disableSelection();
    };

    ItemList.prototype.updateItems = function (items) {
        $.each(items, function (itemId, item) {
            var itemInst = Item.instance($('[data-item-id="' + itemId + '"]'));
            itemInst.setData(item);
        });
    };

    module.export({
        ItemList: ItemList,
        Item: Item
    });
});