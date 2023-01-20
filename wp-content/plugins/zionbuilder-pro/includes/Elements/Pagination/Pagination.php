<?php

namespace ZionBuilderPro\Elements\Pagination;

use ZionBuilder\Elements\Element;
use ZionBuilderPro\Utils;
use ZionBuilderPro\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Pagination
 *
 * @package ZionBuilderPro\Elements
 */
class Pagination extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'pagination';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Pagination', 'zionbuilder-pro' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'pro';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'pagination', 'navigation', 'nav' ];
	}

	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-pagination';
	}

	public function options( $options ) {
		$options->add_option(
			'show_numbers',
			[
				'type'    => 'checkbox_switch',
				'default' => true,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Show numbers?', 'zionbuilder-pro' ),
			]
		);

		$options->add_option(
			'show_prev_next',
			[
				'type'    => 'checkbox_switch',
				'default' => true,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Show previous and next?', 'zionbuilder-pro' ),
			]
		);

		$options->add_option(
			'shorten_numbers',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Shorten numbers?', 'zionbuilder-pro' ),
			]
		);

		$options->add_option(
			'previous_text',
			[
				'type'    => 'text',
				'default' => __( '&laquo; Previous', 'zionbuilder-pro' ),
				'title'   => esc_html__( 'Previous text?', 'zionbuilder-pro' ),
			]
		);

		$options->add_option(
			'next_text',
			[
				'type'    => 'text',
				'default' => __( 'Next &raquo;', 'zionbuilder-pro' ),
				'title'   => esc_html__( 'Next text?', 'zionbuilder-pro' ),
			]
		);
	}

	/**
	 * Enqueue element styles for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_style(), enqueue_element_style() functions
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		// Using helper methods will go through caching policy
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Pagination/frontend.css' ) );
	}

	public function before_render( $options ) {
		$show_numbers = $options->get_value( 'show_numbers' );
		$this->set_wrapper_tag( 'nav' );

		if ( ! $show_numbers ) {
			$this->render_attributes->add( 'wrapper', 'class', 'zb-el-pagination--no-numbers' );
		}
	}

	protected function can_render() {
		$query = $this->get_query();
		if ( $query && (int) $query->max_num_pages === 1 ) {
			return false;
		}

		return true;
	}

	/**
	 * Render
	 *
	 * Will render the element based on options
	 *
	 * @param mixed $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$query           = $this->get_query();
		$show_numbers    = $options->get_value( 'show_numbers' );
		$show_prev_next  = $options->get_value( 'show_prev_next' );
		$shorten_numbers = $options->get_value( 'shorten_numbers' );
		$previous_text   = $options->get_value( 'previous_text' );
		$next_text       = $options->get_value( 'next_text' );

		if ( ! $query ) {
			return '';
		}

		if ( $show_numbers ) {
			echo \paginate_links(
				[
					'prev_next' => $show_prev_next,
					'prev_text' => $show_prev_next ? $previous_text : false,
					'next_text' => $show_prev_next ? $next_text : false,
					'show_all'  => ! $shorten_numbers,
					'total'     => $query->max_num_pages,
					'current'   => is_front_page() ? max( 1, get_query_var( 'page' ) ) : max( 1, get_query_var( 'paged' ) ),
				]
			);
		} elseif ( $show_prev_next ) {
			echo \get_previous_posts_link( $previous_text );
			echo \get_next_posts_link( $next_text, $query->max_num_pages );
		}

	}

	public function get_query() {
		$active_repeater_provider = Plugin::instance()->repeater->get_active_provider();

		if ( $active_repeater_provider ) {
			$query_config = $active_repeater_provider->get_query();

			return isset( $query_config['query'] ) ? $query_config['query'] : false;
		}

		if ( isset( $provider_data['query'] ) ) {
			return $provider_data['query'];
		}

		return false;
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'page_numbers',
			[
				'title'                   => esc_html__( 'Page numbers styling', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .page-numbers',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'current_page',
			[
				'title'                   => esc_html__( 'Current page styling', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .page-numbers.current',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'previous_text',
			[
				'title'                   => esc_html__( 'Previous text styling', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .page-numbers.prev',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'next_text',
			[
				'title'                   => esc_html__( 'Next text styling', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .page-numbers.next',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}
