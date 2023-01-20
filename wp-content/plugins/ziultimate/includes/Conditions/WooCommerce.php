<?php
namespace ZiUltimate\Conditions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use ZionBuilderPro\ElementConditions\ConditionsBase;
use ZionBuilderPro\ElementConditions\ElementConditions;

class WooCommerce extends ConditionsBase {

	public static function init_conditions() {
		self::register_groups_for_woo();
		self::register_conditions_for_woo();
	}

	public static function register_groups_for_woo() {
		// Register groups
		ElementConditions::register_condition_group('zuwoo', [
			'name' => esc_html__('ZiUltimate Woo', 'ziultimate')
		]);
	}

	/*public static function get_yesno_operators( $slug ) {
		return [
			'operator' => [
				'type' => 'select',
				'options' => self::get_operators([
					'equals',
					'not_equals'
				]),
			],
			"{$slug}" => [
				'type' 		=> 'select',
				'options' 	=> [
					[
						'id' 	=> 'yes',
						'name' 	=> esc_html__('Yes', 'zionbuilder'),
					],
					[
						'id' 	=> 'no',
						'name' 	=> esc_html__('No', 'zionbuilder'),
					]
				]
			]
		];
	}*/

	public static function get_yesno_dropdown( $slug ) {
		return [
			"{$slug}" => [
				'type' 		=> 'select',
				'options' 	=> [
					[
						'id' 	=> 'yes',
						'name' 	=> esc_html__('Yes', 'zionbuilder'),
					],
					[
						'id' 	=> 'no',
						'name' 	=> esc_html__('No', 'zionbuilder'),
					]
				]
			]
		];
	}

