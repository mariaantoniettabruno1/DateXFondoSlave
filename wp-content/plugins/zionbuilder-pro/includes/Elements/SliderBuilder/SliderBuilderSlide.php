<?php

namespace ZionBuilderPro\Elements\SliderBuilder;

use ZionBuilder\Elements\Element;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Text
 *
 * @package ZionBuilder\Elements
 */
class SliderBuilderSlide extends Element {

	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'slider_builder_slide';
	}


	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Slider builder Slide', 'zionbuilder' );
	}


	/**
	 * Is child
	 *
	 * Will register the current element as a child of another
	 *
	 * Child elements are not visible in add elements popup and cannot be
	 * interacted with them directly
	 *
	 * @return boolean True in case this is a child element
	 */
	public function is_child() {
		return true;
	}

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

	public function get_sortable_content_orientation() {
		return 'vertical';
	}

		/**
	 * Sets wrapper css classes
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function before_render( $options ) {
		$attributes_as_array = $this->inject('slider_builder_slide_attrs', []);

		if (is_array( $attributes_as_array )) {
			foreach ($attributes_as_array as $key => $value) {
				$this->render_attributes->add( 'wrapper', $key, $value );
			}
		}

		$this->render_attributes->add( 'wrapper', 'class', 'swiper-slide' );
	}


	/**
	 * Render
	 *
	 * Will render the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		?>
			<?php $this->render_children(); ?>
		<?php
	}
}
