<?php

namespace Zip_Code_Based_Product_Price\Admin;

defined('ABSPATH') || exit;


/**
 * Class for admin panel
 * 
 * @since 1.0.0
 */
final class Product_Fields {

	/**
	 * Class contructor
	 */
	public function __construct() {
		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
		add_action('woocommerce_process_product_meta', [$this, 'save_zip_code_price_data']);
		add_action('woocommerce_save_product_variation', [$this, 'save_variable_product_meta']);
		add_action('woocommerce_product_options_pricing', [$this, 'zip_code_price_option']);
		add_action('woocommerce_variation_options_pricing', [$this, 'variation_option'], 5, 3);
		add_action('admin_footer', [$this, 'template_zip_code_row']);
	}

	/**
	 * Enqueue script on backend
	 * 
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();
		if ('product' !== $current_screen->post_type) {
			return;
		}

		wp_register_script('sortable', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/js/sortable.min.js', [], '1.15.0', true);
		wp_register_script('sortable-jquery', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/js/jquery-sortable.js', ['jquery', 'sortable'], 1.0, true);

		wp_enqueue_style('zip-code-based-product-price', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/css/admin.css', [], ZIP_CODE_BASED_PRODUCT_PRICE_VERSION);
		wp_enqueue_script('zip-code-based-product-price', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/js/admin.js', ['jquery', 'sortable-jquery'], ZIP_CODE_BASED_PRODUCT_PRICE_VERSION, true);
		wp_localize_script('zip-code-based-product-price', 'zip_code_based_product_price', array(
			'i10n' => array(
				'delete_zip_code_row_warning' => esc_html__('Do you want to delete this row?', 'zip-code-based-product-price')
			)
		));
	}

	/**
	 * Save meta data for this plugin
	 * 
	 * @since 1.0.0
	 */
	public function save_zip_code_price_data($post_id) {
		if (!isset($_POST['_nonce_zip_code_based_product_settings'])) {
			return;
		}

		if (!wp_verify_nonce(sanitize_text_field($_POST['_nonce_zip_code_based_product_settings']), 'zip_code_based_price_setting_nonce')) {
			return;
		}

		$enable = isset($_POST['zip_code_based_price_enable']) ? 'yes' : 'no';
		update_post_meta($post_id, 'zip_code_based_price_enable', $enable);

		$zip_codes = isset($_POST['zip_code_based_product_price']) ? wc_clean($_POST['zip_code_based_product_price']) : null;
		update_post_meta($post_id, 'zip_code_based_product_price', $zip_codes);
	}

	/**
	 * Save zip code pricing for variable product
	 * 
	 * @since 1.0.0
	 */
	public function save_variable_product_meta($variation_id) {
		$_nonce_key = '_nonce_zip_code_based_variation_product_settings_' . $variation_id;
		if (!isset($_POST[$_nonce_key])) {
			return;
		}

		if (!wp_verify_nonce(sanitize_text_field($_POST[$_nonce_key]), 'zip_code_based_price_variation_product_setting_nonce_' . $variation_id)) {
			return;
		}

		$enable_field_key = 'zip_code_based_product_price_enable_' . $variation_id;

		$enable_zip_based_price = isset($_POST[$enable_field_key]) ? 'yes' : 'no';
		update_post_meta($variation_id, 'zip_code_based_price_enable', $enable_zip_based_price);

		$product_price_key = 'zip_code_based_product_price_' . $variation_id;
		$product_prices = isset($_POST[$product_price_key]) && is_array($_POST[$product_price_key]) ? wc_clean($_POST[$product_price_key]) : null;
		update_post_meta($variation_id, 'zip_code_based_product_price', $product_prices);
	}

