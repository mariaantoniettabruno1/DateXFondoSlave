<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class ArchiveTitle
 *
 * @package ZiUltimate\DynamicContent\Fields
 */
class ArchiveTitle extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'taxonomy';
	}

	public function get_id() {
		return 'zu-archive-title';
	}

	public function get_name() {
		return esc_html__( 'Archive title - ZU', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Returns the current term title
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		add_filter( 'get_the_archive_title_prefix', '__return_false' );
		the_archive_title();
		remove_filter( 'get_the_archive_title_prefix', '__return_false' );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		add_filter( 'get_the_archive_title_prefix', '__return_false' );
		return get_the_archive_title();
		remove_filter( 'get_the_archive_title_prefix', '__return_false' );
	}
}