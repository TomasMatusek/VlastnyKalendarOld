jQuery(function($) {

    var Pdf = {

        state: {
            'user_id': 0,
            'order_id': 0,
            'sequence': [],
            'index': 0
        },

        zip: {
            'calendar_ids': 0,
            'index': 0
        },

        init: function(user_id, order_id, sequence) {
            console.log('PDF process init. User: ' + user_id + ', Order: ' + order_id + ', Sequence: ');
            console.log(sequence);
            Pdf.state.user_id = user_id;
            Pdf.state.order_id = order_id;
            Pdf.state.sequence = sequence;
            Pdf.zip.calendar_ids = Pdf.getUniquerIdsFromSequence();
        },

        generateFiles: function() {

            if (Pdf.state.index === Pdf.state.sequence.length) {
                Pdf.state.index = 0;
                Pdf.generateZip();
                return;
            }

            AdminActions.changeGenerateZipStatus('Processing PDF pages for calendar '
                + Pdf.state.sequence[Pdf.state.index]['id'] + ', month: '
                + Pdf.state.sequence[Pdf.state.index]['month'] + ', series: '
                + Pdf.state.sequence[Pdf.state.index]['series']
                + ' (' + Pdf.state.index + ' / ' + (Pdf.state.sequence.length - 1) + ')'
            );

            $.ajax({
                type: "POST",
                url: "/index.php?option=com_calendar&task=admin.generateSinglePdfPage&tmpl=component",
                data: {
                    'user_id': Pdf.state.user_id,
                    'order_id': Pdf.state.order_id,
                    'calendar_id': Pdf.state.sequence[Pdf.state.index]['id'],
                    'month': Pdf.state.sequence[Pdf.state.index]['month'],
                    'series': Pdf.state.sequence[Pdf.state.index]['series']
                },
                dataType: 'html',
                timeout: 110000
            }).error(function(jqXHR, textStatus, errorThrown) {
                console.log('PDF failed. ' + jqXHR + ', ' + textStatus + ', ' + errorThrown);
            }).success(function(data) {

                // succcess
                if (data == '') {
                    console.log('PDF page successfully generated! Calendar: '
                        + Pdf.state.sequence[Pdf.state.index]['id'] + ', Month: '
                        + Pdf.state.sequence[Pdf.state.index]['month'] + ', Series: '
                        + Pdf.state.sequence[Pdf.state.index]['series']
                    );
                }
                // error response
                else {
                    console.log('PDF page Failed! Response: ', data);
                }

                Pdf.state.index++;
                Pdf.generateFiles();
            });
        },

        generateZip: function() {

            if (Pdf.zip.index === Pdf.zip.calendar_ids.length) {
                Pdf.zip.index = 0;
                AdminActions.changeGenerateZipStatus('Finished');
                AdminActions.showGenerateZipButton();
                return;
            }

            AdminActions.changeGenerateZipStatus(
                'Creating ZIP file for calendar ' + Pdf.zip.calendar_ids[Pdf.zip.index] + ' (' + Pdf.zip.index + ' / ' + (Pdf.zip.calendar_ids.length - 1) + ')'
            );

            $.ajax({
                type: "POST",
                url: "/index.php?option=com_calendar&task=admin.generateSingleZipFile&tmpl=component",
                data: {
                    'order_id': Pdf.state.order_id,
                    'calendar_id': Pdf.zip.calendar_ids[Pdf.zip.index],
                },
                dataType: 'html',
                timeout: 110000
            }).error(function(jqXHR, textStatus, errorThrown) {
                console.log('ZIP failed. ' + jqXHR);
            }).success(function(response) {
                console.log('ZIP successfully generated! Calendar: ' + Pdf.zip.calendar_ids[Pdf.zip.index]);

                var data = $.parseJSON(response);

                Pdf.zip.index++;
                Pdf.generateZip();

                console.log(data['calendar_id'], data);

                var calendar_row = $('#' + data['calendar_id']);
                calendar_row.find('.zip_download').html('<a class="orange" href="/generatedPDF/'+data['file']+'">Download</a>');
                calendar_row.find('.zip_delete').html('<a class="orange" href="/index.php/component/calendar/?task=admin.deleteZipFile&order_id='+Pdf.state.order_id+'&calendar_id='+data['calendar_id']+'">Delete</a>');
            });
        },

        getUniquerIdsFromSequence: function() {
            var uniquer_calendar_ids = [];
            for (var i = 0; i < Pdf.state.sequence.length; i++) {
                if (uniquer_calendar_ids.indexOf(Pdf.state.sequence[i].id) === -1) {
                    uniquer_calendar_ids.push(Pdf.state.sequence[i].id);
                }
            }
            return uniquer_calendar_ids;
        }
    };

    var AdminActions = {

        months: ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'],

        series: {
            'o': {
                'cover': 1, 'january': 3, 'february': 2, 'march': 2, 'april': 2, 'may': 2, 'june': 3, 'july': 2, 'august': 2, 'september': 2, 'october': 2, 'november': 2, 'december': 3
            },
            'p': {
                'cover': 1, 'january': 3, 'february': 2, 'march': 2, 'april': 2, 'may': 2, 'june': 3, 'july': 2, 'august': 2, 'september': 2, 'october': 2, 'november': 2, 'december': 3
            },
            'q': {
                'cover': 1, 'january': 3, 'february': 2, 'march': 2, 'april': 2, 'may': 2, 'june': 3, 'july': 2, 'august': 2, 'september': 2, 'october': 2, 'november': 2, 'december': 3
            },
            'w': {
                'cover': 4, 'january': 4, 'february': 4, 'march': 4, 'april': 4, 'may': 4, 'june': 4, 'july': 4, 'august': 4, 'september': 4, 'october': 4, 'november': 5, 'december': 5
            },
            'default': {
                'cover': 1, 'january': 1, 'february': 1, 'march': 1, 'april': 1, 'may': 1, 'june': 1, 'july': 1, 'august': 1, 'september': 1, 'october': 1, 'november': 1, 'december': 1
            }
        },

        onGenerateZipClicked: function() {

            AdminActions.hideGenerateZipButton();
            AdminActions.changeGenerateZipStatus('Starting ...');

            var user_id = $('input[name="user_id"]').val();
            var order_id = $('input[name="order_id"]').val();
            var sequence = [];

            $('input[name="calendar_id[]"]').each(function(index, element) {
                var type = $(this).data('type');
                var cover = $(this).data('front-page');
                var calendar_id = $(this).val();

                var series = AdminActions.series[type] === undefined ? AdminActions.series['default'] : AdminActions.series[type];

                console.log('Series for ', type, ' calendar: ', series);

                if (cover) {
                    for (var k = 0; k < series['cover']; k++) {
                        sequence.push({
                            id: calendar_id,
                            month: 'cover',
                            series: k
                        });
                    }
                }

                // There are one page calendars
                if (!['r','s','t','u','v'].includes(type)) {
                    for (var j = 0; j < AdminActions.months.length; j++) {
                        for (var l = 0; l < series[AdminActions.months[j]]; l++) {
                            sequence.push({
                                id: calendar_id,
                                month: AdminActions.months[j],
                                series: l
                            });
                        }
                    }
                }
            });

            console.log('Sequence finished: ', sequence);

            Pdf.init(user_id, order_id, sequence);
            Pdf.generateFiles();
        },

        hideGenerateZipButton: function() {
            $('#generate-zip').hide();
            $('#generate-zip-status').show();
        },

        showGenerateZipButton: function() {
            $('#generate-zip').show();
            $('#generate-zip-status').hide();
        },

        changeGenerateZipStatus: function(text) {
            $('#generate-zip-status').html(text);
        }

    };

    /****************************************************
     * Event listeners
     ****************************************************/

    $('#generate-zip').on('click', AdminActions.onGenerateZipClicked);

    $( "input[name='invoice_date']" ).datepicker({ dateFormat: 'dd.mm.yy' });
});