<?php

namespace ZionBuilderPro;

use WP_Error;
use ZionBuilderPro\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// Load licensing class
if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php';
}

class License {
	const STORE_URL                   = 'https://zionbuilder.io';
	const LIBRARY_URL                 = 'https://library.zionbuilder.io/wp-json/zionbuilder-library/v1/';
	const EDD_ITEM_ID                 = '93';
	const API_KEY_OPTION_FIELD        = 'zionbuilder_license_key';
	const API_DETAILS_OPTION_FIELD    = 'zionbuilder_license_details';
	const API_KEY_OPTION_STATUS_FIELD = 'zionbuilder_license_status';

	public function __construct() {
		add_action( 'admin_init', [ $this, 'check_for_updates' ] );
		add_filter( 'zionbuilder/utils/pro_active', [ $this, 'has_valid_license' ] );
		add_filter( 'zionbuilder/library/zion_library/remote_arguments', [ $this, 'attach_license_info' ], 10, 2 );
	}

	public function check_for_updates() {
		// Don't proceed if the license is not valid
		if ( ! self::has_valid_license() ) {
			return false;
		}

		$license_key = self::get_license_key();

		// setup the updater
		$edd_updater = new \EDD_SL_Plugin_Updater(
			self::STORE_URL,
			Plugin::instance()->get_plugin_file(),
			[
				'version' => Plugin::instance()->get_version(),
				'license' => $license_key,
				'item_id' => self::EDD_ITEM_ID,
				'author'  => 'zionbuilder.io',
				'beta'    => false,
			]
		);
	}

	public function attach_license_info( $args ) {
		$args['license']  = self::get_license_key();
		$args['site_url'] = home_url();

		return $args;
	}

	public static function has_valid_license() {
		$license_details = self::get_license_details();
		$license_key     = self::get_license_key();
		$license_status  = self::get_license_status();

		if ( ! $license_key || $license_status !== 'valid' ) {
			return false;
		}

		if ( ! isset( $license_details->expires ) ) {
			return false;
		}

		$expire_date  = strtotime( $license_details->expires );
		$current_date = time();

		return $expire_date > $current_date || $license_details->expires === 'lifetime';
	}

	public static function activate_license( $license ) {
		$license = trim( $license );
		$license = filter_var( $license, FILTER_SANITIZE_STRING );

		if ( ! $license ) {
			return new \WP_Error( 'invalid_response', __( 'Invalid license.', 'zionbuilder-pro' ) );
		}

		// data to send in our API request
		$api_params = [
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_id'    => self::EDD_ITEM_ID,
			'url'        => home_url(),
		];

		// Call the custom API.
		$response = wp_remote_post(
			self::STORE_URL,
			[
				'timeout' => 15,
				'body'    => $api_params,
			]
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'zionbuilder-pro' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						$message = sprintf(
							__( 'Your license key expired on %s.', 'zionbuilder-pro' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled':
					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'zionbuilder-pro' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'zionbuilder-pro' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'zionbuilder-pro' );
						break;

					case 'item_name_mismatch':
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'zionbuilder-pro' ), Plugin::instance()->get_plugin_data( 'Name' ) );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'zionbuilder-pro' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'zionbuilder-pro' );
						break;
				}
			}
		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			return new \WP_Error( 'invalid_response', $message );
		}

		$valid_license = 'valid' === $license_data->license;

		// $license_data->license will be either "valid" or "invalid"
		update_option( self::API_KEY_OPTION_STATUS_FIELD, $license_data->license );

		// Save the license in DB if valid
		if ( $valid_license ) {
			update_option( self::API_KEY_OPTION_FIELD, $license );
			update_option( self::API_DETAILS_OPTION_FIELD, $license_data );
			return $license_data;
		}

		return false;
	}

	public static function get_license_details() {
		return get_option( self::API_DETAILS_OPTION_FIELD );
	}

	public static function get_license_key() {
		return get_option( self::API_KEY_OPTION_FIELD );
	}

	public static function get_license_status() {
		return get_option( self::API_KEY_OPTION_STATUS_FIELD );
	}

	public static function delete_license() {
		// retrieve the license from the database
		$license = trim( get_option( self::API_KEY_OPTION_FIELD ) );

		// data to send in our API request
		$api_params = [
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_id'    => self::EDD_ITEM_ID,
			'url'        => home_url(),
		];

		// Call the custom API.
		$response = wp_remote_post(
			self::STORE_URL,
			[
				'timeout' => 15,
				'body'    => $api_params,
			]
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'zionbuilder-pro' );
			}

			return new WP_Error( 'invalid_response', $message );
		}

		// decode the license data
		json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		delete_option( self::API_KEY_OPTION_STATUS_FIELD );
		delete_option( self::API_KEY_OPTION_FIELD );
		delete_option( self::API_DETAILS_OPTION_FIELD );

		return [
			'message' => __( 'License disconnected.', 'zionbuilder-pro' ),
		];
	}
}
