jQuery(function () {

    // ULOZENIE NA POZICIU HOVER
    jQuery(".upl-images li").hover(function () {
        jQuery(this).find(".addImage").animate({opacity: "show"}, "fast");
    }, function () {
        jQuery(this).find(".addImage").animate({opacity: "hide"}, "fast");
    });

    jQuery('div#obal_output').text(jQuery('textarea#obal_input').val());

    // 50 chars
    jQuery('#customTextInput').keyup(function () {
        countChar(this, 50, '#charNum');
        jQuery('div#customText').html(jQuery('#customTextInput').val());
    });

    if (jQuery('#customTextInput').val() != '') {
        jQuery('#customTextInput').trigger('keyup');
    }

    var month = jQuery('h2.currentMonth span').html();
    jQuery('div.wrapper-inside').addClass(month);

    var monthAdmin = jQuery('div.admin-form h2.currentMonth span').html();
    if (monthAdmin != '') {
        jQuery('div.admin-form div.wrapper-inside').addClass(monthAdmin);
    }

    /* ============ add coupon field ============ */

    jQuery('#add_coupon').click(function () {
        var id = parseInt(jQuery("#next_coupon_id").val()) + 1;

        var html = "<div id='coupon_div_" + id + "'><div class='control-group'><div class='controls'><input type='text' name='coupons_" + id + "' placeholder='Sem zadajte zľavový kupón' id='coupons_" + id + "' /></div></div></div>";
        jQuery('#coupons_field').append(html);
        jQuery('#next_coupon_id').val(id);
    });

    jQuery('#remove_coupon').on('click', function () {
        var last_id = parseInt(jQuery("#next_coupon_id").val());

        if (last_id > 1) {
            jQuery('#coupon_div_' + last_id).remove();
            jQuery('#next_coupon_id').val(last_id - 1);
        }
    });

    /* ============ new order view price generator ============ */

    jQuery('#deliveryCheck').on("change", function () {
        var deliveryCheck = jQuery(this);
        if (deliveryCheck.is(':checked')) {
            jQuery("span.deliveryResult").html('ÁNO');
            jQuery(".delivery-form").css("display", "block");
            jQuery("#shipping_zip, #shipping_city, #shipping_address, #shipping_name, #shipping_phone").addClass("required")
        }
        else {
            jQuery("span.deliveryResult").html('NIE');
            jQuery(".delivery-form").css("display", "none");
            jQuery("#shipping_zip, #shipping_city, #shipping_address, #shipping_name, #shipping_phone").removeClass("required");
        }
    });

    function countChar(val, limit, outElement) {
        var len = val.value.length;
        if (len > limit) {
            val.value = val.value.substring(0, limit);
        } else {
            jQuery(outElement).text(limit - len);
        }
    };

    jQuery("#adminTable", "body").on({
        'click': function (event) {
            event.preventDefault();
            var content = jQuery(this).find("span");
            if (content.text() === 'Info') {
                content.text("Info");
            } else {
                content.text("Info");
            }
            jQuery(this).closest("tr.item").nextUntil("tr.item").toggle();
        }
    }, "a.stats", null);

}); 