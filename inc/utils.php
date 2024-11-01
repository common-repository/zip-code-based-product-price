<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Utilities Class
 * 
 * @since 1.0.0
 */
class Utils {

	/**
	 * Get settings
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option('zip_code_based_product_price_settings');

		$settings = wp_parse_args($settings, array(
			'widget_hide_close_button' => 'yes',
			'widget_set_your_location_text' => esc_html__('Please set your location to see prices.', 'zip-code-based-product-price'),
			'widget_location_available_text' => esc_html__('Your country is [country] and zip code is [zip_code].', 'zip-code-based-product-price'),
			'widget_set_location_button_label' => esc_html__('Set your location', 'zip-code-based-product-price'),
			'widget_clear_location_button_label' => esc_html__('Clear your location', 'zip-code-based-product-price'),
			'widget_clear_location_warning_text' => esc_html__('Do you want to clear your location information?', 'zip-code-based-product-price'),
			'widget_button_text_color' => '#fff',
			'widget_button_background_color' => '#333',
			'widget_text_color' => '',
			'widget_background_color' => '#fff',

			'popup_choose_country_text' => esc_html__('Choose a country', 'zip-code-based-product-price'),
			'popup_country_field_description' => esc_html__('Please choose your country.', 'zip-code-based-product-price'),
			'popup_country_field_error_text' => esc_html__('Please choose your country.', 'zip-code-based-product-price'),
			'popup_zip_code_field_description' => esc_html__('Please enter your zip code.', 'zip-code-based-product-price'),
			'popup_zip_code_field_error_text' => esc_html__('Please enter your zip code.', 'zip-code-based-product-price'),
			'popup_submit_button_text' => esc_html__('Submit', 'zip-code-based-product-price'),
			'popup_submit_button_text_color' => '#fff',
			'popup_submit_button_background_color' => '#333',
			'popup_text_color' => '',
			'popup_background_color' => '#fff',
			'show_popup_immediately' => 'no',

			'product_loop_set_location_button_text' => esc_html__('Set your location', 'zip-code-based-product-price'),
			'product_loop_read_more_button_text' => esc_html__('Read more', 'zip-code-based-product-price'),
			'grouped_product_price_unavailable_text' => esc_html__('Price unavailable', 'zip-code-based-product-price'),
			'single_product_price_unavailable_text' => esc_html__('We are sorry. Price is unavailable in your location.', 'zip-code-based-product-price'),
			'single_product_set_location_text' => esc_html__('Please set your location from [location_popup_link title="here"].', 'zip-code-based-product-price'),
			'zipcode_number_only' => 'no',
			'zipcode_max_characters' => '',
		));

		return array_map('stripslashes', $settings);
	}

	/**
	 * Get template path
	 * 
	 * @param string $template_slug Template name 
	 * @param array $args Pass array at template
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_template_path($template_slug) {
		$theme_template_path = sprintf('%s/zip-code-based-product-price/%s.php', get_template_directory(), $template_slug);
		$stylesheet_template_path = sprintf('%s/zip-code-based-product-price/%s.php', get_stylesheet_directory(), $template_slug);

		if (file_exists($stylesheet_template_path)) {
			$template_path = $stylesheet_template_path;
		} elseif (file_exists($theme_template_path)) {
			$template_path = $theme_template_path;
		} else {
			$template_path = ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'templates/' . $template_slug . '.php';
		}

		return apply_filters('zip_code_based_product_price/load_template', $template_path, $template_slug);
	}

	/**
	 * Get template of this plugin
	 * 
	 * @param string $template_slug Template name 
	 * @param array $args Pass array at template
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_template($template_slug, $args = []) {
		$template_path = self::get_template_path($template_slug);

		if (file_exists($template_path)) {
			if (is_array($args)) {
				extract($args);
			}

			require $template_path;
		}
	}

	/**
	 * Get empty template
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_empty_template_path() {
		return ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'templates/blank.php';
	}

	public static function shortcode_location_popup_link($atts, $content = null) {
		$atts = shortcode_atts(array(
			'title' => 'here'
		), $atts);

		return sprintf('<a class="btn-open-zip-code-price-location-modal" href="#">%s</a>', esc_html($atts['title']));
	}

	/**
	 * Get zip code based price for a product
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_prices($post_id) {
		$prices = (array) get_post_meta($post_id, 'zip_code_based_product_price', true);

		array_walk($prices, function (&$price) {
			$price = array_map('trim', wp_parse_args($price, array('country_code' => '', 'enable' => 'no', 'zip_codes' => '', 'regular_price' => '', 'sale_price' => '')));

			if (empty($price['country_code']) || '*' == $price['country_code']) {
				$price['country_code'] = 'global';
			}

			$price['zip_codes'] = apply_filters('zip_code_based_product_price/price_zip_codes', $price['zip_codes']);
			if (!$price['zip_codes']) {
				$price['zip_codes'] = '';
			}

			$price['zip_codes'] = array_filter(array_map('trim', explode(',', $price['zip_codes'])));
		});

		return array_filter($prices, function ($price) {
			return 'yes' == $price['enable'] && count($price['zip_codes']) > 0 && !empty($price['regular_price']);
		});
	}

	/**
	 * Check zip code price enabled for product ID by meta
	 * 
	 * @since 1.0.0
	 * @param int $post_id WooCommerce Product ID
	 * @return boolean
	 */
	public static function is_location_based_price_meta_enabled($post_id) {
		return get_post_meta($post_id, 'zip_code_based_price_enable', true) === 'yes';
	}

	/**
	 * Check zip code price enabled for product
	 * 
	 * @since 1.0.0
	 * @param int $post_id WooCommerce Product ID
	 * @return boolean
	 */
	public static function is_location_based_price_enabled($post_id) {
		$product = wc_get_product($post_id);

		$zip_code_enabled[] = self::is_location_based_price_meta_enabled($product->get_id());

		if (is_a($product, 'WC_Product_Variable')) {
			$variation_ids = $product->get_visible_children();
			foreach ($variation_ids as $variation_id) {
				$zip_code_enabled[] = self::is_location_based_price_meta_enabled($variation_id);
			}
		}

		$zip_code_enabled_items = array_filter($zip_code_enabled);
		return count($zip_code_enabled_items) > 0;
	}

	/**
	 * Check if cart contain location based price product
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function cart_has_location_price() {
		$has_location_enabled_item = false;

		$cart_items = WC()->cart->get_cart_contents();
		foreach ($cart_items as $cart_item) {
			$location_enabled = self::is_location_based_price_enabled($cart_item['product_id']);
			if ($location_enabled) {
				$has_location_enabled_item = true;
				break;
			}
		}

		return $has_location_enabled_item;
	}
}
