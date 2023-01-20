<?php
namespace ZiUltimate;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class RegisterWooElements {
	function __construct() {
		add_filter( 'zionbuilder/elements/categories', 					[ $this, 'add_woo_elements_categories' ] );
		add_action( 'zionbuilder/elements_manager/register_elements', 	[ $this, 'register_woo_elements' ] );
	}

	public function add_woo_elements_categories( $categories ) {
		$name = __( 'ZiUltimate', 'ziultimate' );
		$zuwl = get_option('zuwl');
		if( $zuwl ) {
			$name = ! empty( $zuwl['plugin_name'] ) ? esc_html( $zuwl['plugin_name'] ) : $name;
		}

		$zuwoo_categories = [
			[
				'id'   => 'zuwoo',
				'name' => $name . ' ' . __( 'Woo', 'ziultimate' ),
			],
			[
				'id'   => 'zuwccpb',
				'name' => $name . ' ' . esc_html__('Cart Page Builder', 'ziultimate'),
			],
			[
				'id'   => 'zuwcreviews',
				'name' => esc_html__('WooCommerce Reviews Elements', 'ziultimate'),
			],
		];

		return array_merge( $categories, $zuwoo_categories );
	}

	public function register_woo_elements( $elements_manager ) {
        $zu_woo_elements = self::get_woo_elements();
		$active_wcels = (array) get_option('ziultimate_active_wcels');

		foreach ( $active_wcels as $key => $element ) {
			if( empty( $element ) )
				return;

			if( 'cartpage' == $element ) {
				$class_name = __NAMESPACE__ . '\\WooElements\\CartPage\\CartPage';
				$elements_manager->register_element( new $class_name() );
			} elseif( 'wcreviews' == $element ) {
				foreach( [ 'Author', 'Avatar', 'Date', 'Ratings', 'Comment' ] as $class ) {
					$class_name = __NAMESPACE__ . '\\WooElements\\Reviews\\' . $class;
					$elements_manager->register_element( new $class_name() );
				}
			} elseif( ! empty( $zu_woo_elements[ $element ] ) && is_array( $zu_woo_elements[ $element ] ) ) {
				// Normalize class name
				$class_name = str_replace( '-', '_', $zu_woo_elements[ $element ]['class'] );
				$class_name = __NAMESPACE__ . '\\WooElements\\' . $class_name;
				$elements_manager->register_element( new $class_name() );
			}
		}
    }

	public static function get_woo_elements() {
		$link = 'https://ziultimate.com/doc';
		$elements = [
			/*'atc' 			=> [
				'name' 	=> __('Add To Cart', 'ziultimate'),
				'class' => 'AddToCart\AddToCart',
				'link' 	=> $link . '/add-to-cart/'
			],*/
			'buynow' 		=> [
				'name' 	=> __('Buy Now', 'ziultimate'),
				'class' => 'BuyNow\BuyNow',
				'link' 	=> $link . '/buy-now/'
			],
			'cartcounter' 	=> [
				'name' 	=> __('Cart Counter', 'ziultimate'),
				'class' => 'CartCounter\CartCounter',
				'link' 	=> $link . '/cart-counter/'
			],
			'shopping' 		=> [
				'name' 	=> __('Continue Shopping', 'ziultimate'),
				'class' => 'ContinueShopping\ContinueShopping',
				'link' 	=> $link . '/continue-shopping/'
			],
			'emptycart' 	=> [
				'name' 	=> __('Empty Cart', 'ziultimate'),
				'class' => 'EmptyCart\EmptyCart',
				'link' 	=> $link . '/empty-cart/'
			],
			'fsn' 			=> [
				'name' 	=> __('Free Shipping Notice', 'ziultimate'),
				'class' => 'FreeShippingNotice\FreeShippingNotice',
				'link' 	=> $link . '/free-shipping-notice/'
			],
			'minicart' 		=> [
				'name' 	=> __('Mini Cart', 'ziultimate'),
				'class' => 'MiniCart\MiniCart',
				'link' 	=> $link . '/mini-cart/'
			],
			'NewBadge' 		=> [
				'name' 	=> __('New Badge', 'ziultimate'),
				'class' => 'NewBadge\NewBadge',
				'link' 	=> $link . '/new-badge/'
			],
			'tabsacrd' 		=> [
				'name' 	=> __('Product Tabs in Accordion', 'ziultimate'),
				'class' => 'TabsInAccordion\TabsInAccordion',
				'link' 	=> $link . '/product-tabs-in-accordion/'
			],
			'rategraph' 	=> [
				'name' 	=> __('Rating Graph', 'ziultimate'),
				'class' => 'RatingsGraph\RatingsGraph',
				'link' 	=> $link . '/rating-graph/'
			],
			'wcreviews' 	=> [
				'name' 	=> __('Reviews', 'ziultimate'),
				'class' => 'Reviews\Reviews',
				'link' 	=> $link . '/reviews/'
			],
			'rating' 		=> [
				'name' 	=> __('Reviews Rating', 'ziultimate'),
				'class' => 'ReviewRatings\ReviewRatings',
				'link' 	=> $link . '/reviews-rating/'
			],
			'offer' 		=> [
				'name' 	=> __('Sale Offer', 'ziultimate'),
				'class' => 'SaleOffer\SaleOffer',
				'link' 	=> $link . '/sale-offer/'
			],
			/*'compare' 		=> [
				'name' 	=> __('Yith Compare', 'ziultimate'),
				'class' => 'YithCompare\YithCompare',
				'link' 	=> $link . '/yith-compare-button/'
			],
			'wishlist' 		=> [
				'name' 	=> __('Yith Wishlist', 'ziultimate'),
				'class' => 'YithWishlist\YithWishlist',
				'link' 	=> $link . '/yith-wishlist-button/'
			],*/
		];

		return $elements;
	}
}