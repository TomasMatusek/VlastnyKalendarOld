jQuery(document).ready(function() {	
	jQuery(".addImage").click(function() {
		var img_path = jQuery(this).data("img");
		var position = jQuery("#position option:selected").val();
		jQuery("#cal_img" + position).attr("value", img_path);
	});

	jQuery('[data-popup-open]').on('click', function(e)  {
		var targeted_popup_class = jQuery(this).attr('data-popup-open');
		jQuery('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
		e.preventDefault();
	});

	jQuery('[data-popup-close]').on('click', function(e)  {
		var targeted_popup_class = jQuery(this).attr('data-popup-close');
		jQuery('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
		e.preventDefault();
	});

    jQuery('#calendar-save-month-btn').on('click', function(e)  {
        jQuery(this).hide();
        jQuery('#calendar-save-month-progress-btn').show();
    });
});