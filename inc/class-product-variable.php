<?php

namespace Zip_Code_Based_Product_Price\Product;

use Zip_Code_Based_Product_Price\Product_Helper;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class for woocommerce variable product
 */
final class Variable {

	use Product_Helper;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter('woocommerce_product_variation_get_price', [$this, 'set_price'], 1000, 2);
		add_filter('woocommerce_product_variation_get_sale_price', [$this, 'set_sales_price'], 1000, 2);
		add_filter('woocommerce_product_variation_get_regular_price', [$this, 'set_regular_price'], 1000, 2);

		add_filter('woocommerce_variation_prices_price', [$this, 'set_price'], 1000, 2);
		add_filter('woocommerce_variation_prices_sale_price', [$this, 'set_sales_price'], 1000, 2);
		add_filter('woocommerce_variation_prices_regular_price', [$this, 'set_regular_price'], 1000, 2);

		add_filter('woocommerce_variation_prices', [$this, 'variation_price_range'], 100, 3);
	}

	/**
	 * Update the price range value of variable product
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public function variation_price_range($prices, $product, $for_display) {
		$prices_array = array(
			'price'         => array(),
			'regular_price' => array(),
			'sale_price'    => array(),
		);

		$variation_ids = $product->get_visible_children();
		foreach ($variation_ids as $variation_id) {
			$variation = wc_get_product($variation_id);

			if ($variation) {
				$price         = apply_filters('woocommerce_variation_prices_price', $variation->get_price('edit'), $variation, $product);
				$regular_price = apply_filters('woocommerce_variation_prices_regular_price', $variation->get_regular_price('edit'), $variation, $product);
				$sale_price    = apply_filters('woocommerce_variation_prices_sale_price', $variation->get_sale_price('edit'), $variation, $product);

				// Skip empty prices.
				if ('' === $price) {
					continue;
				}

				// If sale price does not equal price, the product is not yet on sale.
				if ($sale_price === $regular_price || $sale_price !== $price) {
					$sale_price = $regular_price;
				}

				// If we are getting prices for display, we need to account for taxes.
				if ($for_display) {
					if ('incl' === get_option('woocommerce_tax_display_shop')) {
						$price = '' === $price ? '' : wc_get_price_including_tax($variation, array(
							'qty'   => 1,
							'price' => $price,
						));
						$regular_price = '' === $regular_price ? '' : wc_get_price_including_tax($variation, array(
							'qty'   => 1,
							'price' => $regular_price,
						));
						$sale_price    = '' === $sale_price ? '' : wc_get_price_including_tax($variation, array(
							'qty'   => 1,
							'price' => $sale_price,
						));
					} else {
						$price = '' === $price ? '' : wc_get_price_excluding_tax($variation, array(
							'qty'   => 1,
							'price' => $price,
						));

						$regular_price = '' === $regular_price ? '' : wc_get_price_excluding_tax($variation, array(
							'qty'   => 1,
							'price' => $regular_price,
						));

						$sale_price    = '' === $sale_price ? '' : wc_get_price_excluding_tax($variation, array(
							'qty'   => 1,
							'price' => $sale_price,
						));
					}
				}

				$prices_array['price'][$variation_id]         = wc_format_decimal($price, wc_get_price_decimals());
				$prices_array['regular_price'][$variation_id] = wc_format_decimal($regular_price, wc_get_price_decimals());
				$prices_array['sale_price'][$variation_id]    = wc_format_decimal($sale_price, wc_get_price_decimals());

				$prices_array = apply_filters('woocommerce_variation_prices_array', $prices_array, $variation, $for_display);
			}
		}

		return $prices_array;
	}
}
