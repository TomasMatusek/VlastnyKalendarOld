jQuery(function($) {

    var discount = 0;
    var payment_price_for_item = parseFloat($("#payment_price_for_item").val());

    /******************************
     * Events methods
     ******************************/

    var onDepoPupupClosed = function() {
        isDepoPlacePicked(
            function(response) {
                console.log(response);
                $('.depo-place-detail').show();
                $('#depo-place-not-picked').hide();
                $('#depo-place-picked-detail').html(
                    '<p>' + response['name'] + '</p>' +
                    '<p>' + response['street'] + '</p>' +
                    '<p>' + response['zip'] + ', ' + response['city'] + '</p>'
                );
            },
            function() {
                $('.depo-place-detail').show();
                $('#depo-place-picked-detail').hide();
                $('#depo-place-not-picked').show();
            }
        );
    };

    var onCompanyOrderClick = function() {
        var is_company = $('#is_company').is(':checked');

        if (is_company) {
            $('#tr_billing_ico').show();
            $('#tr_billing_dic').show();
            $('#tr_billing_icdph').show();
            $('#tr_billing_name td:first').html('Názov firmy');
            $('#tr_billing_name input').attr('placeholder', 'Názov firmy');
        } else {
            $('#tr_billing_ico').hide();
            $('#tr_billing_dic').hide();
            $('#tr_billing_icdph').hide();
            $('#tr_billing_name td:first').html('Meno a priezvisko');
            $('#tr_billing_name input').attr('placeholder', 'Meno a priezvisko');
        }
    };

    var onDifferentShippingAddressClick = function() {
        var different_shipping_address = $('#different_shipping_address').is(':checked');
        var comment_textarea_width = different_shipping_address ? '95%' : '92%';

        if (different_shipping_address) {
            showShippingPanel();
        } else {
            hideShippingPanel();
        }

        $('#comment').css('width', comment_textarea_width);
	};

    var onShippingMethodChange = function() {
		var shipping_method = $('select[name="transport_method"]').val();
        $('#depo-error-place-not-selected').hide();

        $('#tr_billing_address').show();
        $('#tr_billing_address_number').show();
        $('#tr_billing_city').show();
        $('#tr_billing_zip').show();
        $('#tr_different_shipping_address').show();

		// update payment methods based on selected shipping method
        if (shipping_method === "personal") {
            updatePaymentMethodSelectOptions(true, false, true);
            updateDefaultPaymentMethodBasedOnTransferMethod("cash");
            showDepoPlacePicker(false);
        } else if (shipping_method === "courier") {
            updatePaymentMethodSelectOptions(false, true, true);
            updateDefaultPaymentMethodBasedOnTransferMethod("card");
            showDepoPlacePicker(false);
        } else if (shipping_method === "depo") {
            updatePaymentMethodSelectOptions(false, true, true);
            updateDefaultPaymentMethodBasedOnTransferMethod("card");
            showDepoPlacePicker(true);
        } else if (shipping_method === "post-sk") {
            updatePaymentMethodSelectOptions(false, true, true);
            updateDefaultPaymentMethodBasedOnTransferMethod("card");
            showDepoPlacePicker(false);
        }  else if (shipping_method === "post-cz") {
            updatePaymentMethodSelectOptions(false, false, true);
            updateDefaultPaymentMethodBasedOnTransferMethod("card");
            showDepoPlacePicker(false);
        }  else if (shipping_method === "post-eu") {
            updatePaymentMethodSelectOptions(false, true, true);
            updateDefaultPaymentMethodBasedOnTransferMethod("card");
            showDepoPlacePicker(false);
        }

        // hide / show filed based on shippingmethod / payment method
        var payment_method = $('select[name="payment_method"] option:selected').val();
		if (payment_method === "cash") {
			hideShippingPanel();
			$('#tr_billing_address').hide();
            $('#tr_billing_address_number').hide();
            $('#tr_billing_city').hide();
            $('#tr_billing_zip').hide();
            $('#tr_different_shipping_address').hide();
            $('input[name="different_shipping_address"]').removeAttr('checked');
		} else {
            $('#tr_billing_address').show();
            $('#tr_billing_address_number').show();
            $('#tr_billing_city').show();
            $('#tr_billing_zip').show();
        } 

        // Show hide payment description
        if (payment_method === "card") {
            $("#payment-description").removeAttr("style");
        } else {
            $("#payment-description").css("display", "none");
        }

        updateFinalPrice();
	};

    var showDepoPlacePicker = function(display) {
        if (display) {
            $('.depo-place-picker').show();
            $('.depo-place-detail').show();
        } else {
            $('.depo-place-picker').hide();
            $('.depo-place-detail').hide();
        }
    };

    var onPaymentMethodChange = function() {
        updateFinalPrice();

        // hide / show filed based on shippingmethod / payment method
        var payment_method = $('select[name="payment_method"] option:selected').val();
		if (payment_method === "cash") {
			hideShippingPanel();
			$('#tr_billing_address').hide();
            $('#tr_billing_address_number').hide();
            $('#tr_billing_city').hide();
            $('#tr_billing_zip').hide();
            $('#tr_different_shipping_address').hide();
            $('input[name="different_shipping_address"]').removeAttr('checked');
		} else {
            $('#tr_billing_address').show();
            $('#tr_billing_address_number').show();
            $('#tr_billing_city').show();
            $('#tr_billing_zip').show();
        }

        // Show hide payment description
        if (payment_method === "card") {
            $("#payment-description").removeAttr("style");
        } else {
            $("#payment-description").css("display", "none");
        }
    };

    var onCalendarQuantityChange = function() {
    	var quantity = parseFloat($(this).val());
        var input = $(this);

    	if (isNaN(quantity)) {
    	    quantity = parseFloat(input.data('value')) == 0 ? 1 : parseFloat(input.data('value'));
            input.val(quantity);
        } else {
            quantity = quantity == 0 ? 1 : quantity;
            $(this).val(quantity);
        }

        if (quantity > 1000) {
            quantity = 1000;
            input.val(quantity)
        }

    	var calendar_id = input.data('calendar-id');
		var item_id = '#calendar_' + calendar_id;

        updateItemRowPrice(item_id, quantity, updateFinalPrice);

        $.ajax({
            type: 'POST',
            url: '/index.php?option=com_calendar&task=calendar.setCalendarQuantity',
            dataType: 'text',
            data: {
                'quantity': quantity,
				'calendar_id': calendar_id
            },
            success: function(data){
                updateShoppingCart();
                console.log('Request to update quantity success');
            },
			fail: function(data) {
                console.log('Request to update quantity failed', data);
			}
        });
	};

    var onDiscountCouponVerifyClick = function() {
        var coupon_code = $('input[id="special_code_input"]');
        if (coupon_code.val().length >= 6) {
            $.ajax({
                type: 'POST',
                url: '/index.php?option=com_calendar&task=calendar.verifyDiscountCoupon',
                dataType: 'text',
                data: {
                    'coupon_code': coupon_code.val()
                },
                success: function(response){
                    var data = $.parseJSON(response);
                    if (data['valid']) {
                        discount = parseFloat(data['discount']);
                        updateCouponInputState('verified');
                        updateFinalPrice();
                        return;
                    }
                    updateCouponInputState('invalid');
                },
                fail: function(data) {
                    console.log('Request to verify coupon failed', data);
                }
            });
        }
        else if (coupon_code.val().length > 0) {
            updateCouponInputState('invalid');
        }
        else {
            updateCouponInputState('default');
        }
    };

    var onDiscountCouponRemoveClick = function() {
        discount = 0;
        updateCouponInputState('default');
        updateFinalPrice();
    };

    var onInputDataChange = function() {
        // if single input changed validate only this input
        var inputId = $(this).attr('id');
        if (inputId != undefined) {
            return validateInput($('input[id="' + inputId + '"]'));
        }

        var error_counter = 0;

        error_counter += validateInput($('input[id="billing_name"]'));
        error_counter += validateInput($('input[id="billing_phone"]'));
        error_counter += validateInput($('input[id="billing_mail"]'));
        error_counter += validateInput($('input[id="billing_address"]'));
        error_counter += validateInput($('input[id="billing_address_number"]'));
        error_counter += validateInput($('input[id="billing_city"]'));
        error_counter += validateInput($('input[id="billing_zip"]'));

        error_counter += validateInput($('input[id="shipping_name"]'));
        error_counter += validateInput($('input[id="shipping_address"]'));
        error_counter += validateInput($('input[id="shipping_address_number"]'));
        error_counter += validateInput($('input[id="shipping_city"]'));
        error_counter += validateInput($('input[id="shipping_zip"]'));

        return error_counter;
    };

    var onOrderSubmit = function(event) {
        var error_count = onInputDataChange();
        if (error_count > 0) {
            event.preventDefault();
            return;
        }

        // Hide button to prevent double submit
        $('#order_submit').hide();
        $('#order_submit_in_progress').show();

        // Check if depo place is selected
        let shipping_method = $('select[name="transport_method"]').val();
        if (shipping_method === 'depo' && $('#depo-pickup-place-order-id').data('done') === 0) {
            event.preventDefault();
            isDepoPlacePicked(
                function(data) {
                    if (data['success']) {
                        $('#depo-pickup-place-order-id').data('done', 1);
                        console.log('test123');
                        $('#order_submit').click();
                    } else {
                        $('#depo-error-place-not-selected').show();
                        $('#order_submit').show();
                        $('#order_submit_in_progress').hide();
                    }
                },
                function() {
                    $('#depo-error-place-not-selected').show();
                    $('#order_submit').show();
                    $('#order_submit_in_progress').hide();
                }
            );
        }
    };

    var isDepoPlacePicked = function(onSuccess, onFailure) {
        $.ajax({
            type: 'GET',
            url: '/index.php?option=com_calendar&task=user.isDepoPlaceSelected',
            data: {
                'depo_number': $('#depo-pickup-place-order-id').val()
            },
            success: function(response){
                let data = $.parseJSON(response);
                if (data['success'] === 'true' || data['success'] === true) {
                    onSuccess(data);
                } else {
                    onFailure();
                }
            },
            fail: function() {
                onFailure();
            }
        });
    };

    var isEmpty = function(str) {
        return str.trim().length == 0;
    };

    /******************************
     * UI manipulation methods
     ******************************/

    var showShippingPanel = function() {
        $('#col-3').show();
        $('#col-2 .col-md-6').removeClass('col-md-6').addClass('col-md-4');
        $('#col-1 .col-md-6').removeClass('col-md-6').addClass('col-md-4');
    };

    var hideShippingPanel = function() {
        $('#col-3').hide();
        $('#col-2 .col-md-4').removeClass('col-md-4').addClass('col-md-6');
        $('#col-1 .col-md-4').removeClass('col-md-4').addClass('col-md-6');
    };

    var updateShoppingCart = function() {
        $.ajax({
            type: 'GET',
            url: '/index.php?option=com_calendar&task=calendar.getShoppingCartDataAsJSON',
            success: function(response){
                var data = $.parseJSON(response);
                $('.total_price').html('spolu ' + data['price'] + ' &euro;');
                $('.total_products').html(data['quantity'] + ' x kalendár');
                $('.cart-number').html(data['quantity']);
            },
            fail: function(data) {
                console.log('Request to update shopping cart failed', data);
            }
        });
    };

    var validateInput = function(input) {
        var is_required = input.data('required') === undefined || input.data('required') === 'true';

        if (input.attr('id') === 'billing_phone') {
            var regex = /^\+[0-9]{12}$/g;
            if (input.val() == null || input.val().trim().match(regex) == null) {
                input.addClass('highlight-red');
                return 1;
            }
        }

        if (input.is(':visible') && isEmpty(input.val()) && is_required) {
            input.addClass('highlight-red');
            return 1;
        } else {
            input.removeClass('highlight-red');
            return 0;
        }
    };

    var updateCouponInputState = function(state) {
        var coupon_input = $('input[id="special_code_input"]');
        var coupon_hidden = $('input[id="special_code_hidden"]');
        var verify_button = $('button[id="special_code_verify"]');
        var remove_button = $('button[id="special_code_remove"]');

        if (state == "verified") {
            coupon_input
                .removeClass('highlight-red')
                .addClass('disabled-input')
                .addClass('highlight-green')
                .attr('disabled','disabled');
            coupon_hidden
                .removeAttr('disabled')
                .val(coupon_input.val());
            verify_button.hide();
            remove_button.show();
        } else if (state == 'invalid') {
            coupon_input
                .removeClass('highlight-green')
                .removeClass('disabled-input')
                .addClass('highlight-red')
                .removeAttr('disabled');
            coupon_hidden.
                attr('disabled', 'disabled')
                .val('');
            verify_button.show();
            remove_button.hide();
        } else if (state == 'default') {
            coupon_input
                .removeClass('highlight-green')
                .removeClass('disabled-input')
                .removeClass('highlight-red')
                .removeAttr('disabled')
                .val('');
            coupon_hidden.
                attr('disabled', 'disabled')
                .val('');
            verify_button.show();
            remove_button.hide();
        }
    };

    var updateItemRowPrice = function(item_id, quantity, onComplete) {
    	var row = $(item_id);
        var item_price = $(row).find('.calendar_price span').text();
        var item_cover_price = $(row).find('.calendar_price_cover span').text();
        var price = Number((quantity * (parseFloat(item_price) + parseFloat(item_cover_price))).toFixed(2));
        $(row).find('.calendar_price_total').html(price + ' &euro;').data('value', price);
        onComplete();
	};

    var updateFinalPrice = function() {
        var payment_method_option_selected = $('select[name="payment_method"]').find('option:selected');
        var transport_method_option_selected = $('select[name="transport_method"]').find('option:selected');

        var price_payment_method = parseFloat(payment_method_option_selected.data('value'));
        var price_transport_method = parseFloat(transport_method_option_selected.data('value'));
        var price_calendars = 0;
        var price_calendars_after_discount = 0;
        var price_packing = 0;
        var price_final = 0;
        var quantity = 0;

        $('.calendar-row').each(function() {
            quantity += parseFloat($(this).find('input[name="quantity"]').val());
            price_calendars += parseFloat($(this).find('.calendar_price_total').data('value'));
        });

        if (quantity > 1 && transport_method_option_selected.val() != "personal") {
            price_packing = payment_price_for_item * (quantity - 1);
        }

        price_calendars_after_discount = price_calendars - ((price_calendars / 100) * discount);
        price_final = price_calendars_after_discount + price_packing + price_payment_method + price_transport_method;

        if (discount > 0) {
            $('#price-discount-info').css('display','inline').html('Zľava ' + discount + '%');
        } else {
            $('#price-discount-info').css('display','none').html('');
        }

        $('#price-calendars').html(Number(price_calendars).toFixed(2));
        $('#price-calendars-discount').html(Number(price_calendars_after_discount).toFixed(2));
        $('#price-shipping-and-packing').html(Number(price_packing + price_payment_method + price_transport_method).toFixed(2));
        $('#price-total').html(Number(price_final).toFixed(2));

        $('input[name="price_calendars"]').val(Number(price_calendars_after_discount).toFixed(2));
        $('input[name="price_shipping_and_packing"]').val(Number(price_packing + price_payment_method + price_transport_method).toFixed(2));
        $('input[name="price_total"]').val(Number(price_final).toFixed(2));
    };

    var updatePaymentMethodSelectOptions = function(show_cash, show_on_delivery, show_card) {
        var order = $('#calendar-order');
        var payment_select = $('select[name="payment_method"]');
        var is_admin = $('input[name="user_id"]').val() === "153";
        var options_html = '';
        payment_select.find('option').remove();

        if (show_cash) {
            var price = order.find('input[type="hidden"][name="cash"]').val();
            options_html += '<option value="cash" selected="selected" data-value="'+price+'">Platba v hotovosti (+' + price + ' &euro;)</option>';
        }

        if (show_on_delivery) {
            var price = order.find('input[type="hidden"][name="on-delivery"]').val();
            options_html += '<option value="on-delivery" selected="selected" data-value="'+price+'">Dobierka (+' + price + ' &euro;)</option>';
        }

        if (show_card) {
            var price = order.find('input[type="hidden"][name="card"]').val();
            options_html += '<option value="card" selected="selected" data-value="'+price+'">Platba kartou online - prevod (+' + price + ' &euro;)</option>';
        }

        payment_select.append(options_html);
    };

    var updateDefaultPaymentMethodBasedOnTransferMethod = function(default_payment_method) {
        $('select[name="payment_method"] option').each(function(index, element) {
            if ($(element).attr('value') == default_payment_method) {
                $(element).attr('selected', true);
            } else {
                $(element).removeAttr('selected');
            }
        });
    };

	/******************************
	 * On load actions
	 ******************************/

    $('#is_company').on('click', onCompanyOrderClick);

    $('#different_shipping_address').on('click', onDifferentShippingAddressClick);

    $('select[name="transport_method"]').on('change', onShippingMethodChange);

    $('select[name="payment_method"]').on('change', onPaymentMethodChange);

    $('select[name="quantity[]"]').on('change', onCalendarQuantityChange);

	$('button[name="order_submit"]').on('click', onOrderSubmit);

    $('form[id="order_form"]').on('submit', onOrderSubmit);

	$('button[id="special_code_verify"]').on('click', onDiscountCouponVerifyClick);

    $('button[id="special_code_remove"]').on('click', onDiscountCouponRemoveClick);

    $('.billing-address-form input').on('change blur', onInputDataChange);

    $('.shipping-address-form input').on('change blur', onInputDataChange);

    $('#depo_close_popup').on('click', onDepoPupupClosed);

    updatePaymentMethodSelectOptions(true, false, true);

    updateFinalPrice();

    onShippingMethodChange();
});