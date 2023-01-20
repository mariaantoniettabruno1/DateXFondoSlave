<?php

namespace ZionBuilderPro\DynamicContent;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class BaseField
 *
 * @package ZionBuilderPro\DynamicContent
 */
abstract class BaseField {
	const CATEGORY_HIDDEN = 'CATEGORY_HIDDEN';
	const CATEGORY_TEXT   = 'TEXT';
	const CATEGORY_LINK   = 'LINK';
	const CATEGORY_IMAGE  = 'IMAGE';

	// Groups
	const GROUP_POST    = 'GROUP_POST';
	const GROUP_ARCHIVE = 'GROUP_ARCHIVE';

	/**
	 * Get Id
	 *
	 * Returns the field id
	 *
	 * @throws \Exception
	 *
	 * @return string
	 */
	public function get_id() {
		throw new \Exception( 'get_id() must be implemented in child class of BaseField' );
	}

	/**
	 * Render the dynamic field's value
	 *
	 * @param mixed $options
	 *
	 * @throws \Exception
	 */
	public function render( $options ) {
		throw new \Exception( 'render() must be implemented in child class of BaseField' );
	}

	/**
	 * Retrieve the content
	 *
	 * @param mixed $options
	 *
	 * @throws \Exception
	 *
	 * @return false|string
	 */
	public function get_content( $options ) {
		ob_start();
		$this->render( $options );
		return ob_get_clean();
	}

	/**
	 * Will load the field only if it passes the check
	 *
	 * @return boolean
	 */
	public function can_load() {
		return true;
	}

	/**
	 * Get Data
	 *
	 * Will return the field data for live fields. The data will be loaded on Editor screen
	 *
	 * @return []
	 */
	public function get_data() {
		return false;
	}


	/**
	 * Get Category
	 *
	 * Will return the field category
	 *
	 * @throws \Exception
	 *
	 * @return []
	 */
	abstract public function get_category();


	/**
	 * Get Group
	 *
	 * Will return the field group
	 *
	 * @throws \Exception
	 *
	 * @return []
	 */
	abstract public function get_group();


	/**
	 * Get options
	 *
	 * Will return the list of options for this field
	 *
	 * @return [] The list of options for this field
	 */
	public function get_options() {
		return [];
	}
}
