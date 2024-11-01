<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Main Class 
 */
final class Main {

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
	 * Hold admin instance
	 * 
	 * @var Admin
	 */
	public $admin = null;

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', 'zip-code-based-product-price'), '1.0.0');
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', 'zip-code-based-product-price'), '1.0.0');
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->include_files();
		$this->init();
	}

	/**
	 * Load plugin files
	 * 
	 * @version 1.0.0
	 * @return void
	 */
	public function include_files() {
		require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/utils.php';
		require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/class-location.php';
		require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/class-product-helper.php';
		require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/class-woocommerce.php';

		if (is_admin()) {
			require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/admin/admin.php';
		}
	}

	/**
	 * Initialize this plugin
	 * 
	 * @since 1.0.0
	 */
	public function init() {
		add_action('init', [$this, 'handle_clear_location']);
		add_filter('plugin_action_links', array($this, 'add_plugin_links'), 10, 2);
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('wp_footer', [$this, 'add_location_settings_widget']);
		add_action('wp_footer', [$this, 'set_location_popup']);


		if (is_admin()) {
			$this->admin = new Admin();
		}

		WooCommerce::get_instance();
	}

	/**
	 * Clear location data from cookie and cart
	 */
	public function handle_clear_location() {
		if (!isset($_GET['_nonce'])) {
			return;
		}

		if (!wp_verify_nonce(wc_clean($_GET['_nonce']), 'zip_code_based_price_clear_location')) {
			return;
		}

		setcookie('zip_code_based_product_price_location', null, -1, '/');
		unset($_COOKIE['zip_code_based_product_price_location']);

		wp_redirect(esc_url_raw(remove_query_arg('_nonce'))); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}

	/**
	 * Add links at the plugin action
	 * 
	 * @since 1.0.0
	 */
	public function add_plugin_links($actions, $plugin_file) {
		if (ZIP_CODE_BASED_PRODUCT_PRICE_BASENAME == $plugin_file) {
			$new_links = array(
				'settings' => sprintf('<a href="%s">%s</a>', menu_page_url('zip-code-based-product-price', false), esc_html__('Settings', 'zip-code-based-product-price')),
			);

			$actions = array_merge($new_links, $actions);
		}

		return $actions;
	}

	/**
	 * Enqueue scripts on frontend
	 * 
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$settings = Utils::get_settings();

		wp_enqueue_style('zip-code-based-product-price', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/css/frontend.css', [], ZIP_CODE_BASED_PRODUCT_PRICE_VERSION);

		$upload_dir = wp_upload_dir();
		$generated_css_file = $upload_dir['basedir'] . '/zip-code-based-product-price.css';


		if (file_exists($generated_css_file)) {
			wp_enqueue_style('zip-code-based-product-price-generated', $upload_dir['baseurl'] . '/zip-code-based-product-price.css', [], filemtime($generated_css_file));
		}

		$location = Location::get_instance();

		wp_enqueue_script('zip-code-based-product-price', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/js/frontend.js', ['jquery', 'js-cookie'], ZIP_CODE_BASED_PRODUCT_PRICE_VERSION, true);
		wp_localize_script('zip-code-based-product-price', 'zip_code_based_product_price', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'zipcode' => $location->get_zip_code(),
			'i10n' => array(
				'confirm_clear_location' => esc_html($settings['widget_clear_location_warning_text']),
				'error_country_code_missing' => esc_html($settings['popup_country_field_error_text']),
				'error_zip_code_missing' => esc_html($settings['popup_zip_code_field_error_text'])
			)
		));
	}

	/**
	 * Add location settings widget on frontend
	 * 
	 * @since 1.0.0
	 */
	public function add_location_settings_widget() {
		$location = Location::get_instance();

		$settings = Utils::get_settings();

		$description_text = $settings['widget_set_your_location_text'];

		if ($location->has_location()) {
			$description_text = $settings['widget_location_available_text'];

			$description_text = str_replace('[zip_code]', $location->get_zip_code(), $description_text);

			$countries = WC()->countries->get_countries();
			$country = isset($countries[$location->get_country_code()]) ? $countries[$location->get_country_code()] : esc_html__('Unknown', 'zip-code-based-product-price');
			$description_text = str_replace('[country]', $country, $description_text);
		}

		echo '<div id="zip-code-based-product-price-location-widget">';
		echo '<p>' . wp_kses_post($description_text) . '</p>';

		if ('cookie' !== $location->get_soruce()) {
			$button_label = $settings['widget_set_location_button_label'];
			echo '<a class="btn-zip-code-based-price btn-open-zip-code-price-location-modal" href="#">' . esc_html($button_label) . '</a>';
		} else {
			$button_label = $settings['widget_clear_location_button_label'];
			printf('<a class="btn-zip-code-based-price btn-zip-code-price-clear-location" href="%s">%s</a>', esc_url(add_query_arg('_nonce', wp_create_nonce('zip_code_based_price_clear_location'))), esc_html($button_label));
		}

		if ('yes' !== $settings['widget_hide_close_button']) {
			echo '<a class="btn-close-widget" href="#"></a>';
		};

		echo '</div>';
	}

	/**
	 * Output popup for set customer location - Country, Zip code, etc
	 * 
	 * @since 1.0.0
	 */
	public function set_location_popup() {
		$location = Location::get_instance();
		if ('cookie' == $location->get_soruce()) {
			return;
		}

		$settings = Utils::get_settings();

		$class = '';
		if ($location->need_location() && 'yes' == $settings['show_popup_immediately']) {
			$class = 'opened';
		}

		echo '<div id="zip-code-based-product-price-location-popup" class="' . esc_attr($class) . '">';
		Utils::get_template('location-popup', array(
			'settings' => $settings,
			'zip_code' => $location->get_zip_code(),
			'selected_country' => $location->get_country_code(),
		));
		echo '</div>';
	}
}

Main::get_instance();
