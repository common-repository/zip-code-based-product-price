(function ($) {
	$('.form-footer .button-reset').on('click', function (e) {
		const response = confirm(zip_code_based_product_price.i10n.reset_form_warning);
		if (!response) {
			e.preventDefault();
		}
	})

	$('.zip-code-price-color-field').wpColorPicker();

})(jQuery)