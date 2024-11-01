<?php

if (!defined('ABSPATH')) {
	exit;
}

$country_field_description = $settings['popup_country_field_description'];
$zip_code_field_description = $settings['popup_zip_code_field_description'];

$selling_countries = WC()->countries->get_allowed_countries();
$first_country = key($selling_countries);

$zip_code_field_attributes = array('type' => 'text');
if ('yes' == $settings['zipcode_number_only']) {
	$zip_code_field_attributes['type'] = 'number';
	$zip_code_field_attributes['min'] = '0';
	$zip_code_field_attributes['pattern'] = '\d*';
}

if (absint($settings['zipcode_max_characters']) > 0) {
	$zip_code_field_attributes['maxlength'] = absint($settings['zipcode_max_characters']);
}

$attribute_html = '';
foreach ($zip_code_field_attributes as $attr => $value) {
	$attribute_html .= sprintf(' %s="%s"', $attr, $value);
} ?>

<form method="post">
	<a class="btn-close-popup" href="#"></a>
	<?php wp_nonce_field('_nonce_zip_codes_based_product_price_set_location_form'); ?>

	<div class="message-box"></div>

	<?php if (1 === count($selling_countries)) : ?>
		<input type="hidden" name="country" value="<?php echo esc_attr($first_country); ?>">
	<?php else : ?>
		<div class="field-row">
			<select name="country" id="country">
				<option value=""><?php echo esc_html($settings['popup_choose_country_text']); ?></option>
				<?php
				foreach ($selling_countries as $country_code => $country_label) {
					printf('<option value="%s" %s>%s</option>', esc_attr($country_code), selected($country_code, $selected_country, false), esc_html($country_label));
				}
				?>
			</select>

			<?php if (!empty($country_field_description)) : ?>
				<p class="field-guide"><?php echo wp_kses_post($country_field_description); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="field-row">
		<input name="zip_code" placeholder="<?php esc_attr_e('Zip code', 'zip-code-based-product-price'); ?>" value="<?php echo esc_attr($zip_code); ?>" <?php echo wp_kses_post($attribute_html) ?>>

		<?php if (!empty($zip_code_field_description)) : ?>
			<p class="field-guide"><?php echo wp_kses_post($zip_code_field_description); ?></p>
		<?php endif; ?>
	</div>

	<footer>
		<button class="btn-zip-code-based-price" type="submit"><?php echo esc_html($settings['popup_submit_button_text']); ?></button>
	</footer>
</form>