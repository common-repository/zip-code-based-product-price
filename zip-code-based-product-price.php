<?php

/**
 * Plugin Name: Zip Code Based Product Price
 * Description: Enhance your WooCommerce store with Zip Code Based Product Pricing for personalized shopping, dynamic pricing, and targeted marketing.
 * Version: 1.0.4
 * Author: Repon Hossain
 * Author URI: https://workwithrepon.com/
 * Text Domain: zip-code-based-product-price
 * Domain Path: /languages
 * 
 * WC requires at least: 5.0
 * 
 * License: GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


if (!defined('ABSPATH')) {
	exit;
}

define('ZIP_CODE_BASED_PRODUCT_PRICE', __FILE__);
define('ZIP_CODE_BASED_PRODUCT_PRICE_VERSION', '1.0.4');
define('ZIP_CODE_BASED_PRODUCT_PRICE_BASENAME', plugin_basename(__FILE__));
define('ZIP_CODE_BASED_PRODUCT_PRICE_URL', trailingslashit(plugins_url('/', __FILE__)));
define('ZIP_CODE_BASED_PRODUCT_PRICE_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('ZIP_CODE_BASED_PRODUCT_PRICE_MIN_PHP_VERSION', '7.4.3');

/**
 * Declare HPOS compatibility
 * 
 * @since 1.0.0
 */
add_action('before_woocommerce_init', function () {
	if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
	}
});

/**
 * Load textdomain of this plugin
 * 
 * @since 1.0.0
 */
function zip_code_based_product_price_load_textdomain() {
	load_plugin_textdomain('zip-code-based-product-price', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'zip_code_based_product_price_load_textdomain');

/**
 * Startup woocommerce request a shipping quote plugin
 * 
 * @since 1.0.0
 */
function zip_code_based_product_price_startup() {
	// Check for required PHP version
	if (version_compare(PHP_VERSION, ZIP_CODE_BASED_PRODUCT_PRICE_MIN_PHP_VERSION, '<')) {
		return add_action('admin_notices', 'zip_code_based_product_price_php_version_missing');
	}

	//Check WooCommerce activate
	if (!class_exists('WooCommerce', false)) {
		return add_action('admin_notices', 'zip_code_based_product_price_woocommerce_missing');
	}

	require_once ZIP_CODE_BASED_PRODUCT_PRICE_PATH . 'inc/main.php';
}

add_action('plugins_loaded', 'zip_code_based_product_price_startup');

/**
 * Admin notice for required minimum php version
 * 
 * @since 1.0.0
 */
function zip_code_based_product_price_php_version_missing() {

	$notice = sprintf(
		/* translators: $1: plugin name, $2: PHP, $3: PHP version */
		esc_html__('%1$s need %2$s version %3$s or greater.', 'zip-code-based-product-price'),
		'<strong>Zip Code Based Product Price</strong>',
		'<strong>PHP</strong>',
		ZIP_CODE_BASED_PRODUCT_PRICE_MIN_PHP_VERSION
	);

	printf('<div class="notice notice-warning"><p>%1$s</p></div>', wp_kses_post($notice));
}

/**
 * Admin notice for missing woocommerce
 * 
 * @since 1.0.0
 */
function zip_code_based_product_price_woocommerce_missing() {
	if (file_exists(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')) {
		$notice_title = esc_html__('Activate WooCommerce', 'zip-code-based-product-price');
		$notice_url = wp_nonce_url('plugins.php?action=activate&plugin=woocommerce/woocommerce.php&plugin_status=all&paged=1', 'activate-plugin_woocommerce/woocommerce.php');
	} else {
		$notice_title = esc_html__('Install WooCommerce', 'zip-code-based-product-price');
		$notice_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=woocommerce'), 'install-plugin_woocommerce');
	}

	$notice = sprintf(
		/* translators: $1: plugin name, $2: WooCommerce, $3: link for install or activate woocommerce */
		esc_html__('%1$s need %2$s to be installed and activated to function properly. %3$s', 'zip-code-based-product-price'),
		'<strong>Zip Code Based Product Price</strong>',
		'<strong>WooCommerce</strong>',
		'<a href="' . esc_url($notice_url) . '">' . $notice_title . '</a>'
	);

	printf('<div class="notice notice-warning"><p>%1$s</p></div>', wp_kses_post($notice));
}


require __DIR__ . '/vendor/autoload.php';


/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_zip_code_based_product_price() {

    if ( ! class_exists( 'Appsero\Client' ) ) {
      require_once __DIR__ . '/appsero/src/Client.php';
    }

    $client = new Appsero\Client( '4dec1b3f-57d7-4ca7-b9ff-eaebcfb2ef64', 'Zip Code Based Product Price', __FILE__ );

    // Active insights
    $client->insights()->init();

}

appsero_init_tracker_zip_code_based_product_price();
