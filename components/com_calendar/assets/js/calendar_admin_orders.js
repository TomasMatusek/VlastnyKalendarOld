jQuery(function($){

    var baseURL = '/index.php/component/calendar/?task=';

    $('#calendars-orders-admin-view').on('click', '#select-all-orders', function() {
        $.each($('.order-checkbox'), function(key, element) {
            if (!$(this).attr('disabled')) {
                $(this).attr('checked','checked');
            }
        });
    });

    $('#calendars-orders-admin-view').on('click', '#delete-order-files', function() {

        var commands = [];
        $.each(jQuery('.order-checkbox:checked'), function(key, element) {
            var orderId = $(this).val();
            var calendarIds = $(this).data('ids').split(',');

            $.each(FTPCleaner.removeZipFiles(calendarIds, orderId), function(key, url) {
                commands.push(url);
            });

            commands.push(FTPCleaner.removeTempFiles(orderId));
            commands.push(FTPCleaner.removeCalendarImages(calendarIds, orderId));
        });

        FTPCleaner.executeCommands(commands);
    });

    var FTPCleaner = {

        removeTempFiles: function(orderId) {
            return {
                description: '[' + orderId + '][TEMP] Removing temp files for order: ' + orderId,
                url: baseURL + 'admin.clearTemporaryFiles&order_id=' + orderId
            };
        },

        removeCalendarImages: function (calendarIds, orderId) {
            var url =  'admin.removeCalendarBackupImages&order_id=' + orderId;
            $.each(calendarIds, function(key, value) {
                if (value.length > 0) {
                    url += '&id[]=' + value;
                }
            });
            return {
                description: '[' + orderId + '][IMAGE] Removing calendar images for order: ' + orderId + ', calendars: ' + calendarIds,
                url: baseURL + url
            };
        },

        removeZipFiles: function (calendarIds, orderId) {
            var urls = [];
            $.each(calendarIds, function(key, value) {
                if (value.length > 0) {
                    urls.push({
                        description: '[' + orderId + '][ZIP] Removing ZIP for order: ' + orderId + ', calendar: ' + value,
                        url: baseURL + 'admin.deleteZipFile&order_id=' + orderId + '&calendar_id=' + value
                    })
                }
            });
            return urls;
        },

        executeCommands: function(commands) {
            if (commands.length == 0) {
                FTPCleaner.showExecuteButton();
                return;
            }

            var commandURL = commands[0].url;
            var commandDescription = commands[0].description;
            FTPCleaner.changeStatusText(commandDescription);

            console.log(commandDescription, commandURL);

            $.ajax(commandURL)
            .done(function() {
                commands.shift();
                FTPCleaner.executeCommands(commands);
            })
            .fail(function() {
                FTPCleaner.changeStatusText("<bold>OPERATION FAILED!!!</bold> " + commandDescription + ", URL: " + commandURL);
                console.log('[FATAL ERROR]' + commandDescription, commandURL);
            });
        },

        changeStatusText: function(status) {
            $('#delete-orders-files-button-wrapper').html(status);
        },

        showExecuteButton: function() {
            $('#delete-orders-files-button-wrapper').html("<a role='button' data-popup-open='delete-order-files' id='delete-order-files' class='btn btn-default btn-filter'>Zmazať fotky a ZIP subory vybranych kalendarov</a>");
        }
    }





});