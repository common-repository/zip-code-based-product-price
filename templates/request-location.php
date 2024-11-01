<?php

namespace Zip_Code_Based_Product_Price;

if (!defined('ABSPATH')) {
	exit;
}

$settings = Utils::get_settings();

$set_location_text = html_entity_decode($settings['single_product_set_location_text'], ENT_QUOTES);
if (empty($set_location_text)) {
	return;
}

add_shortcode('location_popup_link', array('\Zip_Code_Based_Product_Price\Utils', 'shortcode_location_popup_link'));
echo do_shortcode(wp_kses_post($set_location_text));
remove_shortcode('location_popup_link');