	public static function register_conditions_for_woo() {
		ElementConditions::register_condition('zuwoo/isshop', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is shop page', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_shop_page'],
			'form' 		=> self::get_yesno_dropdown('isshop')
		]);

		ElementConditions::register_condition('zuwoo/iscart', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is cart page', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_cart_page'],
			'form' 		=> self::get_yesno_dropdown('iscart')
		]);

		ElementConditions::register_condition('zuwoo/ischeckout', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is checkout page', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_checkout_page'],
			'form' 		=> self::get_yesno_dropdown('ischeckout')
		]);

		ElementConditions::register_condition('zuwoo/issingle', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is single product page', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_single_page'],
			'form' 		=> self::get_yesno_dropdown('issingle')
		]);

		ElementConditions::register_condition('zuwoo/isaccount', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is account page', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_account_page'],
			'form' 		=> self::get_yesno_dropdown('isaccount')
		]);

		ElementConditions::register_condition('zuwoo/featured', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is featured product', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_featured_product'],
			'form' 		=> self::get_yesno_dropdown('featured')
		]);

		ElementConditions::register_condition('zuwoo/virtual', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is virtual product', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_virtual_product'],
			'form' 		=> self::get_yesno_dropdown('virtual')
		]);

		ElementConditions::register_condition('zuwoo/downloadable', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Is product downloadable', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_is_product_downloadable'],
			'form' 		=> self::get_yesno_dropdown('downloadable')
		]);

		ElementConditions::register_condition('zuwoo/stockstatus', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Stock status', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_stock_status'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals'
					]),
				],
				"stockstatus" => [
					'type' 		=> 'select',
					'options' 	=> [
						[
							'id' 	=> 'instock',
							'name' 	=> __( 'In stock', 'woocommerce' ),
						],
						[
							'id' 	=> 'outofstock',
							'name' 	=> __( 'Out of stock', 'woocommerce' ),
						],
						[
							'id' 	=> 'onbackorder',
							'name' 	=> __( 'On backorder', 'woocommerce' ),
						]
					]
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/cart_total', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Cart total', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_cart_total'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
						'greater',
						'lower',
						'greater_or_equal',
						'lower_or_equal'
					]),
				],
				'cart_total' => [
					'type' => 'text',
					'placeholder' => esc_html__('Enter price', 'ziultimate')
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/cart_counter', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Cart counter', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_cart_counter'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
						'greater',
						'lower',
						'greater_or_equal',
						'lower_or_equal'
					]),
				],
				'cart_counter' => [
					'type' => 'text',
					'placeholder' => esc_html__('Enter integer value', 'ziultimate')
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/prdtype', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Product type', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_type'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals'
					]),
				],
				"prdtype" => [
					'type' 		=> 'select',
					'options' 	=> [
						[
							'id' 	=> 'simple',
							'name' 	=> __( 'Simple', 'ziultimate' ),
						],
						[
							'id' 	=> 'grouped',
							'name' 	=> __( 'Grouped', 'ziultimate' ),
						],
						[
							'id' 	=> 'variable',
							'name' 	=> __( 'Variable', 'ziultimate' ),
						],
						[
							'id' 	=> 'external',
							'name' 	=> __( 'External', 'ziultimate' ),
						]
					]
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/instock', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Product in stock', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_in_stock'],
			'form' 		=> self::get_yesno_dropdown('instock')
		]);

		ElementConditions::register_condition('zuwoo/incart', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Selected product in cart', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_in_cart'],
			'form' 		=> [
				'product_id' => [
					'type' => 'select',
					'rest' => 'v1/conditions/post/post?post_type=product',
					'filterable' => true
				],
				"in_cart" => [
					'type' 		=> 'select',
					'options' 	=> [
						[
							'id' 	=> 'yes',
							'name' 	=> esc_html__('Yes', 'zionbuilder'),
						],
						[
							'id' 	=> 'no',
							'name' 	=> esc_html__('No', 'zionbuilder'),
						]
					]
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/visible', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Product is visible', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_is_visible'],
			'form' 		=> self::get_yesno_dropdown('visible')
		]);

		ElementConditions::register_condition('zuwoo/purchasable', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Product is purchasable', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_is_purchasable'],
			'form' 		=> self::get_yesno_dropdown('purchasable')
		]);

		ElementConditions::register_condition('zuwoo/has_image', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Product has image', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_has_image'],
			'form' 		=> self::get_yesno_dropdown('has_image')
		]);

		ElementConditions::register_condition('zuwoo/has_gallery', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Product gallery images', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_has_gallery'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'is_set',
						'is_not_set'
					]),
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/hasreviews', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Product has reviews', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_has_reviews'],
			'form' 		=> self::get_yesno_dropdown('hasreviews')
		]);

		ElementConditions::register_condition('zuwoo/product_purchased', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Has purchased product', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_already_purchased_product'],
			'form' 		=> [
				'product_id' => [
					'type' => 'select',
					'rest' => 'v1/conditions/post/post?post_type=product',
					'filterable' => true
				],
				"is_purchased" => [
					'type' 		=> 'select',
					'options' 	=> [
						[
							'id' 	=> 'yes',
							'name' 	=> esc_html__('Yes', 'zionbuilder'),
						],
						[
							'id' 	=> 'no',
							'name' 	=> esc_html__('No', 'zionbuilder'),
						]
					]
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/has_featured_product', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Has featured product', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_featured_product'],
			'form' 		=> self::get_yesno_dropdown('has_featured_product')
		]);

		ElementConditions::register_condition('zuwoo/onsale_product', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Has on-sale product', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_onsale_product'],
			'form' 		=> self::get_yesno_dropdown('onsale_product')
		]);

		ElementConditions::register_condition('zuwoo/backorders', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Has backorder', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_backorders'],
			'form' 		=> self::get_yesno_dropdown('backorders')
		]);

		ElementConditions::register_condition('zuwoo/has_sku', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Sku', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_sku'],
			'form' 		=> [
				'operator' => [
					'type' 		=> 'select',
					'options' 	=> self::get_operators([
						'equals',
						'not_equals',
						'starts_with',
						'ends_with',
						'contains',
						'does_not_contain',
						'is_set',
						'is_not_set'
					]),
				],
				'sku' => [
					'type' 			=> 'text',
					'placeholder' 	=> esc_html__('sku value', 'ziultimate'),
					'requires' 		=> [
						[
							'option_id' => 'operator',
							'operator' 	=> 'not_in',
							'value' 	=> [
								'is_set',
								'is_not_set'
							]
						]
					]
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/upsell', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Upsell products', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_upsell_products'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'is_set',
						'is_not_set'
					]),
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/cross_sell', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Cross-sell products', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_has_cross_sell_products'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'is_set',
						'is_not_set'
					]),
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/totalreviews', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Number of reviews', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_product_reviews'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
						'greater',
						'lower',
						'greater_or_equal',
						'lower_or_equal'
					]),
				],
				'totalreviews' => [
					'type' => 'text',
					'placeholder' => esc_html__('Enter numeric value', 'ziultimate')
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/endpoint', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Endpoint', 'woocommerce'),
			'callback' 	=> [get_class(), 'validate_endpoint'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals'
					]),
				],
				"endpoint" => [
					'type' 		=> 'select',
					'options' 	=> [
						[
							'id' 	=> 'any',
							'name' 	=> __( 'Any', 'woocommerce' ),
						],
						[
							'id' 	=> 'orderpay',
							'name' 	=> __( 'Order Pay', 'woocommerce' ),
						],
						[
							'id' 	=> 'orderreceived',
							'name' 	=> __( 'Order Received', 'woocommerce' ),
						],
						[
							'id' 	=> 'vieworder',
							'name' 	=> __( 'View Order', 'woocommerce' ),
						],
						[
							'id' 	=> 'editaccount',
							'name' 	=> __( 'Edit Account', 'woocommerce' ),
						],
						[
							'id' 	=> 'editaddress',
							'name' 	=> __( 'Edit Address', 'woocommerce' ),
						],
						[
							'id' 	=> 'paymentmethod',
							'name' 	=> __( 'Add Payment Method', 'woocommerce' ),
						],
						[
							'id' 	=> 'customerlogout',
							'name' 	=> __( 'Customer Logout', 'woocommerce' ),
						],
						[
							'id' 	=> 'lostpassword',
							'name' 	=> __( 'Lost Password', 'woocommerce' ),
						]
					]
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/recently_viewed', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Recently viewed product', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_recently_viewed_product'],
			'form' 		=> self::get_yesno_dropdown('recently_viewed')
		]);

		ElementConditions::register_condition('zuwoo/bought_one', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Bought at least 1 product', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_bought_one_product'],
			'form' 		=> self::get_yesno_dropdown('bought_one')
		]);

		ElementConditions::register_condition('zuwoo/cust_total_orders', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Customer total orders', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_customer_total_orders'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
						'greater',
						'lower',
						'greater_or_equal',
						'lower_or_equal'
					]),
				],
				'total_orders' => [
					'type' => 'text'
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/cust_total_spent', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Customer total spent', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_customer_total_spent'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
						'greater',
						'lower',
						'greater_or_equal',
						'lower_or_equal'
					]),
				],
				'total_spent' => [
					'type' => 'text'
				]
			]
		]);

		ElementConditions::register_condition('zuwoo/cust_ttl_prds', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Customer total products', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_customer_total_products'],
			'form' 		=> [
				'operator' => [
					'type' => 'select',
					'options' => self::get_operators([
						'equals',
						'not_equals',
						'greater',
						'lower',
						'greater_or_equal',
						'lower_or_equal'
					]),
				],
				'total_products' => [
					'type' => 'text'
				]
			]
		]);

		/*ElementConditions::register_condition('zuwoo/discount_apply', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Discount Applied?', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_discount_apply'],
			'form' 		=> self::get_yesno_operators('discount_apply')
		]);

		ElementConditions::register_condition('zuwoo/fees_apply', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Fees Applied?', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_fees_apply'],
			'form' 		=> self::get_yesno_operators('fees_apply')
		]);

		ElementConditions::register_condition('zuwoo/shipping_apply', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Shipping Applied?', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_shipping_apply'],
			'form' 		=> self::get_yesno_operators('shipping_apply')
		]);

		ElementConditions::register_condition('zuwoo/tax_apply', [
			'group' 	=> 'zuwoo',
			'name' 		=> esc_html__('Tax Applied?', 'ziultimate'),
			'callback' 	=> [get_class(), 'validate_tax_apply'],
			'form' 		=> self::get_yesno_operators('tax_apply')
		]);*/
	}

	public static function validate_is_shop_page( $settings ) {
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['isshop'] == 'yes' ? true : false,
			'current_value' => is_shop()
		]);
	}

	public static function validate_is_cart_page( $settings ) {
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['iscart'] == 'yes' ? true : false,
			'current_value' => is_cart()
		]);
	}

	public static function validate_is_checkout_page( $settings ) {
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['ischeckout'] == 'yes' ? true : false,
			'current_value' => is_checkout()
		]);
	}

	public static function validate_is_single_page( $settings ) {
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['issingle'] == 'yes' ? true : false,
			'current_value' => is_product()
		]);
	}

	public static function validate_is_account_page( $settings ) {
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['isaccount'] == 'yes' ? true : false,
			'current_value' => is_account_page()
		]);
	}

	public static function validate_is_featured_product( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['featured'] == 'yes' ? true : false,
				'current_value' => $product->is_featured()
			]);
		}
		
		return false;
	}

	public static function validate_is_virtual_product( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['virtual'] == 'yes' ? true : false,
				'current_value' => $product->is_virtual()
			]);
		}
		
		return false;
	}

	public static function validate_is_product_downloadable( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['downloadable'] == 'yes' ? true : false,
				'current_value' => $product->is_downloadable()
			]);
		}
		
		return false;
	}

	public static function validate_product_in_stock( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['instock'] == 'yes' ? true : false,
				'current_value' => $product->is_in_stock()
			]);
		}
		
		return false;
	}

	public static function validate_product_in_cart( $settings ) {
		$product_in_cart = WC()->cart ? WC()->cart->generate_cart_id( intval( $settings['product_id'] ) ) : '';
		if ( empty( $product_in_cart ) || is_null( $product_in_cart ) ) {
			return false;
		}

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['in_cart'] == 'yes' ? true : false,
			'current_value' => WC()->cart ? WC()->cart->find_product_in_cart( $product_in_cart ) : false
		]);
	}

	public static function validate_already_purchased_product( $settings ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$customer = wp_get_current_user();
		$customer_bought_product = wc_customer_bought_product( $customer->user_email, $customer->ID, intval( $settings['product_id'] ) );

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['is_purchased'] == 'yes' ? true : false,
			'current_value' => $customer_bought_product
		]);
	}

	public static function validate_cart_total( $settings ) {
		return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> floatval( $settings['cart_total'] ),
				'current_value' => WC()->cart ? WC()->cart->get_total( false ) : 0
			]);
	}

	public static function validate_cart_counter( $settings ) {
		return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> absint( $settings['cart_counter'] ),
				'current_value' => WC()->cart ? WC()->cart->get_cart_contents_count() : 0
			]);
	}

	public static function validate_stock_status( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> $settings['stockstatus'],
				'current_value' => $product->get_stock_status()
			]);
		}
		
		return false;
	}

	public static function validate_product_type( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> $settings['prdtype'],
				'current_value' => $product->get_type()
			]);
		}
		
		return false;
	}

	public static function validate_product_is_visible( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['visible'] == 'yes' ? true : false,
				'current_value' => $product->is_visible()
			]);
		}
		
		return false;
	}

	public static function validate_product_is_purchasable( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['purchasable'] == 'yes' ? true : false,
				'current_value' => $product->is_purchasable()
			]);
		}
		
		return false;
	}

	public static function validate_product_has_reviews( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['hasreviews'] == 'yes' ? true : false,
				'current_value' => ( $product->get_review_count() > 0 )
			]);
		}
		
		return false;
	}

	public static function validate_product_has_image( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> 'equals',
				'saved_value' 	=> $settings['has_image'] == 'yes' ? true : false,
				'current_value' => $product->get_image_id()
			]);
		}
		
		return false;
	}

	public static function validate_product_has_gallery( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> true,
				'current_value' => $product->get_gallery_image_ids()
			]);
		}
		
		return false;
	}

	public static function validate_product_reviews( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> $settings['totalreviews'],
				'current_value' => ( $product->get_review_count() > 0 ) ? $product->get_review_count() : 0
			]);
		}
		
		return false;
	}

	public static function validate_has_featured_product( $settings ) {
		$featured_products = wc_get_products( [ 'featured' => true ] );
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['has_featured_product'] == 'yes' ? true : false,
			'current_value' => ! empty( $featured_products )
		]);
	}

	public static function validate_has_onsale_product( $settings ) {
		$onsale = wc_get_product_ids_on_sale();
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['onsale_product'] == 'yes' ? true : false,
			'current_value' => ! empty( $onsale )
		]);
	}

	public static function validate_has_backorders( $settings ) {
		$backorders = wc_get_products( [ 'backorders' => true ] );
		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['backorders'] == 'yes' ? true : false,
			'current_value' => ! empty( $backorders )
		]);
	}

	public static function validate_has_sku( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> esc_attr( $settings['sku'] ),
				'current_value' => esc_attr( $product->get_sku() )
			]);
		}

		return false;
	}

	public static function validate_has_upsell_products( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> true,
				'current_value' => $product->get_upsell_ids()
			]);
		}

		return false;
	}

	public static function validate_has_cross_sell_products( $settings ) {
		global $product;

		$product = wc_get_product();
		if ($product != false) {
			return self::validate([
				'operator' 		=> $settings['operator'],
				'saved_value' 	=> true,
				'current_value' => $product->get_cross_sell_ids()
			]);
		}

		return false;
	}

	public static function validate_endpoint( $settings ) {
		$value 		= $settings['endpoint'];
		$operator 	= $settings['operator'];
		$endpoint_url = '';

		if( $value == 'any') {
			$endpoint_url = is_wc_endpoint_url();
		} else if( $value == "orderpay" ) {
			$endpoint_url = is_wc_endpoint_url( 'order-pay' );
		} else if( $value == "orderreceived" ) {
			$endpoint_url = is_wc_endpoint_url( 'order-received' );
		} else if( $value == "vieworder" ) {
			$endpoint_url = is_wc_endpoint_url( 'view-order' );
		} else if( $value == "editaccount" ) {
			$endpoint_url = is_wc_endpoint_url( 'edit-account' );
		} else if( $value == "editaddress" ) {
			$endpoint_url = is_wc_endpoint_url( 'edit-address' );
		} else if( $value == "paymentmethod" ) {
			$endpoint_url = is_wc_endpoint_url( 'add-payment-method' );
		} else if( $value == "customerlogout" ) {
			$endpoint_url = is_wc_endpoint_url( 'customer-logout' );
		} else if( $value == "lostpassword" ) {
			$endpoint_url = is_wc_endpoint_url( 'lost-password' );
		} else {
			//*
		}

		if ( $operator == "equals" ) {
			return ( ! empty( $endpoint_url ) ) ? true : false;
		} else if ( $operator == "not_equals" ) {
			return ( empty( $endpoint_url ) ) ? true : false;
		} else {
			return false;
		}
	}

	public static function validate_recently_viewed_product( $settings ) {
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
		$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		if ( empty( $viewed_products ) ) {
			$rc_viewed_products = false;
		} else {
			$rc_viewed_products = true;
		}

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['recently_viewed'] == 'yes' ? true : false,
			'current_value' => $rc_viewed_products
		]);
	}

	public static function validate_bought_one_product( $settings ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		global $wpdb;

		$meta_key   = '_customer_user';
    	$meta_value = (int) get_current_user_id();

    	$paid_order_statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );

		$count = $wpdb->get_var( $wpdb->prepare("
			SELECT COUNT(p.ID) FROM {$wpdb->prefix}posts AS p
			INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
			WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $paid_order_statuses ) . "' )
			AND p.post_type LIKE 'shop_order'
			AND pm.meta_key = '%s'
			AND pm.meta_value = %s
			LIMIT 1
		", $meta_key, $meta_value ) );

		return self::validate([
			'operator' 		=> 'equals',
			'saved_value' 	=> $settings['bought_one'] == 'yes' ? true : false,
			'current_value' => ( $count > 0 )
		]);
	}

	public static function validate_customer_total_orders( $settings ) {
		if( ! is_user_logged_in() )
			return false;

		$user   = wp_get_current_user();
		return self::validate([
			'operator' 		=> $settings['operator'],
			'saved_value' 	=> absint( $settings['total_orders'] ),
			'current_value' => $user->ID ? wc_get_customer_order_count( $user->ID ) : 0
		]);
	}

	public static function validate_customer_total_products( $settings ) {
		if( ! is_user_logged_in() )
			return false;

		$user   = wp_get_current_user();
		$count  = 0;
		$orders = wc_get_orders( array(
			'customer_id' => $user->ID,
			'status'      => 'completed',
		) );

		foreach ( $orders as $order ) {
			$count += count( $order->get_items() );
		}

		return self::validate([
			'operator' 		=> $settings['operator'],
			'saved_value' 	=> absint( $settings['total_products'] ),
			'current_value' => $count
		]);
	}

	public static function validate_customer_total_spent( $settings ) {
		if( ! is_user_logged_in() )
			return false;

		$user     = wp_get_current_user();
		return self::validate([
			'operator' 		=> $settings['operator'],
			'saved_value' 	=> floatval( $settings['total_spent'] ),
			'current_value' => $user->ID ? wc_get_customer_total_spent( $user->ID ) : 0,
		]);
	}
}