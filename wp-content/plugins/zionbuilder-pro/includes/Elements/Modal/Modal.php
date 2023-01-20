<?php

namespace ZionBuilderPro\Elements\Modal;

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
class Modal extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'modal';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Modal', 'zionbuilder-pro' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'layout';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return array( 'modal', 'popup' );
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

	public function options( $options ) {
		$options->add_option(
			'modal_state',
			array(
				'type'             => 'custom_selector',
				'title'            => __( 'Modal state', 'zionbuilder-pro' ),
				'columns'          => 3,
				'options'          => array(
					array(
						'name' => __( 'Open', 'zionbuilder-pro' ),
						'id'   => 'open',
					),
					array(
						'name' => __( 'Inline', 'zionbuilder-pro' ),
						'id'   => 'inline',
					),
					array(
						'name' => __( 'Hidden', 'zionbuilder-pro' ),
						'id'   => 'hidden',
					),
				),
				'render_attribute' => array(
					array(
						'attribute' => 'class',
						'value'     => 'zb-modal--{{VALUE}}',
					),
				),
			)
		);

		$modal_triggers = $options->add_group(
			'modal_trigger',
			array(
				'type'  => 'accordion_menu',
				'title' => __( 'Triggers', 'zionbuilder-pro' ),
			)
		);

		$triggers = $modal_triggers->add_option(
			'trigger_types',
			array(
				'type'               => 'repeater',
				'add_button_text'    => __( 'Add new trigger', 'zionbuilder-pro' ),
				'item_title'         => 'type',
				'default_item_title' => 'trigger %s',
				'reset_group'        => array(
					'option' => 'type',
				),
				'title'              => __( 'Triggers', 'zionbuilder-pro' ),
			)
		);

		$triggers->add_option(
			'type',
			array(
				'type'    => 'select',
				'title'   => __( 'Trigger type', 'zionbuilder-pro' ),
				'default' => 'page_load',
				'options' => array(
					array(
						'id'   => 'page_load',
						'name' => __( 'Page Load', 'zionbuilder-pro' ),
					),
					array(
						'id'   => 'scroll',
						'name' => __( 'Scroll', 'zionbuilder-pro' ),
					),
					array(
						'id'   => 'exit_intent',
						'name' => __( 'Exit intent', 'zionbuilder-pro' ),
					),
					array(
						'id'   => 'page_clicks',
						'name' => __( 'Page clicks', 'zionbuilder-pro' ),
					),
					array(
						'id'   => 'selector_click',
						'name' => __( 'Element click', 'zionbuilder-pro' ),
					),
				),
			)
		);

		// Page load
		$page_load = $triggers->add_group(
			'page_load',
			array(
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => array(
					array(
						'option' => 'type',
						'value'  => array( 'page_load' ),
					),
				),
			)
		);

		$page_load->add_option(
			'delay',
			array(
				'type'    => 'number',
				'title'   => __( 'Delay', 'zionbuilder-pro' ),
				'default' => 0,
				'min'     => 0,
				'suffix'  => 'ms',
			)
		);

		// Scroll
		$scroll_group = $triggers->add_group(
			'scroll',
			array(
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => array(
					array(
						'option' => 'type',
						'value'  => array( 'scroll' ),
					),
				),
			)
		);

		$scroll_group->add_option(
			'direction',
			array(
				'type'    => 'custom_selector',
				'title'   => __( 'Scroll direction', 'zionbuilder-pro' ),
				'default' => 'down',
				'options' => array(
					array(
						'name' => __( 'Up', 'zionbuilder-pro' ),
						'id'   => 'up',
					),
					array(
						'name' => __( 'Down', 'zionbuilder-pro' ),
						'id'   => 'down',
					),
				),
			)
		);

		$scroll_group->add_option(
			'ammount',
			array(
				'type'       => 'number',
				'title'      => __( 'Scroll direction ammount', 'zionbuilder-pro' ),
				'suffix'     => '%',
				'default'    => 0,
				'dependency' => array(
					array(
						'option' => 'direction',
						'value'  => array( 'down' ),
					),
				),
			)
		);

		// Page clicks
		$page_clicks = $triggers->add_group(
			'page_clicks',
			array(
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => array(
					array(
						'option' => 'type',
						'value'  => array( 'page_clicks' ),
					),
				),
			)
		);

		$page_clicks->add_option(
			'clicks',
			array(
				'type'    => 'number',
				'title'   => __( 'Number of clicks', 'zionbuilder-pro' ),
				'default' => 1,
			)
		);

		// Selector click
		$selector_click = $triggers->add_group(
			'selector_click',
			array(
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => array(
					array(
						'option' => 'type',
						'value'  => array( 'selector_click' ),
					),
				),
			)
		);

		$selector_click->add_option(
			'selector',
			array(
				'type'  => 'text',
				'title' => __( 'Select element', 'zionbuilder-pro' ),
			)
		);

		/**
		 * Popup conditions
		 */
		// $modal_triggers = $options->add_group(
		//  'conditions',
		//  [
		//      'type'        => 'accordion_menu',
		//      'title'       => __( 'Conditions', 'zionbuilder-pro' ),
		//      'description' => __( 'Set a delay in seconds before the popup appears', 'zionbuilder-pro' ),
		//  ]
		// );

		/**
		 * Popup behaviour
		 */
		$behaviour = $options->add_group(
			'behaviour',
			array(
				'type'  => 'accordion_menu',
				'title' => __( 'Behaviour', 'zionbuilder-pro' ),
			)
		);

		$behaviour->add_option(
			'close_on_outside_click',
			array(
				'type'    => 'custom_selector',
				'title'   => __( 'Close on backdrop click?', 'zionbuilder-pro' ),
				'default' => 'yes',
				'options' => array(
					array(
						'name' => __( 'Yes', 'zionbuilder-pro' ),
						'id'   => 'yes',
					),
					array(
						'name' => __( 'No', 'zionbuilder-pro' ),
						'id'   => 'no',
					),
				),
			)
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Modal/frontend.css' ) );
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Modal/editor.css' ) );
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		wp_enqueue_script( 'zb-modal' );

		$this->enqueue_editor_script( Utils::get_file_url( 'dist/elements/Modal/editor.js' ) );
		wp_enqueue_script( 'zb-element-modal', Utils::get_file_url( 'dist/elements/Modal/frontend.js' ), array(), Plugin::instance()->get_version(), true );
	}

	public function before_render( $options ) {
		$triggers_values   = $options->get_value( 'trigger_types' );
		$close_on_backdrop = $options->get_value( 'close_on_outside_click' ) === 'yes';

		$config   = array();
		$triggers = array();

		if ( is_array( $triggers_values ) ) {
			foreach ( $triggers_values as $trigger_config ) {

				switch ( $trigger_config['type'] ) {
					case 'page_load':
						$triggers[] = array(
							'type'    => 'pageLoad',
							'options' => array(
								'delay' => isset( $trigger_config['delay'] ) ? $trigger_config['delay'] : 0,
							),
						);
						break;

					case 'scroll':
						$triggers[] = array(
							'type'    => 'pageScroll',
							'options' => array(
								'direction'     => isset( $trigger_config['direction'] ) ? $trigger_config['direction'] : 'down',
								'scrollAmmount' => isset( $trigger_config['ammount'] ) ? $trigger_config['ammount'] : 0,
							),
						);
						break;

					case 'exit_intent':
						$triggers[] = array(
							'type' => 'exitIntent',
						);
						break;

					case 'page_clicks':
						$triggers[] = array(
							'type'    => 'click',
							'options' => array(
								'clicks' => isset( $trigger_config['clicks'] ) ? $trigger_config['clicks'] : 1,
							),
						);
						break;

					case 'selector_click':
						$triggers[] = array(
							'type'    => 'selector_click',
							'options' => array(
								'selector' => isset( $trigger_config['selector'] ) ? $trigger_config['selector'] : null,
							),
						);
						break;

					default:
						# code...
						break;
				}
			}
		}

		$config['triggers']             = $triggers;
		$config['closeOnBackdropClick'] = $close_on_backdrop;

		$this->render_attributes->add( 'wrapper', 'data-zion-modal-config', wp_json_encode( $config ) );
		$this->render_attributes->add( 'wrapper', 'class', 'zb-modal' );
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

		echo "<div {$this->render_attributes->get_attributes_as_string( 'modal_content', [ 'class' => [ 'zb-modalContent' ] ] )}>";
			$this->render_children();
			$this->render_tag( 'div', 'close_button', '', array( 'class' => array( 'zb-modalClose' ) ) );
		echo '</div>';
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'modal_content_styles',
			array(
				'title'      => esc_html__( 'Modal content styles', 'zionbuilder-pro' ),
				'selector'   => '{{ELEMENT}} .zb-modalContent',
				'render_tag' => 'modal_content',
			)
		);

		$this->register_style_options_element(
			'modal_close_button',
			array(
				'title'      => esc_html__( 'Modal close button', 'zionbuilder-pro' ),
				'selector'   => '{{ELEMENT}} .zb-modalClose',
				'render_tag' => 'close_button',
			)
		);
	}

}
