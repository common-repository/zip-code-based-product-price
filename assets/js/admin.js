(function ($) {
	$('body').on('change', '.zip-code-based-product-price-enable-field [type="checkbox"]', function () {
		const price_table_field = $(this).closest('.zip-code-based-product-price-enable-field ').next('.show_if_zip_code_based_price_enable ')
		if ($(this).is(':checked')) {
			price_table_field.show();
		} else {
			price_table_field.hide();
		}
	})

	$('.table-zip-code-based-pricing').on('update', function () {
		const field_name = $(this).data('name');
		$(this).find('tbody tr').each(function (index) {
			$(this).attr('data-row-no', index);
			$(this).find('[data-name]').each(function () {
				const input_name = $(this).data('name');
				$(this).attr('name', `${field_name}[${index}][${input_name}]`)
			})
		})
	})

	$('body').on('click', '.table-zip-code-based-pricing .btn-add-new-zip-codes-row', function (e) {
		e.preventDefault();

		const current_table = $(this).closest('table');
		const zip_codes_table = $(this).closest('.table-zip-code-based-pricing');
		const zip_code_row_template = wp.template('zip-code-based-product-price-row');

		const all_rows_no = [0];
		zip_codes_table.find('tbody tr').each(function () {
			all_rows_no.push($(this).data('row-no'))
		})

		const max_row_no = Math.max(...all_rows_no);
		const zip_code_new_row = zip_code_row_template({ name: current_table.data('name'), row_no: max_row_no + 1 })
		zip_codes_table.find('tbody').append(zip_code_new_row)
	})

	$('body').on('click', '.table-zip-code-based-pricing .btn-zip-codes-delete', function (e) {
		e.preventDefault()
		const current_table = $(this).closest('table');

		const response = confirm(zip_code_based_product_price.i10n.delete_zip_code_row_warning)
		if (response) {
			$(this).closest('tr').remove();
			current_table.trigger('update');
		}
	})

	$('body').on('click', '.table-zip-code-based-pricing .btn-zip-codes-clone', function (e) {
		e.preventDefault()
		const current_table = $(this).closest('table');
		const current_row = $(this).closest('tr');
		current_row.clone().insertAfter(current_row)
		current_table.trigger('update');
	})

	function zip_code_based_pricing_table_init_sortable() {
		$('.table-zip-code-based-pricing tbody').sortable({
			animation: 150,
			handle: '.column-sortable',
			onUpdate: function (evt) {
				$(evt.target).closest('table').trigger('update');
			},
		})
	}

	zip_code_based_pricing_table_init_sortable();

	$('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
		zip_code_based_pricing_table_init_sortable();
	});
})(jQuery)