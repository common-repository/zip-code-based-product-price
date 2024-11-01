<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Location class. Get the customer location from user, cookie and automatically (later)
 */
final class Location {

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
	 * Country code of customer
	 * 
	 * @var string
	 */
	protected $country_code = '';

	/**
	 * Zip code of customer
	 * 
	 * @var string
	 */
	protected $zip_code = '';

	/**
	 * Source of user location
	 * 
	 * @var string
	 */
	private $source = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_from_cookie();
		$this->set_from_customer();
	}

	/**
	 * Set location from cookie
	 * 
	 * @since 1.0.0
	 */
	public function set_from_cookie() {
		if (empty($_COOKIE['zip_code_based_product_price_location'])) {
			return;
		}

		$location = json_decode(stripslashes(sanitize_text_field($_COOKIE['zip_code_based_product_price_location'])), true);
		if (!is_array($location)) {
			return;
		}

		$this->source = 'cookie';

		if (isset($location['country_code'])) {
			$this->country_code = trim($location['country_code']);
		}

		if (isset($location['zip_code'])) {
			$this->zip_code = trim($location['zip_code']);
		}
	}

	/**
	 * Set location from customer
	 * 
	 * @since   1.0.0
	 */
	public function set_from_customer() {
		if ('cookie' == $this->source) {
			return;
		}

		if (!is_a(WC()->customer, 'WC_Customer')) {
			return;
		}

		$country_code = WC()->customer->get_billing_country();
		$zip_code = WC()->customer->get_billing_postcode();

		if (empty($country_code) || empty($zip_code)) {
			return;
		}

		$this->source = 'customer';
		$this->country_code = $country_code;
		$this->zip_code = $zip_code;
	}

	/**
	 * Check if location available
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function has_location() {
		return !empty($this->zip_code) && !empty($this->country_code);
	}

	/**
	 * Check if need location 
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function need_location() {
		return !$this->has_location();
	}

	/**
	 * Check if location set by customer
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function is_set_by_customer() {
		return 'cookie' == $this->source;
	}

	/**
	 * Check if country code available
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function has_country_code() {
		return !empty($this->country_code);
	}

	/**
	 * Get country code
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function get_country_code() {
		return $this->country_code;
	}

	/**
	 * Check if zip code available
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function has_zip_code() {
		return !empty($this->zip_code);
	}

	/**
	 * Get zip code
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function get_zip_code() {
		return $this->zip_code;
	}

	/**
	 * Get location source. Possibility source customer, custom and auto (on later)
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function get_soruce() {
		return $this->source;
	}

	/**
	 * Get price from customer location
	 * 
	 * @since   1.0.0
	 * @param   int $post_id WooCommerce Product ID
	 * @return  array|false
	 */
	public function get_price($post_id) {
		$prices = array_filter(Utils::get_prices($post_id), function ($price) {
			if ($this->country_code == $price['country_code'] && in_array($this->zip_code, $price['zip_codes'])) {
				return true;
			}

			if ('global' == $price['country_code']  && in_array($this->zip_code, $price['zip_codes'])) {
				return true;
			}

			return false;
		});

		$exact_match_price = false;
		foreach ($prices as $price) {
			if ($this->country_code == $price['country_code'] && in_array($this->zip_code, $price['zip_codes'])) {
				$exact_match_price = $price;
				break;
			}
		}

		if ($exact_match_price) {
			return $exact_match_price;
		}

		if (count($prices) > 0) {
			return current($prices);
		}

		return false;
	}
}
