<?php
namespace ZiUltimate;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class WooHelpers {
	function __construct() {
		$active_wcels = (array) get_option('ziultimate_active_wcels');

		add_action( 'wp_ajax_zuwoo_free_shipping_amount', [ $this, 'zuwoo_free_shipping_amount' ] );
		add_action( 'wp_ajax_nopriv_zuwoo_free_shipping_amount', [ $this, 'zuwoo_free_shipping_amount' ] );
		add_action( 'wp_loaded', [ $this, 'zuwo_woocommerce_empty_cart_action' ], 20 );

		add_action( 'wp_ajax_zuwoo_update_free_shipping_qty', [ $this, 'zuwoo_update_free_shipping_qty' ] );
		add_action( 'wp_ajax_nopriv_zuwoo_update_free_shipping_qty', [ $this, 'zuwoo_update_free_shipping_qty' ] );

		add_action( 'wp_ajax_zu_update_mini_cart_item_quantity', [ $this, 'zu_update_mini_cart_item_quantity' ] );
		add_action( 'wp_ajax_nopriv_zu_update_mini_cart_item_quantity', [ $this, 'zu_update_mini_cart_item_quantity' ] );

		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'zu_add_to_cart_fragments' ] );

		if( $active_wcels && in_array( 'minicart', $active_wcels ) ) {
			add_action( 'woocommerce_before_mini_cart_contents', function() {
				add_filter( 'woocommerce_widget_cart_item_quantity', [ $this, 'zu_add_quantity_input_field' ], 1000, 3 );
			});

			add_action( 'woocommerce_after_mini_cart', function() {
				remove_filter( 'woocommerce_widget_cart_item_quantity', [ $this, 'zu_add_quantity_input_field' ], 1000, 3 );
			});
		}
	}

	public function zuwoo_free_shipping_amount() {
		//check_ajax_referer( 'ouwoo-free-shipping', 'security' );

		$price = $_POST['price'];
		$exclude_coupons = $_POST['excl_cp'];
		$threshold_amount = $_POST['thamt'];
		$order_amount = self::cart_total( $exclude_coupons );
		$pbres = ($order_amount < $price) ? ceil( ( $order_amount / $price ) * 100 ) : 100;

		if( ! empty($threshold_amount) && $threshold_amount > $order_amount) {
			wp_send_json( array(
				'amount' 	=> 'false',
				'threshold' => 'true',
				'pbres' 	=> $pbres
			));
		}

		if ( $order_amount < $price ) {
			$data = array(
				'amount' 	=> wc_price( $price - $order_amount ),
				'threshold' => 'false',
				'pbres' 	=> $pbres
			);
		} else {
			$data = array(
				'amount' 	=> 'false',
				'threshold' => 'false',
				'pbres' 	=> $pbres
			);
		}

		wp_send_json($data);
	}

	public function zuwoo_update_free_shipping_qty() {
		//check_ajax_referer( 'ouwoo-free-shipping', 'security' );

		$reqqty = $_POST['reqqty'];
		$threshold_qty = $_POST['thqty'];
		$cart_qty = ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ? 0 : WC()->cart->get_cart_contents_count();
		$pbres = ($cart_qty < $reqqty) ? ceil( ( $cart_qty / $reqqty ) * 100 ) : 100;

		if( ! empty($threshold_qty) && $threshold_qty > $cart_qty) {
			wp_send_json( array(
				'remaining_qty' 	=> 'false',
				'threshold' 		=> 'true',
				'pbres' 	=> $pbres
			));
		}

		if ( $cart_qty < $reqqty ) {
			$data = array(
				'remaining_qty' => '<span class="remaining-qty">' . ( $reqqty - $cart_qty ) . '</span>',
				'threshold' 	=> 'false',
				'pbres' 	=> $pbres
			);
		} else {
			$data = array(
				'remaining_qty' 	=> 'false',
				'threshold' 		=> 'false',
				'pbres' 	=> $pbres
			);
		}

		wp_send_json($data);
	}

	public static function cart_total( $exclude_coupons = false ) {
		$cart_total = 0;

		if ( is_null( WC()->cart ) || WC()->cart->is_empty() ) {
			return $cart_total;
		}

		$cart_total = ( 'incl' === WC()->cart->get_tax_price_display_mode() ) ? WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() : WC()->cart->get_subtotal();
			

		if ( ! empty( $exclude_coupons ) && $exclude_coupons === true ) {
			$coupons = WC()->cart->get_applied_coupons();

			if ( $coupons ) {
				$coupons_total_amount = 0;
				foreach ( $coupons as $coupon ) {
					$coupons_total_amount += WC()->cart->get_coupon_discount_amount( $coupon, WC()->cart->display_cart_ex_tax );
				}
				$cart_total -= ( $coupons_total_amount );
			}
		}

		return $cart_total;
	}

	/**
	 * Will update the fragments
	 * 
	 * @return array
	 */
	public function zu_add_to_cart_fragments( $fragments ) {
		ob_start();
		?>
			<span class="zu-cart-counter"><?php echo is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : '0'; ?></span>
		<?php
		
		$fragments['span.zu-cart-counter'] = ob_get_clean();

		ob_start();
		?>
			<span class="zu-cart-price"><?php echo is_object( WC()->cart ) ? WC()->cart->get_cart_total() : wc_price( 0 ); ?></span>
		<?php
		
		$fragments['span.zu-cart-price'] = ob_get_clean();

		remove_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'zu_add_to_cart_fragment' ] );
		
		return $fragments;
	}

	/**
	 * callback function for empty cart and buy now buttons
	 * 
	 */
	public function zuwo_woocommerce_empty_cart_action() {
		if ( isset( $_GET['zu_empty_cart'] ) && 'yes' === esc_html( $_GET['zu_empty_cart'] ) ) {
			WC()->cart->empty_cart();

			if( isset( $_GET['zu_redirect'] ) && 'yes' === esc_html( $_GET['zu_redirect'] ) ) {
				$referer  = esc_url( remove_query_arg( [ 'zu_empty_cart', 'zu_redirect' ] ) );
				wp_safe_redirect( $referer ); 
				exit();
			}
		}

		if( isset( $_GET['zu_buy_now'] ) && $_GET['zu_buy_now'] == 'yes' ) {
			if( isset( $_GET['keep_cart_items'] ) && $_GET['keep_cart_items'] == 'no' ) { WC()->cart->empty_cart(); }
			
			$product_id = absint( $_GET['add_to_cart'] );

			WC()->cart->add_to_cart( $product_id, 1 );

			$referer  = esc_url( remove_query_arg( [ 'add_to_cart', 'zu_buy_now', 'keep_cart_items' ] ) );
			wp_safe_redirect( $referer ); 
			exit();
		}

		if( isset( $_POST['zu_buy_now'] ) && $_POST['zu_buy_now'] == 'yes' ) {
			$flag 				= false;
			$keep_cart_items 	= isset( $_POST['keep_cart_items'] ) ? $_POST['keep_cart_items'] : 'no';
			$quantities 		= $_POST['quantity'];

			//* grouped product
			if( is_array( $quantities ) ) {
				
				$qtys = array_flip( $quantities );
				
				foreach( $qtys as $quantity => $product_id ) {
					if( ! empty( $quantity ) )  {
						if( $keep_cart_items == 'no' && $flag === false ) { WC()->cart->empty_cart(); }
						if( $keep_cart_items == 'no' ){ WC()->cart->add_to_cart( absint( $product_id ), absint( $quantity ) ); }
						$flag = true;
					}
				}

			} else {
				
				$flag = true;

				//* remove the existing cart items and add new item
				if( $keep_cart_items == 'no' )
					WC()->cart->empty_cart(); 
				
				$product_id = absint( $_POST['product_id'] );
				$quantity = absint( $_POST['quantity'] );

				if ( isset( $_POST['variation_id'] ) ) {
					$variation_id = absint( $_POST['variation_id'] );
					WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
				} else {
					WC()->cart->add_to_cart( $product_id, $quantity );
				}
			}

			if( $flag ) {
				//* clear the all notices
				wc_clear_notices();

				wp_safe_redirect( esc_url( $_POST['zu_redirect_url'] ) ); 
				exit();
			}
		}
	}

	/**
	 * Will add the +/- button with
	 * quantity input field
	 * 
	 * @return HTML
	 */
	public function zu_add_quantity_input_field( $price_html, $cart_item, $cart_item_key ) {
		if( is_null( WC()->cart ) )
			return $price_html;

		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( WC()->cart->display_prices_including_tax() ) {
			$product_price = wc_get_price_including_tax( $_product );
		} else {
			$product_price = wc_get_price_excluding_tax( $_product );
		}

		$min_value = max( $_product->get_min_purchase_quantity(), 0 );
		$max_value = $_product->get_max_purchase_quantity();
		$max_value = 0 < $max_value ? $max_value : '';
		
		if ( '' !== $max_value && $max_value < $min_value ) {
			$max_value = $min_value;
		}

		$qtyField = '<input 
						type="number" 
						class="zu-mini-cart-qty" 
						step="1" 
						min="'. $min_value .'" 
						max="'. $max_value .'" 
						value="' . $cart_item['quantity'] . '" 
						placeholder="" 
						inputmode="numeric"
					>';

		$price_html .= sprintf( 
			'<div class="zu-mini-cart-quantity">
				<div class="zu-mini-cart-qty-box" data-product_id="%d" data-cart_item_key="%s">
					<span class="zu-mini-cart-qty-minus zu-mini-cart-qty-chng">-</span>
					%s
					<span class="zu-mini-cart-qty-plus zu-mini-cart-qty-chng">+</span>
				</div>
				<div class="zu-mini-cart-item-total-price">%s</div>
			</div>',
			$cart_item['product_id'],
			$cart_item_key,
			$qtyField,
			wc_price( $cart_item['quantity'] * $product_price )
		);

		return $price_html;
	}

	/**
	 * Will update the mini cart quantity
	 * 
	 * @return data in json format
	 */
	public function zu_update_mini_cart_item_quantity() {
		$cart_key 		= sanitize_text_field( $_POST['cart_key'] );
		$new_qty 		= wc_stock_amount(absint( $_POST['qty'] ));
		$cart_item_data = WC()->cart->get_cart_item( $cart_key );
		$product_id 	= apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
		$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $new_qty);	

		if( $passed_validation && $cart_key && ! empty( $cart_item_data ) ) {
			$updated = $new_qty == 0 ? WC()->cart->remove_cart_item( $cart_key ) : WC()->cart->set_quantity( $cart_key, $new_qty );
			if( $updated ){
				
				wc_clear_notices();

				\WC_AJAX::get_refreshed_fragments();
			}
		} else {
			$data = array(
				'error' => __( 'Failed. Something went wrong', 'ziultimate' ),
			);
			
			wp_send_json($data);
		}
	}

	/**
	 * Will generate the query args 
	 * for ultimate query builder
	 * 
	 * @param array $query_args
	 * @param array $config
	 * 
	 * @return void
	 */
	public static function generate_wc_query_args( $query_args, $config ) {
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$outofstock 	= ! empty( $config['outofstock'] ) ? $config['outofstock'] : 'yes';
		$show_hidden 	= ! empty( $config['show_hidden'] ) ? $config['show_hidden'] : 'no';
		$hide_free 		= ! empty( $config['hide_free'] ) ? $config['hide_free'] : 'yes';
		$orderby 		= ! empty( $config['order_by'] ) ? $config['order_by'] : 'date';

		if( ! empty( $config['category_ids'] ) ) {
			$categories 	= explode( ',', $config['category_ids'] );
			$categories 	= array_map( 'absint', $categories );
			$cat_operator 	= ! empty( $config['cat_operator'] ) ? $config['cat_operator'] : 'IN';
			$query_args['tax_query'][] = [
				'taxonomy'         => 'product_cat',
				'terms'            => $categories,
				'field'            => 'term_taxonomy_id',
				'operator'         => $cat_operator,
				'include_children' => 'AND' === $cat_operator ? false : true,
			];
		}

		if ( $show_hidden == 'no' ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
				'operator' => 'NOT IN',
			);
			$query_args['post_parent'] = 0;
		}

		if ( $hide_free == 'yes' ) {
			$query_args['meta_query'][] = array(
				'key'     => '_price',
				'value'   => 0,
				'compare' => '>',
				'type'    => 'DECIMAL',
			);
		}

		if ( 'yes' === $outofstock ) {
			$query_args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				),
			); // WPCS: slow query ok.
		}

		switch ( $orderby ) {
			case 'price':
				$query_args['meta_key'] = '_price'; // WPCS: slow query ok.
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'rand':
				$query_args['orderby'] = 'rand';
				break;
			case 'sales':
				$query_args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
				$query_args['orderby']  = 'meta_value_num';
				break;
			default:
				$query_args['orderby'] = 'date';
		}

		return $query_args;
	}
}