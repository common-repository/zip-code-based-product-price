<?php

namespace Zip_Code_Based_Product_Price;

defined('ABSPATH') || exit;


/**
 * Class for admin panel
 * 
 * @since 1.0.0
 */
final class Admin {

	/**
	 * Hold product field instance
	 * 
	 * @var Product_Fields
	 */
	public $product_fields = null;

	/**
	 * Class contructor
	 */
	public function __construct() {
		include_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/admin/product-fields.php';
		$this->init();
	}

	/**
	 * Initilize all feature of admin
	 * 
	 * @since 1.0.0
	 */
	public function init() {
		$this->product_fields = new Admin\Product_Fields();

		add_action('init', [$this, 'handle_submit_form']);
		add_action('admin_menu', [$this, 'admin_menu'], 65);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	public function generate_css_file($post_data) {
		ob_start();
		$widget_styles = [];
		$widget_text_color = $post_data['widget_text_color'];
		if (!empty($widget_text_color)) {
			$widget_styles[] = sprintf('color: %s', $widget_text_color);
		}

		$widget_background_color = $post_data['widget_background_color'];
		if (!empty($widget_background_color)) {
			$widget_styles[] = sprintf('background-color: %s', $widget_background_color);
		}

		if (count($widget_styles) > 0) {
			echo '#zip-code-based-product-price-location-widget {' . esc_html(implode(';', $widget_styles)) . '}';
		}

		$widget_button_styles = [];
		$widget_button_text_color = $post_data['widget_button_text_color'];
		if (!empty($widget_button_text_color)) {
			$widget_button_styles[] = sprintf('color: %s!important', $widget_button_text_color);
		}

		$widget_button_background_color = $post_data['widget_button_background_color'];
		if (!empty($widget_button_background_color)) {
			$widget_button_styles[] = sprintf('background-color: %s!important', $widget_button_background_color);
		}

		if (count($widget_button_styles) > 0) {
			echo '.btn-zip-code-based-price {' . esc_html(implode(';', $widget_button_styles)) . '}';
		}

		$popup_styles = [];
		$popup_text_color = $post_data['popup_text_color'];
		if (!empty($popup_text_color)) {
			$popup_styles[] = sprintf('color: %s', $popup_text_color);
		}

		$popup_background_color = $post_data['popup_background_color'];
		if (!empty($popup_background_color)) {
			$popup_styles[] = sprintf('background-color: %s', $popup_background_color);
		}

		if (count($popup_styles) > 0) {
			echo '#zip-code-based-product-price-location-popup form {' . esc_html(implode(';', $popup_styles)) . '}';
		}

		$popup_submit_button_styles = [];
		$popup_submit_button_text_color = $post_data['popup_submit_button_text_color'];
		if (!empty($popup_submit_button_text_color)) {
			$popup_submit_button_styles[] = sprintf('color: %s!important', $popup_submit_button_text_color);
		}

		$popup_submit_button_background_color = $post_data['popup_submit_button_background_color'];
		if (!empty($popup_submit_button_background_color)) {
			$popup_submit_button_styles[] = sprintf('background-color: %s!important', $popup_submit_button_background_color);
		}

		if (count($popup_submit_button_styles) > 0) {
			echo '#zip-code-based-product-price-location-popup [type="submit"] {' . esc_html(implode(';', $popup_submit_button_styles)) . '}';
		}

		$styles = ob_get_clean();

		if ('reset' === $post_data['submit']) {
			$styles = '';
		}

		global $wp_filesystem;
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		$upload_dir = wp_upload_dir();
		$wp_filesystem->put_contents($upload_dir['basedir'] . '/zip-code-based-product-price.css', $styles);
	}

	/**
	 * Handle settings form
	 * 
	 * @since 1.0.0
	 */
	public function handle_submit_form() {
		if (!isset($_POST['_wpnonce'])) {
			return;
		}

		if (!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), '_nonce_zip_code_based_price_settings_form')) {
			return;
		}

		$settings_data = isset($_POST['settings']) && is_array($_POST['settings']) ? wc_clean($_POST['settings']) : array();
		$settings_data['submit'] = isset($_POST['submit']) ? sanitize_text_field($_POST['submit']) : '';

		$this->generate_css_file($settings_data);

		if ('reset' === $settings_data['submit']) {
			return delete_option('zip_code_based_product_price_settings');
		}

		unset($settings_data['submit']);
		update_option('zip_code_based_product_price_settings', $settings_data);
	}

	/**
	 * Enqueue script on backend
	 * 
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();
		preg_match('/zip-code-based-product-price/', $current_screen->id, $matches);
		if (empty($matches)) {
			return;
		}

		wp_enqueue_style('zip-code-based-product-price', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/css/admin.css', ['wp-color-picker'], ZIP_CODE_BASED_PRODUCT_PRICE_VERSION);
		wp_enqueue_script('zip-code-based-product-price', ZIP_CODE_BASED_PRODUCT_PRICE_URL . 'assets/js/admin-settings.js', ['jquery', 'wp-color-picker'], ZIP_CODE_BASED_PRODUCT_PRICE_VERSION, true);
		wp_localize_script('zip-code-based-product-price', 'zip_code_based_product_price', array(
			'i10n' => array(
				'reset_form_warning' => esc_html__('Do you want to reset your settings?', 'zip-code-based-product-price')
			)
		));
	}

	public function admin_menu() {
		add_submenu_page(
			'woocommerce',
			esc_html__('Zip Code Based Pricing Settings', 'zip-code-based-product-price'),
			esc_html__('Zip Based Price', 'zip-code-based-product-price'),
			'manage_options',
			'zip-code-based-product-price',
			array($this, 'settings_page')
		);
	}

	public function settings_page() {
		echo '<div class="wrap wrap-zip-code-based-product-price">';
		echo '<hr class="wp-header-end">';
		include_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/admin/settings-page.php';
		echo '</div>';
	}
}
