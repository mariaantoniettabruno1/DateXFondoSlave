<?php

namespace ZionBuilderPro\ElementConditions\Conditions;

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class WooCommerceConditionals extends ConditionsBase {
	public static function init_conditions() {
		self::register_groups();
		self::register_conditions();
	}

	public static function register_groups() {
		// Register groups
		ElementConditions::register_condition_group(
			'woocommerce',
			[
				'name' => esc_html__( 'WooCommerce Conditionals', 'zionbuilder-pro' ),
			]
		);
	}

	public static function register_conditions() {
		//#! woocommerce/customer_total_orders
		ElementConditions::register_condition(
			'woocommerce/customer_total_orders',
			[
				'group'    => 'woocommerce',
				'name'     => esc_html__( 'Customer total orders', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_customer_total_orders' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
								'greater',
								'lower',
								'greater_or_equal',
								'lower_or_equal',
							]
						),
					],
					'value'    => [
						'type' => 'text',
					],
				],
			]
		);

		//#! woocommerce/customer_total_spent
		ElementConditions::register_condition(
			'woocommerce/customer_total_spent',
			[
				'group'    => 'woocommerce',
				'name'     => esc_html__( 'Customer total spent', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_customer_total_spent' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
								'greater',
								'lower',
								'greater_or_equal',
								'lower_or_equal',
							]
						),
					],
					'value'    => [
						'type' => 'text',
					],
				],
			]
		);

		//#! woocommerce/cart
		ElementConditions::register_condition(
			'woocommerce/cart',
			[
				'group'    => 'woocommerce',
				'name'     => esc_html__( 'Cart', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_cart_empty' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'is_empty', 'is_not_empty', ] ),
					],
				],
			]
		);

		//#! woocommerce/product_in_cart
		ElementConditions::register_condition(
			'woocommerce/product_in_cart',
			[
				'group'    => 'woocommerce',
				'name'     => esc_html__( 'Products from cart', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_product_in_cart' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'include', 'does_not_include', ] ),
					],
					'post_id'  => [
						'type'       => 'select',
						'rest'       => 'v1/conditions/post/post?post_type=product',
						'filterable' => true,
					],

				],
			]
		);

		//#! woocommerce/cart_total_value
		ElementConditions::register_condition(
			'woocommerce/cart_total_value',
			[
				'group'    => 'woocommerce',
				'name'     => esc_html__( 'Cart total value', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_cart_total_value' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators(
							[
								'equals',
								'not_equals',
								'greater',
								'lower',
								'greater_or_equal',
								'lower_or_equal',
							]
						),
					],
					'value'    => [
						'type' => 'text',
					],
				],
			]
		);

		//#! woocommerce/cart_total_value
		ElementConditions::register_condition(
			'woocommerce/product_type',
			[
				'group'    => 'woocommerce',
				'name'     => esc_html__( 'Product', 'zionbuilder-pro' ),
				'callback' => [ get_class(), 'validate_condition_product_type' ],
				'form'     => [
					'operator' => [
						'type'    => 'select',
						'options' => self::get_operators( [ 'equals', ] ),
					],
					'value'    => [
						'type'    => 'select',
						'options' => [
							[
								'name' => esc_html__( 'Is on sale', 'zionbuilder-pro' ),
								'id'   => 'is_on_sale',
							],
							[
								'name' => esc_html__( 'Has short description', 'zionbuilder-pro' ),
								'id'   => 'has_short_description',
							],
							[
								'name' => esc_html__( 'Has description', 'zionbuilder-pro' ),
								'id'   => 'has_description',
							],
							[
								'name' => esc_html__( 'Has reviews', 'zionbuilder-pro' ),
								'id'   => 'has_reviews',
							],
						],
					],
				],
			]
		);
	}

	public static function validate_customer_total_orders( array $settings ) {
		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => intval( $settings[ 'value' ] ),
				'current_value' => self::getCountCustomerTotalOrders(),
			]
		);
	}

	public static function validate_customer_total_spent( array $settings ) {
		if ( ! isset( $settings[ 'value' ] ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => intval( $settings[ 'value' ] ),
				'current_value' => self::getCustomerTotalSpent(),
			]
		);
	}

	public static function validate_cart_empty( array $settings ) {
		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => null,
				'current_value' => WC()->cart->get_cart(),
			]
		);
	}

	public static function validate_product_in_cart( array $settings ) {
		if ( ! isset( $settings[ 'post_id' ] ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => $settings[ 'post_id' ],
				'current_value' => self::getItemsFromCart(),
			]
		);
	}

	public static function validate_cart_total_value( array $settings ) {
		if ( ! isset( $settings[ 'value' ] ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => intval( $settings[ 'value' ] ),
				'current_value' => self::getCountCartTotal(),
			]
		);
	}

	public static function validate_condition_product_type( array $settings ) {
		if ( empty( $settings[ 'value' ] ) || ! is_singular( 'product' ) ) {
			return false;
		}

		$current_value = null;
		$product       = self::get_post();
		$wcProduct     = new \WC_Product( $product->ID );

		switch ( $settings[ 'value' ] ) {
			case 'is_on_sale':
				$current_value = $wcProduct->is_on_sale();
				break;
			case 'has_short_description':
				$current_value = ! empty( $wcProduct->get_short_description() );
				break;
			case 'has_description':
				$current_value = ! empty( $wcProduct->get_description() );
				break;
			case 'has_reviews':
				$current_value = ! empty( $wcProduct->get_review_count() );
				break;
		}

		if ( is_null( $current_value ) ) {
			return false;
		}

		return self::validate(
			[
				'operator'      => $settings[ 'operator' ],
				'saved_value'   => true,
				'current_value' => $current_value,
			]
		);
	}

	/**
	 * Helper method to retrieve current user's total orders
	 * @return int
	 */
	private static function getCountCustomerTotalOrders() {
		$userID = ( is_user_logged_in() ? wp_get_current_user()->ID : 0 );
		if ( empty( $userID ) ) {
			error_log( 'NO ORDERS FOR: ' . $userID );

			return 0;
		}
		$args   = [
			'customer_id' => $userID,
		];
		$orders = wc_get_orders( $args );

		return count( $orders );
	}

	/**
	 * Helper method to retrieve current user's total spent on orders
	 * @return int
	 */
	private static function getCustomerTotalSpent() {
		$userID = ( is_user_logged_in() ? wp_get_current_user()->ID : 0 );
		if ( empty( $userID ) ) {
			error_log( 'NO ORDERS FOR: ' . $userID );

			return 0;
		}
		$args   = [
			'customer_id' => $userID,
		];
		$orders = wc_get_orders( $args );
		if ( empty( $orders ) ) {
			return 0;
		}

		// One implementation of how to sum up all the totals
		return array_reduce( $orders, function ( $total, $order ) {
			$total += (float) $order->get_total();

			return $total;
		}, 0.0 );
	}

	/**
	 * Retrieve the items from the cart
	 * @return array
	 */
	private static function getItemsFromCart() {
		$items     = [];
		$cartItems = WC()->cart->get_cart();
		if ( empty( $cartItems ) ) {
			return $items;
		}
		// Loop over $cart items
		foreach ( $cartItems as $cart_item_key => $cart_item ) {
			$items[] = $cart_item[ 'product_id' ];
		}

		return $items;
	}

	/**
	 * Retrieve cart's total
	 * @return float
	 */
	private static function getCountCartTotal() {
		return WC()->cart->get_cart_contents_total();
	}
}