	/**
	 * Pricing table field of product
	 * 
	 * @since 1.0.0
	 */
	public function pricing_table($post_id, $field_name) {
		$wrapper_class = get_post_meta($post_id, 'zip_code_based_price_enable', true) === 'yes' ? '' : 'hidden';

		$zip_codes_rows = get_post_meta($post_id, 'zip_code_based_product_price', true);
		if (!is_array($zip_codes_rows)) {
			$zip_codes_rows = [];
		}

		$zip_codes_rows = array_map(function ($item) {
			return wp_parse_args($item, array(
				'enable' => 'no'
			));
		}, $zip_codes_rows);


		$zip_codes_rows = array_values($zip_codes_rows); ?>

		<div class="options_group show_if_zip_code_based_price_enable <?php echo esc_attr($wrapper_class); ?>">
			<fieldset class="form-field zip-code-based-pricing">
				<table class="table-zip-code-based-pricing widefat" data-name="<?php echo esc_attr($field_name); ?>">
					<thead>
						<tr>
							<th class="column-sortable"></th>
							<th class="column-country-code"><a href="https://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank"><?php esc_html_e('Country code', 'zip-code-based-product-price'); ?></a><?php echo wc_help_tip(esc_html__('A 2 digit country code, e.g. US. Leave blank to apply to all.', 'zip-code-based-product-price')); ?></th>
							<th><?php esc_html_e('Zip codes', 'zip-code-based-product-price'); ?><?php echo wc_help_tip(esc_html__('Enter zip codes. For multiple zip codes use commas.', 'zip-code-based-product-price')); ?></th>
							<th class="column-regular-price"><?php esc_html_e('Regular price', 'zip-code-based-product-price'); ?></th>
							<th class="column-sale-price"><?php esc_html_e('Sale price', 'zip-code-based-product-price'); ?></th>
							<th class="column-enabled"><?php echo wc_help_tip(esc_html__('Enable/Disable zip codes row.', 'zip-code-based-product-price')); ?></th>
							<th class="column-action"></th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($zip_codes_rows as $row_no => $row) : ?>
							<tr data-row-no="<?php echo esc_attr($row_no); ?>">
								<td class="column-sortable">
									<span class="sortable-handle dashicons dashicons-menu-alt"></span>
								</td>
								<td>
									<input name="<?php echo esc_attr("{$field_name}[{$row_no}]"); ?>[country_code]" type="text" placeholder="*" data-name="country_code" value="<?php echo esc_attr($row['country_code']); ?>">
								</td>

								<td>
									<?php echo apply_filters('zip_code_based_product_price/field_zip_codes', '<input type="text" name="' . esc_attr("{$field_name}[{$row_no}]") . '[zip_codes]" data-name="zip_codes" placeholder="' . esc_html__('Enter zip codes, for multiple zip codes use commas.', 'zip-code-based-product-price') . '" value="' . esc_attr($row['zip_codes']) . '" />', $row['zip_codes'], $field_name, $row_no) ?>
								</td>

								<td>
									<input name="<?php echo esc_attr("{$field_name}[{$row_no}]"); ?>[regular_price]" class="price-input wc_input_price" type="text" placeholder="0.00" data-name="regular_price" value="<?php echo esc_attr($row['regular_price']); ?>">
								</td>

								<td>
									<input name="<?php echo esc_attr("{$field_name}[{$row_no}]"); ?>[sale_price]" class="price-input wc_input_price" type="text" placeholder="0.00" data-name="sale_price" value="<?php echo esc_attr($row['sale_price']); ?>">
								</td>

								<td class="has-padding column-enabled">
									<input name="<?php echo esc_attr("{$field_name}[{$row_no}]"); ?>[enable]" class="zip-code-base-product-price-enable-checkbox" type="checkbox" data-name="enable" value="yes" <?php checked('yes', $row['enable']); ?> title="<?php esc_html_e('Click to enable/disable these zip codes.', 'zip-code-based-product-price'); ?>">
								</td>

								<td class="has-padding column-action">
									<div class="row-action-items">
										<a class="btn-zip-codes-clone dashicons dashicons-admin-page" href="#" title="<?php esc_attr_e('Clone this row', 'zip-code-based-product-price'); ?>"></a>
										<a class="btn-zip-codes-delete dashicons dashicons-trash" href="#" title="<?php esc_attr_e('Delete this row', 'zip-code-based-product-price'); ?>"></a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>

					<tfoot>
						<tr>
							<td colspan="20">
								<a class="button btn-add-new-zip-codes-row" href="#"><?php esc_html_e('Add new row', 'zip-code-based-product-price'); ?></a>
							</td>
						</tr>
					</tfoot>
				</table>
			</fieldset>
		</div>
	<?php
	}

