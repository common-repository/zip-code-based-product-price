<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class product helper
 */
trait Product_Helper {

	/**
	 * Check if product has price for the location
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function has_location_based_price($product) {
		$location = Location::get_instance();

		$available_price = $location->get_price($product->get_id());

		if (is_a($product, 'WC_Product_Variable')) {
			$variation_prices = array();
			foreach ($product->get_visible_children() as $variation_id) {
				$variation_prices[] = $location->get_price($variation_id);
			}

			$variation_prices = array_filter($variation_prices);
			$available_price = count($variation_prices) > 0;
		}

		return $available_price;
	}

	/**
	 * Update product price 
	 * 
	 * @since 1.0.0
	 * @return float
	 */
	public function set_price($value, $product) {
		if (Utils::is_location_based_price_meta_enabled($product->get_id()) === false) {
			return $value;
		}

		$location = Location::get_instance();
		$price = $location->get_price($product->get_id());

		if ($price) {
			if (!empty($price['sale_price'])) {
				return floatval($price['sale_price']);
			}

			return floatval($price['regular_price']);
		}

		return $value;
	}

	/**
	 * Update product sales price
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	public function set_sales_price($value, $product) {
		if (Utils::is_location_based_price_meta_enabled($product->get_id()) === false) {
			return $value;
		}

		$location = Location::get_instance();
		$price = $location->get_price($product->get_id());

		if (!empty($price['sale_price'])) {
			return floatval($price['sale_price']);
		}

		return null;
	}

	/**
	 * Update product regular price
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	public function set_regular_price($value, $product) {
		if (Utils::is_location_based_price_meta_enabled($product->get_id()) === false) {
			return $value;
		}

		$location = Location::get_instance();
		$price = $location->get_price($product->get_id());
		if ($price) {
			return floatval($price['regular_price']);
		}

		return $value;
	}
}
