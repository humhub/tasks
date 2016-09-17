
$(document).ready(function () {

    $('input[type=radio][name=tasksTimeFilter]').change(function () {
        $('#tasksList').data('filters').time = this.value;
        reloadTasks();
    });

    $('input[type=checkbox][name="tasksStatusFilter[]"]').change(function () {
        $('#tasksList').data('filters').status = [];
        $('input[type=checkbox][name="tasksStatusFilter[]"]').each(function () {
            if (this.checked) {
                $('#tasksList').data('filters').status.push(this.value);
            }
        });
        reloadTasks();
    });


    $('input[type=checkbox][name=tasksShowFromOtherSpaces]').change(function () {
        $('#tasksList').data('filters').showFromOtherSpaces = this.checked;
        reloadTasks();

    });

    $('#ancShowMyTasks').on('click', function () {
        if ($('#tf_userFilter_invite_tags').find('li#tf_userFilter_' + tasksCurrentUserGuid).size() == 0) {
            $.fn.userpicker.addUserTag(tasksCurrentUserGuid, tasksCurrentUserImage, tasksCurrentUserDisplayName, 'tf_userFilter');
        }
        return false;
    });

    $('input[type=checkbox][name=userFilterUnassigned]').change(function () {
        if (this.checked) {
            $('#tf_userFilter_invite_tags').find('li').each(function () {
                if ($(this).attr('id') != 'tf_userFilter_tag_input') {
                    $(this).remove();
                }
            });
            $('#tasksList').data('filters').showUnassigned = true;
        } else {
            $('#tasksList').data('filters').showUnassigned = false;
        }
        reloadTasks();
    });


});






/**
 * Lookup Userpicker changes
 */
$(document).ready(function () {
    if ($('#tf_userFilter_invite_tags').length) {
        var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
        var myObserver = new MutationObserver(onUserPickerChange);
        var obsConfig = {childList: true, characterData: false, attributes: false, subtree: false};

        myObserver.observe($('#tf_userFilter_invite_tags')[0], obsConfig);
    }

});

function onUserPickerChange(mutationRecords) {

    $('#tasksList').data('filters').user = [];
    $('#tf_userFilter_invite_tags').find('li').each(function () {
        if ($(this).attr('id') != 'tf_userFilter_tag_input') {
            $('#tasksList').data('filters').user.push($(this).attr('id').replace('tf_userFilter_', ''));
        }
    });

    if ($('#tasksList').data('filters').user.length != 0) {
        $('input[type=checkbox][name=userFilterUnassigned]').prop("checked", false);
        $('#tasksList').data('filters').showUnassigned = false;
    }

    reloadTasks();
}