	/**
	 * Add field for zip code based price
	 * 
	 * @since 1.0.0
	 */
	public function zip_code_price_option() {
		wp_nonce_field('zip_code_based_price_setting_nonce', '_nonce_zip_code_based_product_settings', false);

		$zip_code_based_price_enable = get_post_meta(get_the_ID(), 'zip_code_based_price_enable', true);

		woocommerce_wp_checkbox(array(
			'id'          => 'zip_code_based_price_enable',
			'value'       => $zip_code_based_price_enable,
			'label'       => esc_html__('Zip code based pricing', 'zip-code-based-product-price'),
			'description' => esc_html__('Enable zip code based pricing', 'zip-code-based-product-price'),
			'wrapper_class' => 'zip-code-based-product-price-enable-field',
		));

		$this->pricing_table(get_the_ID(), 'zip_code_based_product_price');
	}

	public function variation_option($loop, $variation_data, $variation) {
		wp_nonce_field('zip_code_based_price_variation_product_setting_nonce_' . $variation->ID, '_nonce_zip_code_based_variation_product_settings_' . $variation->ID, false);

		$zip_code_based_price_enable = get_post_meta($variation->ID, 'zip_code_based_price_enable', true);

		woocommerce_wp_checkbox(array(
			'value'         => $zip_code_based_price_enable,
			'name'          => "zip_code_based_product_price_enable_{$variation->ID}",
			'label'         => false,
			'description'   => esc_html__('Enable zip code based pricing', 'zip-code-based-product-price'),
			'wrapper_class' => 'form-row form-row-full zip-code-based-product-price-enable-field',
		));

		$this->pricing_table($variation->ID, "zip_code_based_product_price_{$variation->ID}");
	}

	public function template_zip_code_row() {
	?>
		<template id="tmpl-zip-code-based-product-price-row">
			<tr data-row-no="{{data.row_no}}">
				<td class="column-sortable"><span class="sortable-handle dashicons dashicons-menu-alt"></span></td>
				<td><input name="{{data.name}}[{{data.row_no}}][country_code]" type="text" placeholder="*" data-name="country_code"></td>
				<td><?php echo apply_filters('zip_code_based_product_price/field_zip_codes_template', '<input type="text" name="{{data.name}}[{{data.row_no}}][zip_codes]" data-name="zip_codes" placeholder="' . esc_html__('Enter zip codes, for multiple zip codes use commas.', 'zip-code-based-product-price') . '" />') ?></td>
				<td><input name="{{data.name}}[{{data.row_no}}][regular_price]" class="price-input wc_input_price" type="text" placeholder="0.00" data-name="regular_price"></td>
				<td><input name="{{data.name}}[{{data.row_no}}][sale_price]" class="price-input wc_input_price" type="text" placeholder="0.00" data-name="sale_price"></td>
				<td class="has-padding column-enabled">
					<input name="{{data.name}}[{{data.row_no}}][enable]" value="yes" class="zip-code-base-product-price-enable-checkbox" type="checkbox" data-name="enable" checked title="<?php esc_html_e('Click to enable/disable these zip codes.', 'zip-code-based-product-price'); ?>">
				</td>
				<td class="has-padding column-action">
					<div class="row-action-items">
						<a class="btn-zip-codes-clone dashicons dashicons-admin-page" href="#" title="<?php esc_html_e('Clone this row', 'zip-code-based-product-price'); ?>"></a>
						<a class="btn-zip-codes-delete dashicons dashicons-trash" href="#" title="<?php esc_html_e('Delete this row', 'zip-code-based-product-price'); ?>"></a>
					</div>
				</td>
			</tr>
		</template>
<?php
	}
}
