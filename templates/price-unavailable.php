<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

$settings = Utils::get_settings();

$price_unavailable_text = $settings['single_product_price_unavailable_text'];
if (!empty($price_unavailable_text)) {
	echo '<p class="zip-code-based-product-price-unavailable">' . wp_kses_post($price_unavailable_text) . '</p>';
}
