<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

$settings = Utils::get_settings();

?>

<form method="post">
	<?php wp_nonce_field('_nonce_zip_code_based_price_settings_form'); ?>

	<div class="zip-code-based-pricing-box">
		<div class="zip-code-based-pricing-header">
			<h2 class="zip-code-based-pricing-title"><?php esc_html_e('Widget Settings', 'zip-code-based-product-price'); ?></h2>
		</div>

		<div class="zip-code-based-pricing-body">
			<table class="form-table">
				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Set your location text', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[widget_set_your_location_text]" value="<?php echo esc_html($settings['widget_set_your_location_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Zip code available text', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[widget_location_available_text]" value="<?php echo esc_html($settings['widget_location_available_text']); ?>">
						<p class="field-note"><?php esc_html_e('Use the shortcode [country] for the country and [zip_code] for the zip code.', 'zip-code-based-product-price'); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Hide close button', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<div class="zip-code-based-pricing-toggle">
							<input name="settings[widget_hide_close_button]" type="radio" value="yes" data-label="<?php esc_html_e('Yes', 'zip-code-based-product-price'); ?>" <?php checked('yes', $settings['widget_hide_close_button']); ?>>
							<input name="settings[widget_hide_close_button]" type="radio" value="no" data-label="<?php esc_html_e('No', 'zip-code-based-product-price'); ?>" <?php checked('no', $settings['widget_hide_close_button']); ?>>
						</div>
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description" for="widget-set-location-button">
							<?php esc_html_e('Set location button label', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[widget_set_location_button_label]" value="<?php echo esc_html($settings['widget_set_location_button_label']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description" for="widget-clear-location-button">
							<?php esc_html_e('Clear location button label', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[widget_clear_location_button_label]" value="<?php echo esc_html($settings['widget_clear_location_button_label']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description" for="widget-clear-location-button">
							<?php esc_html_e('Clear location warning text', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[widget_clear_location_warning_text]" value="<?php echo esc_html($settings['widget_clear_location_warning_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Button Text color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[widget_button_text_color]" data-default-color="#fff" value="<?php echo esc_attr($settings['widget_button_text_color']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Button background color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[widget_button_background_color]" data-default-color="#333" value="<?php echo esc_attr($settings['widget_button_background_color']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Widget Text color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[widget_text_color]" value="<?php echo esc_attr($settings['widget_text_color']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Widget background color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[widget_background_color]" value="<?php echo esc_attr($settings['widget_background_color']); ?>">
					</td>
				</tr>

			</table>
		</div>
	</div>

	<div class="zip-code-based-pricing-box">
		<div class="zip-code-based-pricing-header">
			<h2 class="zip-code-based-pricing-title"><?php esc_html_e('Popup Settings', 'zip-code-based-product-price'); ?></h2>
		</div>

		<div class="zip-code-based-pricing-body">
			<table class="form-table">

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Choose a country text', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[popup_choose_country_text]" value="<?php echo esc_html($settings['popup_choose_country_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Country field description', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[popup_country_field_description]" value="<?php echo esc_html($settings['popup_country_field_description']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Country field error text', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[popup_country_field_error_text]" value="<?php echo esc_html($settings['popup_country_field_error_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Zip code field description', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[popup_zip_code_field_description]" value="<?php echo esc_html($settings['popup_zip_code_field_description']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Zip code field error text', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[popup_zip_code_field_error_text]" value="<?php echo esc_html($settings['popup_zip_code_field_error_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Button text', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="settings[popup_submit_button_text]" value="<?php echo esc_html($settings['popup_submit_button_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Button Text color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[popup_submit_button_text_color]" data-default-color="#fff" value="<?php echo esc_attr($settings['popup_submit_button_text_color']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Button background color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[popup_submit_button_background_color]" data-default-color="#333" value="<?php echo esc_attr($settings['popup_submit_button_background_color']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Popup Text color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[popup_text_color]" value="<?php echo esc_attr($settings['popup_text_color']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Popup background color', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<input class="zip-code-price-color-field" type="text" name="settings[popup_background_color]" value="<?php echo esc_attr($settings['popup_background_color']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php esc_html_e('Show popup window immediately', 'zip-code-based-product-price'); ?>
						</label>
						<p class="field-label-description"><?php esc_html_e('Show popup window immediately if zip code is not available.', 'zip-code-based-product-price'); ?></p>
					</th>
					<td>
						<div class="zip-code-based-pricing-toggle">
							<input name="settings[show_popup_immediately]" type="radio" value="yes" data-label="<?php esc_html_e('Yes', 'zip-code-based-product-price'); ?>" <?php checked('yes', $settings['show_popup_immediately']); ?>>
							<input name="settings[show_popup_immediately]" type="radio" value="no" data-label="<?php esc_html_e('No', 'zip-code-based-product-price'); ?>" <?php checked('no', $settings['show_popup_immediately']); ?>>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="zip-code-based-pricing-box">
		<div class="zip-code-based-pricing-header">
			<h2 class="zip-code-based-pricing-title"><?php esc_html_e('Others Settings', 'zip-code-based-product-price'); ?></h2>
		</div>

		<div class="zip-code-based-pricing-body">
			<table class="form-table">

				<tr>
					<th>
						<label>
							<?php esc_html_e('Set your location button text', 'zip-code-based-product-price'); ?>
						</label>

						<p class="field-label-description"><?php esc_html_e('Change the text of set your location button on the product loop if the location is unavailable.', 'zip-code-based-product-price'); ?></p>
					</th>
					<td>
						<input type="text" name="settings[product_loop_set_location_button_text]" value="<?php echo esc_html($settings['product_loop_set_location_button_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php esc_html_e('Read more button text', 'zip-code-based-product-price'); ?>
						</label>

						<p class="field-label-description"><?php esc_html_e('Change the read more button text if the price is unavailable on the product loop.', 'zip-code-based-product-price'); ?></p>
					</th>
					<td>
						<input type="text" name="settings[product_loop_read_more_button_text]" value="<?php echo esc_html($settings['product_loop_read_more_button_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php esc_html_e('Price unavailable text for grouped product', 'zip-code-based-product-price'); ?>
						</label>

						<p class="field-label-description"><?php esc_html_e('Price unavailable text on the single product page of grouped products.', 'zip-code-based-product-price'); ?></p>
					</th>
					<td>
						<input type="text" name="settings[grouped_product_price_unavailable_text]" value="<?php echo esc_html($settings['grouped_product_price_unavailable_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php esc_html_e('Price unavailable text', 'zip-code-based-product-price'); ?>
						</label>

						<p class="field-label-description"><?php esc_html_e('Display price unavailable text on the single product page.', 'zip-code-based-product-price'); ?></p>
					</th>
					<td>
						<input type="text" name="settings[single_product_price_unavailable_text]" value="<?php echo esc_html($settings['single_product_price_unavailable_text']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php esc_html_e('Set location text', 'zip-code-based-product-price'); ?>
						</label>

						<p class="field-label-description"><?php esc_html_e('Replace price to set location text on the single product page if the location is unavailable.', 'zip-code-based-product-price'); ?></p>
					</th>
					<td>
						<input type="text" name="settings[single_product_set_location_text]" value="<?php echo esc_html($settings['single_product_set_location_text']); ?>">
						<p class="field-note"><?php esc_html_e('Use [location_popup_link title="here"] shortcode for the popup link.', 'zip-code-based-product-price'); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description">
							<?php esc_html_e('Zip code only number', 'zip-code-based-product-price'); ?>
						</label>
					</th>
					<td>
						<div class="zip-code-based-pricing-toggle">
							<input name="settings[zipcode_number_only]" type="radio" value="yes" data-label="<?php esc_html_e('Yes', 'zip-code-based-product-price'); ?>" <?php checked('yes', $settings['zipcode_number_only']); ?>>
							<input name="settings[zipcode_number_only]" type="radio" value="no" data-label="<?php esc_html_e('No', 'zip-code-based-product-price'); ?>" <?php checked('no', $settings['zipcode_number_only']); ?>>
						</div>
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php esc_html_e('Maximum characters allow for zipcode', 'zip-code-based-product-price'); ?>
						</label>

						<p class="field-label-description"><?php esc_html_e('Allow maximum characters for customers to enter in the zip code field. Keep blank for unlimited.', 'zip-code-based-product-price'); ?></p>
					</th>
					<td>
						<input type="number" min="0" step="1" name="settings[zipcode_max_characters]" style="width: 80px;padding-right:0" value="<?php echo esc_html($settings['zipcode_max_characters']); ?>">
					</td>
				</tr>

			</table>
		</div>
	</div>

	<div class="form-footer">
		<button class="button button-primary" name="submit" value="save"><?php esc_html_e('Save Changes', 'zip-code-based-product-price'); ?></button>
		<button class="button button-reset" name="submit" value="reset"><?php esc_html_e('Reset Settings', 'zip-code-based-product-price'); ?></button>
	</div>
</form>