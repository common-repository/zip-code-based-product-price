(function ($) {

	$('#zip-code-based-product-price-location-popup').on('close', function () {
		$(this).removeClass('opened')
	})

	$('#zip-code-based-product-price-location-popup form').on('submit', function () {
		const current_form = $(this);
		const submit_button = current_form.find('[type="submit"]')

		const message_box = current_form.find('.message-box').removeAttr('type').html('')

		const country_code = $(this).find('[name="country"]').val()
		if (!country_code.length) {
			message_box.html(zip_code_based_product_price.i10n.error_country_code_missing).attr('data-type', 'error')
			return false;
		}

		const zip_code = $(this).find('[name="zip_code"]').val();
		if (!zip_code.length) {
			message_box.html(zip_code_based_product_price.i10n.error_zip_code_missing).attr('data-type', 'error')
			return false;
		}

		submit_button.prop('disabled', true);
		Cookies.set('zip_code_based_product_price_location', JSON.stringify({ country_code, zip_code }), {
			expires: 7
		})

		window.location.reload();
		submit_button.prop('disabled', false)

		return false;
	})

	$('#zip-code-based-product-price-location-popup .btn-close-popup').on('click', function (e) {
		e.preventDefault();
		$('#zip-code-based-product-price-location-popup').trigger('close')
	})

	$(document).keyup(function (e) {
		if (e.key === "Escape") {
			$('#zip-code-based-product-price-location-popup').trigger('close')
		}
	});

	$('body').on('click', function (e) {
		if (!$(e.target).is('#zip-code-based-product-price-location-popup')) {
			return;
		}

		if ($(e.target).closest('#zip-code-based-product-price-location-popup form').length == 0) {
			$('#zip-code-based-product-price-location-popup').trigger('close')
		}
	});

	$('.btn-open-zip-code-price-location-modal').on('click', function (e) {
		e.preventDefault();
		$('#zip-code-based-product-price-location-popup').addClass('opened')
	})

	$('#zip-code-based-product-price-location-widget .btn-zip-code-price-clear-location').on('click', function (e) {
		const response = confirm(zip_code_based_product_price.i10n.confirm_clear_location)
		if (response === false) {
			e.preventDefault();
		}
	})

	$('#zip-code-based-product-price-location-widget .btn-close-widget').on('click', function (e) {
		e.preventDefault();
		$('#zip-code-based-product-price-location-widget').fadeOut(150);
	})

	$('#zip-code-based-product-price-location-popup [name="zip_code"][maxlength]').on('keydown', function (e) {
		const maxlength = $(this).attr('maxlength');
		if ($(this).val().length >= maxlength && e.key != 'Backspace') {
			return false;
		}
	})

	$('#ship-to-different-address-checkbox').on('change', function () {
		if (!zip_code_based_product_price.zipcode.length) {
			return;
		}

		if ($(this).is(':checked')) {
			$('#billing_postcode').prop('readonly', false);
		} else {
			$('#billing_postcode').prop('readonly', true);
			$('#billing_postcode').val(zip_code_based_product_price.zipcode);
		}

	}).trigger('change')

})(jQuery)