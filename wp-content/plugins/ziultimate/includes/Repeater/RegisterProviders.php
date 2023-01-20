<?php
namespace ZiUltimate\Repeater;

use ZiUltimate\Admin\License;
use ZiUltimate\Repeater\Providers\ACFOptionsRepeater;
use ZiUltimate\Repeater\Providers\GalleryBuilder;


class RegisterProviders {
	private $settings = null;
	/**
	 * Main class constructor
	 *
	 * @return void
	 */
	function __construct() {
		if( ! License::has_valid_license() )
			return;

		$this->settings = get_option('zu_settings');;

		add_action( 'template_redirect', [ $this, 'zu_woo_track_product_view' ], 21 );
		add_action( 'zionbuilderpro/repeater/register_providers', [ $this, 'zu_register_providers' ], 2 );
		add_action( 'zionbuilderpro/repeater/register_providers', [ $this, 'zu_register_acf_providers' ], 15 );
	}

	/**
	 * Will register repeater providers
	 *
	 * @return void
	 */
	public function zu_register_providers( $repeater ) {
		$providers = self::general_providers();

		foreach( $providers as $slug => $provider ) {
			if( ! empty( $this->settings[ $slug ] ) && $this->settings[ $slug ] == $provider['value'] ) {
				$classname = $provider['class'];
				$repeater->register_provider( new $classname() );
			}
		}

		if( function_exists( 'acf' ) || class_exists('RWMB_Loader') ) {
			$repeater->register_provider( new GalleryBuilder() );
		}

		if( class_exists( 'WooCommerce' ) ) {
			$woo_providers = self::get_woo_providers();

			foreach( $woo_providers as $slug => $provider ) {
				if( ! empty( $this->settings[ $slug ] ) && $this->settings[ $slug ] == $provider['value'] ) {
					$classname = $provider['class'];
					$repeater->register_provider( new $classname() );
				}
			}
		}
	}

	private static function general_providers() {
		return [
			'adjposts' => [
				'value' 	=> 'adjposts',
				'class' 	=> '\ZiUltimate\Repeater\Providers\AdjacentPosts'
			],
			'authboxrep' => [
				'value' 	=> 'authboxrep',
				'class' 	=> '\ZiUltimate\Repeater\Providers\AuthorBoxQueryBuilder'
			],
			'relposts' => [
				'value' 	=> 'relposts',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WPRelatedPosts'
			],
			'termsrep' => [
				'value' 	=> 'termsrep',
				'class' 	=> '\ZiUltimate\Repeater\Providers\TermsQueryBuilder'
			],
			'extndrep' => [
				'value' 	=> 'extndrep',
				'class' 	=> '\ZiUltimate\Repeater\Providers\ExtendedQueryBuilder'
			],
			'advrep' => [
				'value' 	=> 'advrep',
				'class' 	=> '\ZiUltimate\Repeater\Providers\UltimateQueryBuilder'
			],
		];
	}

	private static function get_woo_providers() {
		return [
			'bsprd' => [
				'value' 	=> 'bsprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCBestSellingProducts'
			],
			'fetdprd' => [
				'value' 	=> 'fetdprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCFeaturedProducts'
			],
			'onsaleprd' => [
				'value' 	=> 'onsaleprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCOnSaleProducts'
			],
			'rctvwprd' => [
				'value' 	=> 'rctvwprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCRecentlyViewed'
			],
			'relprd' => [
				'value' 	=> 'relprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCRelatedProducts'
			],
			'trprd' => [
				'value' 	=> 'trprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCTopRatedProducts'
			],
			'upsprd' => [
				'value' 	=> 'upsprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCUpSells'
			],
			'crossells' => [
				'value' 	=> 'crossells',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCCrossSells'
			],
			'revprd' => [
				'value' 	=> 'revprd',
				'class' 	=> '\ZiUltimate\Repeater\Providers\WCReviews'
			],
		];
	}

	public function zu_register_acf_providers( $repeater ) {
		if( ! function_exists( 'acf' ) ) 
			return;

		if( ! empty( $this->settings['acfoptrep'] ) && $this->settings['acfoptrep'] == 'acfoptrep' )
			$repeater->register_provider( new ACFOptionsRepeater() );
	}

	/**
	 * Will track the recently viewed product's ids
	 *
	 * @return void
	 */
	public function zu_woo_track_product_view() {
		if( is_admin() )
			return;

		if ( ! is_singular( 'product' ) )
			return;

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) );
		}

		$keys = array_flip( $viewed_products );

		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}

		$viewed_products[] = $post->ID;

		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only.
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
	}
}