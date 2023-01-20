<?php

namespace ZionBuilderPro\Elements\Tabs;

use ZionBuilderPro\Utils;
use \ZionBuilder\Elements\Tabs\TabsItem as FreeTabsItem;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class TabsItem extends FreeTabsItem {
	/**
	 * Holds a refference to the uid generated for free to pro migration
	 *
	 * @var string
	 */
	private $tabs_content_migration_uid = null;

	/**
	 * Is wrapper
	 *
	 * Returns true if the element can contain other elements ( f.e. section, column )
	 *
	 * @return boolean The element icon
	 */
	public function is_wrapper() {
		return true;
	}

	/**
	 * On before init
	 *
	 * Allow the users to add their own initialization process without extending __construct
	 *
	 * @param array<string, mixed> $data The data for the element instance
	 *
	 * @return void
	 */
	public function on_before_init( $data = [] ) {
		$this->tabs_content_migration_uid = uniqid( 'zntempuid' );

		$this->on( 'options/schema/set', [ $this, 'change_options' ] );
	}

	public function change_options() {
		$this->options->remove_option( 'content' );
	}


	/**
	 * Enqueue Scripts
	 *
	 * Loads the scripts necessary for the current element
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/elements/Tabs/editor.js' ) );
		$this->enqueue_editor_style( Utils::get_file_url( 'dist/elements/Tabs/editor.css' ) );
	}

	/**
	 * Get Children
	 *
	 * Returns an array containing all children of this element.
	 * If the element can have multiple content areas ( for example tabs or accordions ) it will loop trough all areas
	 * and returns all it's children
	 *
	 * @return array<int, mixed>
	 */
	public function get_children() {
		$options             = $this->options;
		$content             = $options->get_value( 'content', __( 'Tab content', 'zionbuilder-pro' ) );
		$child_elements_data = ! empty( $this->content ) ? $this->content : [];

		// Convert content to element
		if ( ! empty( $content ) && empty( $child_elements_data ) ) {
			$element_data = [
				'element_type' => 'zion_text',
				'uid'          => $this->tabs_content_migration_uid,
				'options'      => [
					'content' => $content,
				],
			];

			// Set the content first
			$child_elements_data = [ $element_data ];
		}

		return $child_elements_data;
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$this->render_children();
	}
}
