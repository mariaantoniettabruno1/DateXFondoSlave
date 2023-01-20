<?php

namespace ZionBuilderPro\Fonts\Providers;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class TypeKit
 *
 * Facade class to integrate TypeKit in the theme
 */
class TypeKit {
	/**
	 * The token to use when contacting TypeKit
	 *
	 * @var string
	 *
	 * @see TypeKit::__construct()
	 * @see https://typekit.com/account/tokens
	 */
	private $token = '';

	/**
	 * TypeKit constructor.
	 *
	 * @param string|null $token The token you've got from TypeKit
	 *
	 * @see https://typekit.com/account/tokens
	 */
	public function __construct( $token = null ) {
		$this->token = $token;
		return $this;
	}

	/**
	 * Get kits
	 *
	 * Returns the list of typekits for a given token
	 *
	 * @return []
	 */
	public function get_kits() {
		$kits = $this->get();

		// If we have an error, return it to requester
		if ( ! is_wp_error( $kits ) ) {
			$kits = $kits['kits'];
		}

		return $kits;
	}

	/**
	 * Get kit info
	 *
	 * Returns the information for the requested kit
	 *
	 * @param mixed $kit_id
	 *
	 * @return []
	 */
	public function get_kit_info( $kit_id ) {
		return $this->get( $kit_id );
	}

	/**
	 * Get kits info
	 *
	 * Returns the information for all requested kits
	 *
	 * @param [] $kits_ids
	 *
	 * @return []
	 */
	public function get_all_kits_info() {
		$kits      = $this->get_kits();
		$kits_info = [];
		foreach ( $kits as $kit_info ) {
			$kits_info[] = $this->get( $kit_info['id'] );
		}

		return $kits_info;
	}

	/**
	 * Get kits scripts
	 *
	 * This function will retun all kits scripts based on kits ids
	 *
	 * @param [] $kits_ids
	 *
	 * @return []
	 */
	public static function get_all_kits_scripts( $kits_ids ) {

		// Don't proceed if we do not have any kits
		if ( empty( $kits_ids ) ) {
			return false;
		}

		$script = '<script>';
		foreach ( $kits_ids as $kit_id ) {
			$script .= ' (function(d) {
				var config = {
				kitId: "' . $kit_id . '",
				scriptTimeout: 3000,
				async: true
				},
				h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src=\'https://use.typekit.net/' . $kit_id . '.js\';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
			})(document);';
		}
		$script .= '</script>';

		return $script;
	}

	/**
	 * Execute a get request. This method can be used to retrieve the list of all kits or the information of the specified $kit_id
	 *
	 * @param null|string $kit_id The ID of the kit to retrieve the information for
	 *
	 * @return array|\WP_Error
	 */
	public function get( $kit_id = null ) {
		if ( empty( $this->token ) ) {
			return new \WP_Error( 'zionbuilder-pro-typekit', __( 'No token provided.', 'hg-framework' ) );
		}
		$args = [
			'headers'   => [
				'X-Typekit-Token' => $this->token,
				'Accept'          => 'application/json',
				'Content-Type'    => 'application/x-www-form-urlencoded',
			],
		];

		$url = 'https://typekit.com/api/v1/json/kits';
		if ( ! empty( $kit_id ) ) {
			$url .= '/' . $kit_id;
			$args['headers']['kit_id'] = $kit_id;
		}

		$request     = wp_remote_get( $url, $args );
		$status_code = wp_remote_retrieve_response_code( $request );
		if ( is_wp_error( $request ) ) {
			return $request;
		}

		$body = wp_remote_retrieve_body( $request );

		if ( empty( $body ) ) {
			return new \WP_Error( 'zionbuilder-pro-typekit', __( 'There was a problem contacting Typekit Servers. Please try again!', 'zionbuilder-pro' ), [ 'status' => 500 ] );
		}

		$body = json_decode( $body, true );

		if ( isset( $body['errors'] ) ) {
			switch ( $status_code ) {
				case 400:
					return new \WP_Error( 'zionbuilder-pro-typekit', __( 'Bad request', 'zionbuilder-pro' ), [ 'status' => $status_code ] );
				case 401:
					return new \WP_Error( 'zionbuilder-pro-typekit', __( 'Api Token invalid', 'zionbuilder-pro' ), [ 'status' => $status_code ] );
				case 403:
					return new \WP_Error( 'zionbuilder-pro-typekit', __( 'Too many requests. Please try again later!', 'zionbuilder-pro' ), [ 'status' => $status_code ] );
				case 404:
					return new \WP_Error( 'zionbuilder-pro-typekit', __( 'The resource you are requesting was not found!', 'zionbuilder-pro' ), [ 'status' => $status_code ] );
				case 500:
					return new \WP_Error( 'zionbuilder-pro-typekit', __( 'Could not contact Typekit servers. Please try again later!', 'zionbuilder-pro' ), [ 'status' => $status_code ] );
				case 503:
					return new \WP_Error( 'zionbuilder-pro-typekit', __( 'Typekit API is offline. Please try again later!', 'zionbuilder-pro' ), [ 'status' => $status_code ] );
			}
		}

		return $body;
	}
}
