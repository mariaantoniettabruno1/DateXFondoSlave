<?php

namespace ZionBuilderPro\Integrations\WooCommerce;

use ZionBuilder\Integrations\IBaseIntegration;
use ZionBuilder\Plugin;

class WooCommerce implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'woocommerce';
	}

	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return class_exists( 'WooCommerce' );
	}


	/**
	 * Main class constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'zionbuilder/elements_manager/register_elements', [ $this, 'register_elements' ] );
		add_filter( 'zionbuilder/elements/categories', [ $this, 'add_elements_categories' ] );
		add_filter( 'zionbuilder/preview/app/css_classes', [ $this, 'add_preview_app_css_classes' ] );
		add_filter( 'zionbuilder/single/area_class', [ $this, 'add_content_area_classes' ], 10, 2 );
		add_filter( 'zionbuilder-pro/conditions/get_conditions', [ $this, 'add_theme_builder_conditions' ] );

		add_filter(
			'woocommerce_add_to_cart_fragments',
			function ( $fragments ) {
				global $woocommerce;
				$cart_items_count                = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
				$fragments['.zb-miniCart-count'] = "<span class='zb-miniCart-count'>{$cart_items_count}</span>";

				$subtotal                           = is_object( WC()->cart ) ? WC()->cart->get_cart_subtotal() : 0;
				$fragments['.zb-miniCart-subtotal'] = "<span class='zb-miniCart-subtotal'>{$subtotal}</span>";

				return $fragments;
			}
		);
	}

	public function add_content_area_classes( $classes, $area_id ) {
		global $product;

		if ( \is_product() && $product && function_exists( 'wc_get_product_class' ) ) {
			$classes = array_merge( $classes, wc_get_product_class( '', $product ) );
		}

		return $classes;
	}

	public function add_preview_app_css_classes( $classes ) {
		global $product;

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( \is_product() && $product && function_exists( 'wc_get_product_class' ) ) {
			$classes = array_merge( $classes, wc_get_product_class( '', $product ) );
		} else {
			$classes = array_merge( $classes, [ 'woocommerce', 'product', 'single-product' ] );
		}

		return $classes;
	}


	/**
	 * Adds the WooCommerce category to the elements category list
	 *
	 * @since 2.0.0
	 *
	 * @param array $categories
	 * @see zionbuilder/elements/categories filter
	 *
	 * @return array
	 */
	public function add_elements_categories( $categories ) {
		$categories[] = [
			'id'   => 'woocommerce',
			'name' => __( 'WooCommerce', 'zionbuilder-pro' ),
		];

		return $categories;
	}


	/**
	 * Will register all WooCommerce elements
	 *
	 * @since 2.0.0
	 *
	 * @param \ZionBuilder\Elements\Manager $elements_manager
	 * @return void
	 */
	public function register_elements( $elements_manager ) {
		$elements = [
			// Product
			'ProductAdditionalInfo\ProductAdditionalInfo',
			'ProductAddToCart\ProductAddToCart',
			'ProductDescription\ProductDescription',
			'ProductImages\ProductImages',
			'ProductMeta\ProductMeta',
			'ProductPrice\ProductPrice',
			'ProductRating\ProductRating',
			'ProductStock\ProductStock',
			'ProductTabs\ProductTabs',
			'ProductTitle\ProductTitle',
			'ProductReviewsForm\ProductReviewsForm',
			'ProductBreadcrumbs\ProductBreadcrumbs',
			'ProductRelated\ProductRelated',
			'ProductUpSells\ProductUpSells',

			// Archive
			'ArchiveAddToCart\ArchiveAddToCart',

			// Cart
			'CartCrossSells\CartCrossSells',
			'CartTotals\CartTotals',
			'CartProducts\CartProducts',

			// Checkout
			'CheckoutCoupon\CheckoutCoupon',
			'CheckoutFormWrapper\CheckoutFormWrapper',
			'CheckoutLogin\CheckoutLogin',
			'CheckoutCustomerDetails\CheckoutCustomerDetails',
			'CheckoutOrderReview\CheckoutOrderReview',
			'CheckoutThankYou\CheckoutThankYou',

			// General
			'Notices\Notices',
			'MiniCart\MiniCart',
		];

		foreach ( $elements as $element_name ) {
			// Normalize class name
			$class_name = str_replace( '-', '_', $element_name );
			$class_name = 'ZionBuilderPro\\Integrations\\WooCommerce\\Elements\\' . $class_name;
			$elements_manager->register_element( new $class_name() );
		}
	}

	public function add_theme_builder_conditions( $conditions_manager ) {
		$conditions_manager->register_condition(
			[
				'id'         => 'woocommerce_thank_you_page',
				'name'       => esc_html__( 'Thank you page', 'zionbuilder-pro' ),
				'category'   => 'product',
				'validation' => 'is_order_received_page',
			]
		);
	}
}
