/*
 * jQuery File Upload Plugin JS Example 6.7
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global jQuery, window, document */

jQuery(function () {
    'use strict';
		
    // Initialize the jQuery File Upload widget:
    jQuery('#fileupload').fileupload({
    	
			//acceptFileTypes: /(\.|\/)(gif|jpe?g|png)jQuery/i,
			acceptFileTypes:  /(\.|\/)(gif|jpe?g|png)$/i,
			maxFileSize: 10000000, // 10MB
			maxNumberOfFiles: 120,
			
	});

    // Enable iframe cross-domain access via redirect option:
    jQuery('#fileupload').fileupload(
        'option',
        'redirect',
        /*window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )*/
        'https://vlastnykalendar.sk/calendar/result.html?%s'
    );

/*    if (window.location.hostname === 'blueimp.github.com') {
        // Demo settings:
        jQuery('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)jQuery/i,
            process: [
                {
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)jQuery/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                },
                {
                    action: 'save'
                }
            ]
        }); 
        // Upload server status check for browsers with CORS support:
        if (jQuery.support.cors) {
            jQuery.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                jQuery('<span class="alert alert-error"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }*/
//    } else {
	
        // Load existing files:
        jQuery('#fileupload').each(function () {
            var that = this;
            jQuery.getJSON(this.action, function (result) {
                if (result && result.length) {
                    jQuery(that).fileupload('option', 'done').call(that, null, {result: result});
                }
            });
        });
				
//    }

});
