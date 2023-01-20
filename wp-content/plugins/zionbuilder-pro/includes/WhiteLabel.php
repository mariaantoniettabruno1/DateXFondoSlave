<?php

namespace ZionBuilderPro;

use ZionBuilder\Settings;
use ZionBuilder\WhiteLabel as FreeWhiteLabel;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class WhiteLabel {
	public function __construct() {
		add_filter( 'zionbuilder/whitelabel/data', [ $this, 'add_white_label_data' ] );
		add_filter( 'zionbuilderpro/admin/js_data', [ $this, 'add_white_label_options' ] );
	}

	public function add_white_label_data( $data ) {
		$saved_values = Settings::get_value( 'white_label' );

		$values_to_change = [
			'plugin_title',
			'plugin_slug',
			'plugin_logo',
			'plugin_help_url',
			'plugin_loader_logo',
			'getting_started_video',
		];

		foreach ( $values_to_change as $white_label_value ) {
			if ( isset( $saved_values[$white_label_value] ) ) {
				$data[$white_label_value] = $saved_values[$white_label_value];
			}
		}

		return $data;
	}

	public function add_white_label_options( $js_data ) {
		$js_data['white_label_schema'] = [
			'plugin_title'       => [
				'type'  => 'text',
				'id'    => 'plugin_title',
				'width' => '100',
				'title' => __( 'Title', 'zionbuilder-pro' ),
			],
			'plugin_slug'        => [
				'type'        => 'text',
				'id'          => 'plugin_slug',
				'width'       => '100',
				'title'       => __( 'Plugin Slug', 'zionbuilder-pro' ),
				'placeholder' => 'zionbuilder',
				'description' => __( 'The plugin slug is used in plugin related URLs. This needs to be alphanumeric separated by dashes or underscores', 'zionbuilder-pro' ),
			],
			'plugin_logo'        => [
				'type'  => 'media',
				'id'    => 'plugin_logo',
				'width' => '100',
				'title' => __( 'Logo', 'zionbuilder-pro' ),
			],
			'plugin_help_url'    => [
				'type'  => 'text',
				'id'    => 'plugin_help_url',
				'width' => '100',
				'title' => __( 'Help Url', 'zionbuilder-pro' ),
			],
			'plugin_loader_logo' => [
				'type'  => 'media',
				'id'    => 'plugin_loader_logo',
				'width' => '100',
				'title' => __( 'Loader image', 'zionbuilder-pro' ),
			],
			'getting_started_video' => [
				'type'  => 'text',
				'id'    => 'getting_started_video',
				'width' => '100',
				'title' => __( 'Getting started video iframe URL', 'zionbuilder-pro' ),
				'description' => __( 'Enter your iframe video URL that will appear in the getting started popup. The popup will be disabled if you do not set a video.', 'zionbuilder-pro' ),
				'default' => FreeWhiteLabel::get_getting_started_video()
			],
		];

		return $js_data;
	}

}
