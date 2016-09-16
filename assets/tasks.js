var taskStatusActive = 1;
var taskStatusCompleted = 5;

$(document).ready(function () {
    $('#tasksLoader').removeClass('hidden');
    $('#tasksLoader').hide();
});

/**
 * Handles check item
 */
$('body').on('click', '.task-status-check', function () {
    newStatus = taskStatusCompleted;
    if ($(this).hasClass('completed-check')) {
        newStatus = taskStatusActive;
    }

    $task = $(this).closest('.task');
    $taskList = $task.closest('#tasksList');

    $.ajax({
        url: tasksStatusUpdateUrl,
        dataType: 'json',
        data: {
            'taskId': $task.data('task-id'),
            'status': newStatus,
            'filters': JSON.stringify($taskList.data('filters')),
        },
        beforeSend: function (xhr) {
            $task.fadeOut();
        },
        success: function (json) {
            $task.replaceWith(json.output);
            $task.fadeIn();
            resortTasks();
        }
    });
});

/**
 * Reloads the #tasksList based on current filters
 */
$(document).ready(function () {
    resortTasks();
});
var currentTasksRequests = null;
function reloadTasks() {

    currentTasksRequests = $.ajax({
        type: 'POST',
        url: tasksReloadUrl,
        beforeSend: function () {
            if (currentTasksRequests != null) {
                currentTasksRequests.abort();
            }
            $('#noTasksFoundMessage').hide();
            $('#tasksList').empty();
            $('#tasksLoader').show();
        },
        data: {
            filters: JSON.stringify($('#tasksList').data('filters')),
        },
        success: function (data) {
            $('#tasksLoader').hide();
            $('#tasksList').html(data);
            resortTasks();
        }
    });
}


/**
 * Resorts the task list
 * this is executed on page load and after each reload.
 * 
 */
function resortTasks() {

    var $wrapper = $('#tasksList');

    if (!$wrapper.length) {
        return;
    }

    $wrapper.find('div.task').sort(function (a, b) {
        if ($(a).data('task-start-date') == '' && $(b).data('task-start-date') != '') {
            return 1;
        } else if ($(b).data('task-start-date') == '' && $(a).data('task-start-date') != '') {
            return -1;
        } else if ($(a).data('task-start-date') !== $(b).data('task-start-date')) {
            return ($(a).data('task-start-date') < $(b).data('task-start-date')) ? -1 : 1;
        } else if ($(a).data('task-status-id') !== $(b).data('task-status-id')) {
            return ($(a).data('task-status-id') < $(b).data('task-status-id')) ? -1 : 1;
        } else if ($(a).data('task-title') !== $(b).data('task-title')) {
            return ($(a).data('task-title') < $(b).data('task-title')) ? -1 : 1;
        }

        return 0;
    }).appendTo($wrapper);

    $('#tasksList').find('.pagination-container').appendTo($('#tasksList'));
    $('#tasksList').find('.assigned-space').removeClass('hidden');

    if ($('#tasksList').data('filters').showFromOtherSpaces) {
        $('#tasksList').find('.assigned-space').show();
    } else {
        $('#tasksList').find('.assigned-space').hide();
    }

    $('#noTasksFoundMessage').removeClass('hidden');
    if ($('#tasksList').find('div.task').size() == 0) {
        $('#noTasksFoundMessage').show();
    } else {
        $('#noTasksFoundMessage').hide();
    }
}


/**
 * Add task to list 
 *  
 * json should contain
 *      id
 *      output
 *  
 * @param {type} json
 * @returns {undefined}
 */
function addTaskToList(json) {
    if ($('.task[data-task-id="' + json.id + '"]').length) {
        $('.task[data-task-id="' + json.id + '"]').replaceWith(json.output);
    } else {
        $('#tasksList').append(json.output);
    }
    resortTasks();
}

/**
 * Called after edit a task
 * 
 * @param {type} json
 * @returns {undefined}
 */
function handleTaskEditSubmit(json) {
    if (!json.success) {
        $("#globalModal").html(json.output);
    } else {
        addTaskToList(json.task);
        $('#globalModal').modal('toggle');
    }
}

function handleTaskDeleteSubmit(json) {
    $('#globalModal').modal('toggle');
    $('#tasksList').find('.task[data-task-id=' + json.id + ']').remove();
}