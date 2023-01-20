<?php

namespace ZionBuilderPro\Fonts\Providers;

use ZionBuilderPro\Fonts\Providers\TypeKit;
use ZionBuilder\Settings;
use ZionBuilder\FontsManager\FontProvider;


// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class AdobeFontsProvider
 *
 * @package ZionBuilder\FontsManager\Fonts
 */
class AdobeFontsProvider extends FontProvider {
	const CACHE_OPTION_KEY = 'zn_adobe_fonts_cache';

	public static function get_id() {
		return 'adobe-fonts';
	}

	/**
	 * Main class constructor
	 *
	 * Will load the scripts into header
	 */
	public function __construct() {
		add_action( 'wp_head', [ $this, 'enqueue_scripts' ] );
	}

	public function get_data_set() {
		$returned_fonts       = [];
		$all_saved_fonts_info = $this->get_fonts_info();
		$enabled_fonts        = $this->get_adobe_fonts();
		// Get info for each kit and prepare the response
		if ( is_array( $all_saved_fonts_info ) && is_array( $enabled_fonts ) ) {
			foreach ( $all_saved_fonts_info as $kit ) {
				if ( is_wp_error( $kit ) || ! in_array( $kit['kit']['id'], $enabled_fonts ) ) {
					continue;
				}

				if ( isset( $kit['kit']['families'] ) && is_array( $kit['kit']['families'] ) ) {
					$font_families = $kit['kit']['families'];
					foreach ( $font_families as $family_info ) {
						$returned_fonts[] = [
							'id'   => $family_info['css_stack'],
							'name' => $family_info['name'],
						];
					}
				}
			}
		}

		return $returned_fonts;
	}

	public function get_fonts_info( $use_cache = true ) {
		$all_saved_fonts_info = get_option( self::CACHE_OPTION_KEY, [] );
		$returned_fonts       = [];

		if ( ! empty( $all_saved_fonts_info ) && $use_cache ) {
			$returned_fonts = $all_saved_fonts_info;
		} else {
			$typekit_token = Settings::get_value( 'typekit_token' );

			if ( ! empty( $typekit_token ) ) {
				$typekit_instance = new TypeKit( $typekit_token );

				$returned_fonts = $typekit_instance->get_all_kits_info();
				if ( ! empty( $returned_fonts ) ) {
					update_option( self::CACHE_OPTION_KEY, $returned_fonts, false );
				}
			}
		}

		return $returned_fonts;
	}

	/**
	 * This function will return the adobe fonts ids
	 */
	public function get_adobe_fonts() {
		return Settings::get_value( 'typekit_fonts', [] );
	}

	/**
	 * This function will return the adobe script
	 *
	 * @return bool|string
	 */
	public function get_enqueue_scripts() {
		$kits = $this->get_adobe_fonts();
		if ( empty( $kits ) ) {
			return false;
		}

		return TypeKit::get_all_kits_scripts( $kits );
	}

	/**
	 * This function will render the adobe fonts scripts
	 *
	 * @hooked wp_head
	 *
	 * @see __construct()
	 *
	 * @return bool
	 */
	public function enqueue_scripts() {
		$enqueue_scripts = $this->get_enqueue_scripts();

		if ( empty( $enqueue_scripts ) ) {
			return false;
		}

		// Render the inline script
		echo $enqueue_scripts;
	}
}
