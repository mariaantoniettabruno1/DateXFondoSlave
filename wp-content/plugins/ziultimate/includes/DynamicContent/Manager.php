<?php
namespace ZiUltimate\DynamicContent;

// Fields
use ZiUltimate\DynamicContent\Fields\ParentTitle;
use ZiUltimate\DynamicContent\Fields\PostClass;
use ZiUltimate\DynamicContent\Fields\XPostTerms;
use ZiUltimate\DynamicContent\Fields\LoopCounter;
use ZiUltimate\DynamicContent\Fields\ArchiveTitle;
use ZiUltimate\DynamicContent\Fields\TermInfo;
use ZiUltimate\DynamicContent\Fields\TermImage;
use ZiUltimate\DynamicContent\Fields\WpImage;
use ZiUltimate\DynamicContent\Fields\GetQueryString;
use ZiUltimate\DynamicContent\Fields\RepeaterField;
use ZiUltimate\Admin\License;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Manager
 *
 * @package ZiUltimate\DynamicContent
 */
class Manager {

	/**
	 * Main class constructor
	 *
	 * @return void
	 */
	function __construct() {
		if( ! License::has_valid_license() )
			return;

		add_action( 'zionbuilderpro/dynamic_content_manager/register_fields', [ $this, 'zu_register_dynamic_content_fields' ] );
	}

	/**
	 * Register default fields
	 *
	 * Will register our default strings
	 */
	public function zu_register_dynamic_content_fields( $promanager ) {
		// Posts
		$promanager->register_field( new ParentTitle() );
		$promanager->register_field( new PostClass() );
		$promanager->register_field( new XPostTerms() );
		$promanager->register_field( new LoopCounter() );
		
		// Taxonomy
		$promanager->register_field( new ArchiveTitle() );
		$promanager->register_field( new TermInfo() );
		$promanager->register_field( new TermImage() );

		//Other
		$promanager->register_field( new RepeaterField() );
		$promanager->register_field( new WpImage() );
		$promanager->register_field( new GetQueryString() );
	}
}