<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class for woocommerce hooks
 */
final class WooCommerce {

	use Product_Helper;

	/**
	 * Hold the current instance
	 * 
	 * @var Main
	 */
	private static $instance = null;

	/**
	 * Get the instance of plugin
	 * 
	 * @since 1.0.0
	 * @return Main
	 */
	public static function get_instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter('wc_get_template', [$this, 'handle_price_template'], 100, 2);
		add_filter('wc_get_template', [$this, 'handle_add_to_cart_template'], 100, 2);
		add_filter('woocommerce_get_price_html', [$this, 'change_loop_price_html'], 100, 2);
		add_filter('woocommerce_loop_add_to_cart_link', [$this, 'change_loop_add_to_cart_link'], 100, 2);

		add_filter('woocommerce_product_get_price', [$this, 'set_price'], 1000, 2);
		add_filter('woocommerce_product_get_sale_price', [$this, 'set_sales_price'], 1000, 2);
		add_filter('woocommerce_product_get_regular_price', [$this, 'set_regular_price'], 1000, 2);

		add_filter('woocommerce_checkout_fields', [$this, 'update_checkout_fields']);
		add_filter('woocommerce_checkout_get_value', [$this, 'get_checkout_value'], 100, 2);
		add_filter('woocommerce_customer_get_shipping_postcode', [$this, 'get_postcode'], 100);

		require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/class-product-variable.php';
		require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/class-product-grouped.php';

		$this->init();
	}

	/**
	 * Initialized product classess
	 * 
	 * @since 1.0.0
	 */
	public function init() {
		new Product\Variable();
		new Product\Grouped();
	}

	/**
	 * Handle price template of woocommerce
	 * 
	 * @since 1.0.0
	 * @return stirng
	 */
	public function handle_price_template($template, $template_name) {
		if ('single-product/price.php' !== $template_name) {
			return $template;
		}

		$product = wc_get_product(get_the_ID());
		if (!$product) {
			return $template;
		}

		$zip_code_enabled = Utils::is_location_based_price_enabled($product->get_id());
		if (false == $zip_code_enabled) {
			return $template;
		}

		$location = Location::get_instance();
		if ($location->need_location()) {
			return Utils::get_template_path('request-location');
		}

		$has_price = $this->has_location_based_price($product);
		if (false === $has_price) {
			return Utils::get_template_path('price-unavailable');
		}

		return $template;
	}

	/**
	 * Handle add to cart template of woocommerce
	 * 
	 * @since 1.0.0
	 * @return stirng
	 */
	public function handle_add_to_cart_template($template, $template_name) {
		$check_templates = array(
			'single-product/add-to-cart/simple.php',
			'single-product/add-to-cart/variable.php',
		);

		if (!in_array($template_name, $check_templates) || !is_product()) {
			return $template;
		}

		$product = wc_get_product(get_the_ID());

		$zip_code_enabled = Utils::is_location_based_price_enabled($product->get_id());
		if (false == $zip_code_enabled) {
			return $template;
		}

		$location = Location::get_instance();
		if ($location->need_location()) {
			return Utils::get_empty_template_path();
		}

		$has_price = $this->has_location_based_price($product);
		if (false === $has_price) {
			return Utils::get_empty_template_path();
		}

		return $template;
	}

	public function update_checkout_fields($fields) {
		$cart_has_location_price = Utils::cart_has_location_price();

		$location = Location::get_instance();
		if (false === $cart_has_location_price || false === $location->has_location()) {
			return $fields;
		}

		$fields['billing']['billing_postcode']['custom_attributes']['readonly'] = 'readonly';
		$fields['shipping']['shipping_postcode']['custom_attributes']['readonly'] = 'readonly';

		$fields['billing']['billing_country']['custom_attributes']['readonly'] = 'readonly';
		$fields['shipping']['shipping_country']['custom_attributes']['readonly'] = 'readonly';

		return $fields;
	}

	/**
	 * Get value of checkout field
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_checkout_value($value, $input) {
		if ('billing_postcode' !== $input && 'billing_country' !== $input) {
			return $value;
		}

		$location = Location::get_instance();
		$cart_has_location_price = Utils::cart_has_location_price();

		if (false === $cart_has_location_price || false === $location->has_location()) {
			return $value;
		}

		if ('billing_postcode' === $input) {
			return $location->get_zip_code();
		}

		if ('billing_country' === $input) {
			return $location->get_country_code();
		}

		return $value;
	}

	public function change_loop_price_html($price, $product) {
		$zip_code_enabled = Utils::is_location_based_price_enabled($product->get_id());
		if (!$zip_code_enabled) {
			return $price;
		}

		$location = Location::get_instance();
		if (!$location->has_location()) {
			$price = '';
		}

		$has_price = $this->has_location_based_price($product);
		if (false === $has_price) {
			$price = '';
		}

		return $price;
	}

	/**
	 * Update add to cart button for product loop
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function change_loop_add_to_cart_link($button, $product) {
		$zip_code_enabled = Utils::is_location_based_price_enabled($product->get_id());
		if (!$zip_code_enabled) {
			return $button;
		}

		$settings = Utils::get_settings();

		$location = Location::get_instance();

		if (!$location->has_location()) {
			return sprintf(
				'<a class="button add_to_cart_button btn-open-zip-code-price-location-modal" href="#" rel="nofollow">%s</a>',
				$settings['product_loop_set_location_button_text']
			);
		}

		$has_price = $this->has_location_based_price($product);
		if (false === $has_price) {
			return sprintf(
				'<a class="button add_to_cart_button" href="%s" rel="nofollow">%s</a>',
				get_permalink($product->get_id()),
				$settings['product_loop_read_more_button_text']
			);
		}

		return $button;
	}

	/**
	 * Get postcode of this plugin
	 * 
	 * @since 1.0.3
	 * @return string
	 */
	public function get_postcode($value) {
		$location = Location::get_instance();
		if ($location->has_location()) {
			$value = $location->get_zip_code();
		}

		return $value;
	}
}
