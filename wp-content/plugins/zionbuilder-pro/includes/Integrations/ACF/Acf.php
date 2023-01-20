<?php

namespace ZionBuilderPro\Integrations\ACF;

use ZionBuilder\Integrations\IBaseIntegration;
use ZionBuilderPro\DynamicContent\Manager;

// Field types
use ZionBuilderPro\Integrations\ACF\Fields\AcfFieldTypeText;
use ZionBuilderPro\Integrations\ACF\Fields\AcfFieldTypeLink;
use ZionBuilderPro\Integrations\ACF\Fields\AcfFieldTypeImage;

// Repeater provider
use ZionBuilderPro\Integrations\ACF\AcfRepeaterProvider;

class Acf implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'acf';
	}

	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return function_exists( 'acf' );
	}

	/**
	 * Acf constructor
	 */
	public function __construct() {
		add_action( 'zionbuilderpro/dynamic_content_manager/register_fields', [ $this, 'register_fields' ] );
		add_action( 'zionbuilderpro/dynamic_content_manager/register_field_groups', [ $this, 'register_field_group' ] );

		// Repeater functionality
		add_action( 'zionbuilderpro/repeater/register_providers', [ $this, 'register_repeater_provider' ] );
	}

	public function register_repeater_provider( $manager ) {
		$manager->register_provider( new AcfRepeaterProvider() );
	}

	public function register_field_group( Manager $elements_manager ) {
		$elements_manager->register_field_group(
			[
				'id'   => 'ACF',
				'name' => esc_html__( 'ACF', 'zionbuilder-pro' ),
			]
		);
	}

	/**
	 * Will register all supported elements
	 *
	 * @param Manager $elements_manager
	 */
	public function register_fields( Manager $elements_manager ) {
		$elements_manager->register_field( new AcfFieldTypeText() );
		$elements_manager->register_field( new AcfFieldTypeLink() );
		$elements_manager->register_field( new AcfFieldTypeImage() );
	}
}
