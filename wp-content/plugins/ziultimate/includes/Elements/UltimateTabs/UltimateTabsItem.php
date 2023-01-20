<?php
namespace ZiUltimate\Elements\UltimateTabs;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class UltimateTabs
 *
 * @package ZiUltimate\Elements
 */
class UltimateTabsItem extends UltimateElements {

	private $tabs_content_migration_uid = null;

	public function get_type() {
		return 'zu_tabs_item';
	}

	public function is_child() {
		return true;
	}

	public function is_wrapper() {
		return true;
	}

	public function get_name() {
		return __( 'Tab Item', 'zionbuilder' );
	}

	public function get_sortable_content_orientation() {
		return 'vertical';
	}

	public function on_before_init( $data = [] ) {
		$this->tabs_content_migration_uid = uniqid( 'zntempuid' );
	}

	public function get_children() {
		$options             = $this->options;
		$child_elements_data = ! empty( $this->content ) ? $this->content : [];

		// Convert content to element
		if ( empty( $child_elements_data ) ) {
			$element_data = [
				'element_type' => 'zion_text',
				'uid'          => $this->tabs_content_migration_uid,
				'options'      => [
					'content' => __( 'Tab content', 'ziultimate' ),
				],
			];

			// Set the content first
			$child_elements_data = [ $element_data ];
		}

		return $child_elements_data;
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'title',
			[
				'type'    => 'text',
				'title'   => esc_html__( 'Title', 'zionbuilder' ),
				'default' => esc_html__( 'Tab title', 'zionbuilder' ),
			]
		);

		$options->add_option(
			'subtitle',
			[
				'type'    => 'text',
				'title'   => esc_html__( 'Sub Title', 'ziultimate' ),
				'placeholder' => esc_html__( 'Sub title', 'ziultimate' ),
			]
		);

		$options->add_option(
			'icon',
			[
				'type'       => 'icon_library',
				'id'         => 'icon',
				'dynamic' 	 => true
			]
		);
	}

	public function enqueue_styles() {
		$this->enqueue_editor_style( Utils::get_file_url( 'dist/css/elements/UltimateTabs/editor.css' ) );
	}

	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/UltimateTabs/editor.js' ) );
	}

	public function before_render( $options ) {
		if ( $this->extra_render_data['active'] ) {
			$this->render_attributes->add( 'wrapper', 'class', 'zu-el-tabs-nav--active' );
		}
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		echo '<div class="zu-el-tabs--content" itemprop="text">';
		$this->render_children();
		echo '</div>';
	}
}