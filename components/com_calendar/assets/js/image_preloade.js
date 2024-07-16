jQuery(function($) {

    var calendarType = $('input[name="cal_type"]').val();

    var preloaderTopOffset = [];

    switch(calendarType) {
        case 'a':
            preloaderTopOffset[1] = 170;
            break;
        case 'b':
            preloaderTopOffset[1] = 65;
            preloaderTopOffset[2] = 65;
            break;
        case 'c':
            preloaderTopOffset[1] = 310;
            break;
        case 'd':
            preloaderTopOffset[1] = 150;
            preloaderTopOffset[2] = 60;
            preloaderTopOffset[3] = 60;
            preloaderTopOffset[4] = 60;
            break;
        case 'e':
            preloaderTopOffset[1] = 60;
            preloaderTopOffset[2] = 60;
            preloaderTopOffset[3] = 60;
            preloaderTopOffset[4] = 150;
            break;
        case 'f':
            preloaderTopOffset[1] = 310;
            break;
        case 'g':
            preloaderTopOffset[1] = 150;
            preloaderTopOffset[2] = 60;
            preloaderTopOffset[3] = 60;
            preloaderTopOffset[4] = 60;
            break;
        case 'h':
            preloaderTopOffset[1] = 60;
            preloaderTopOffset[2] = 60;
            preloaderTopOffset[3] = 60;
            preloaderTopOffset[4] = 150;
            break;
        case 'i':
            preloaderTopOffset[1] = 140;
            break;
        case 'j':
            preloaderTopOffset[1] = 160;
            break;
        case 'k':
            preloaderTopOffset[1] = 85;
            preloaderTopOffset[2] = 85;
            break;
        case 'l':
            preloaderTopOffset[1] = 130;
            break;
        case 'm':
            preloaderTopOffset[1] = 140;
            break;
        case 'n':
            preloaderTopOffset[1] = 160;
            break;
        case 'o':
            preloaderTopOffset[1] = 90;
            preloaderTopOffset[2] = 90;
            preloaderTopOffset[3] = 90;
            break;
        case 'p':
            preloaderTopOffset[1] = 90;
            preloaderTopOffset[2] = 90;
            preloaderTopOffset[3] = 90;
            preloaderTopOffset[4] = 90;
            preloaderTopOffset[5] = 90;
            preloaderTopOffset[6] = 90;
            break;
        case 'q':
            preloaderTopOffset[1] = 90;
            preloaderTopOffset[2] = 90;
            preloaderTopOffset[3] = 90;
            break;
        case 'r':
            preloaderTopOffset[1] = 170;
            break;
        case 's':
            preloaderTopOffset[1] = 170;
            break;
        case 't':
            preloaderTopOffset[1] = 170;
            break;
        case 'u':
            preloaderTopOffset[1] = 170;
            break;
        case 'v':
            preloaderTopOffset[1] = 170;
            break;
        case 'w':
            preloaderTopOffset[1] = 100;
            preloaderTopOffset[2] = 100;
            preloaderTopOffset[3] = 100;
            preloaderTopOffset[4] = 100;
            preloaderTopOffset[5] = 100;
            break;
        default:
            preloaderTopOffset[1] = 50;
    }

    $.each($('input[name="cal_index[]"]'), function() {
        var positionId = $(this).val();
        var imagePath = $('input[name="cal_img'+positionId+'"]').val();
        if (imagePath !== '' && imagePath !== 'auto') {
            $('#image_loader_mover' + positionId).show();
            $('.cal-dynamic-holder .mover' + positionId).append('<img id="image_loader_mover'+positionId+'" src="/components/com_calendar/assets/img/calendar_image_loader.gif" style="display:block; margin-left: auto; margin-right: auto; display: block; padding-top: '+preloaderTopOffset[positionId]+'px;"/>');
            $('.cal-dynamic-holder .mover' + positionId + ' img').on('load', function() {
                if ($(this).attr('id') != 'image_loader_mover' + positionId) {
                    // $('#image_loader_mover' + positionId).hide();
                }
            });
        }
    });
});