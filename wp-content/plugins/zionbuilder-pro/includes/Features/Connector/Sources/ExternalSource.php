<?php

namespace ZionBuilderPro\Features\Connector\Sources;

use ZionBuilder\Library\Sources\BaseSource;
use ZionBuilder\Nonces;
use ZionBuilder\Plugin as FreePlugin;

class ExternalSource extends BaseSource {
	private $external_url;
	private $external_password;

	public function on_init( $args ) {
		$this->external_url      = $args['external_url'];
		$this->external_password = $args['external_password'];

		// This reuest will run through local installation. We need the auth headers
		$this->request_headers = [
			'X-WP-Nonce'   => Nonces::generate_nonce( Nonces::REST_API ),
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		];
	}

	public function get_type() {
		return self::TYPE_EXTERNAL;
	}

	/**
	 * Returns a list of template items and their categories
	 *
	 * @return array
	 */
	public function get_items_and_categories() {
		$url = $this->external_url . '/wp-json/zionbuilder-pro/v1/connector/library/items-and-categories';

		if ( ! empty( $this->external_password ) ) {
			$url = add_query_arg(
				[
					'password' => md5( $this->external_password ),
				],
				$url
			);
		}

		// Get the url password for this source
		$external_response = wp_remote_get( $url );

		if ( is_wp_error( $external_response ) ) {
			return $external_response;
		}

		return json_decode( $external_response['body'] );
	}

	public function insert_item( $item_id ) {
		$url = $this->external_url . '/wp-json/zionbuilder-pro/v1/connector/library/get-builder-data';

		$url = add_query_arg(
			[
				'template_id' => $item_id,
			],
			$url
		);

		if ( ! empty( $this->external_password ) ) {
			$url = add_query_arg(
				[
					'password' => md5( $this->external_password ),
				],
				$url
			);
		}

		// Get the url password for this source
		$external_response = wp_remote_get( $url );

		if ( is_wp_error( $external_response ) ) {
			return $external_response;
		}

		return json_decode( $external_response['body'] );
	}
}
