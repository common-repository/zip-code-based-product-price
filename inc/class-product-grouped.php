<?php

namespace Zip_Code_Based_Product_Price\Product;

use Zip_Code_Based_Product_Price\Utils;
use Zip_Code_Based_Product_Price\Location;
use Zip_Code_Based_Product_Price\Product_Helper;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class for woocommerce grouped product
 */
final class Grouped {

	use Product_Helper;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('woocommerce_grouped_product_list_before_quantity', [$this, 'before_quantity_column']);
		add_action('woocommerce_grouped_product_list_after_quantity', [$this, 'after_quantity_column']);

		add_filter('woocommerce_quantity_input_args', [$this, 'disable_quantity_field'], 100);
		add_filter('woocommerce_grouped_product_list_column_price', [$this, 'change_price_column_value'], 100, 2);
	}

	/**
	 * Set query var for make quantity field readonly
	 * 
	 * @since 1.0.0
	 */
	public function before_quantity_column($product) {
		$zip_code_enabled = Utils::is_location_based_price_enabled($product->get_id());
		if (false == $zip_code_enabled) {
			return;
		}

		$location = Location::get_instance();
		if ($location->need_location()) {
			set_query_var('quantity_field_readonly', true);
		}

		$has_price = $this->has_location_based_price($product);
		if (false === $has_price) {
			set_query_var('quantity_field_readonly', true);
		}
	}

	/**
	 * Reset quantity field readonly variable to false
	 * 
	 * @since 1.0.0
	 */
	public function after_quantity_column($product) {
		set_query_var('quantity_field_readonly', false);
	}

	/**
	 * Make quantity field readonly
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public function disable_quantity_field($args) {
		if (true === get_query_var('quantity_field_readonly')) {
			$args['readonly'] = true;
		}

		return $args;
	}

	/**
	 * Change price column of grouped product
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function change_price_column_value($value, $product) {
		$zip_code_enabled = Utils::is_location_based_price_enabled($product->get_id());
		if (false == $zip_code_enabled) {
			return $value;
		}

		$settings = Utils::get_settings();

		$location = Location::get_instance();
		if ($location->need_location()) {
			return apply_filters('zip_code_based_product_price/texts', '', 'grouped_product_need_location_empty_text', $product);
		}

		$has_price = $this->has_location_based_price($product);
		if (false === $has_price) {
			return $settings['grouped_product_price_unavailable_text'];
		}

		return $value;
	}
}
