<?php

namespace ZionBuilderPro\Integrations\Metabox;

use ZionBuilder\Integrations\IBaseIntegration;
use ZionBuilderPro\DynamicContent\Manager;

use ZionBuilderPro\Integrations\Metabox\Fields\Text;
use ZionBuilderPro\Integrations\Metabox\Fields\Image;

class Metabox implements IBaseIntegration {
	/**
	 * Retrieve the name of the integration
	 *
	 * @return string
	 */
	public static function get_name() {
		return 'metabox';
	}

	/**
	 * Check if we can load this integration or not
	 *
	 * @return boolean If true, the integration will be loaded
	 */
	public static function can_load() {
		return defined( 'RWMB_VER' );
	}

	/**
	 * Acf constructor
	 */
	public function __construct() {
		add_action( 'zionbuilderpro/dynamic_content_manager/register_fields', [ $this, 'register_fields' ] );
		add_action( 'zionbuilderpro/dynamic_content_manager/register_field_groups', [ $this, 'register_field_group' ] );
	}


	public function register_field_group( Manager $elements_manager ) {
		$elements_manager->register_field_group(
			[
				'id'   => 'metabox',
				'name' => esc_html__( 'Metabox', 'zionbuilder-pro' ),
			]
		);
	}

	/**
	 * Will register all supported elements
	 *
	 * @param Manager $elements_manager
	 */
	public function register_fields( Manager $elements_manager ) {
		$elements_manager->register_field( new Text() );
		$elements_manager->register_field( new Image() );
		// $elements_manager->register_field( new FieldImage() );
		// $elements_manager->register_field( new FieldLink() );
	}
}
