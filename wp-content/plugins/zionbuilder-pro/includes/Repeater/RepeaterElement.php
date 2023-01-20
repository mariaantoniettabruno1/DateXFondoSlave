<?php

namespace ZionBuilderPro\Repeater;

use ZionBuilderPro\Repeater;
use ZionBuilder\Plugin as FreePlugin;
use ZionBuilder\Elements\Style;
use ZionBuilderPro\Plugin;
use ZionBuilder\PageAssets;

class RepeaterElement {
	private $element           = null;
	private $repeater_provider = null;

	public function __construct( $element ) {
		$this->element = $element;
	}

	public function render_element( $extra_data ) {
		$element_instance = $this->element;

		// If this is a repeater provider, set the query
		if ( Repeater::is_repeater_provider( $element_instance ) ) {
			$provider_config = Repeater::get_repeater_provider_config( $element_instance );
			Plugin::$instance->repeater->set_active_provider( $provider_config );
		}

		// Check to see if this is the main repeater consumer
		if ( Repeater::is_repeater_consumer( $element_instance ) ) {
			$active_provider = Plugin::$instance->repeater->get_active_provider();
			if ( ! $active_provider ) {
				return;
			}

			// Set current loop
			$consumer_config = Repeater::get_repeater_consumer_config( $element_instance );
			$active_provider->start_loop( $consumer_config );

			$index = 0;

			while ( $active_provider->have_items() ) {
				$active_provider->the_item();

				$cloned_element = $this->setup_repeated_element( $element_instance, $index );
				$cloned_element->do_element_render( $extra_data );

				$active_provider->next();
				$active_provider->reset_item();
				$index++;
			}

			// Reset consumer
			$active_provider->stop_loop();
		} else {
			// This can only be a repeater provider. We just need to set the provider data and render normally
			$this->element->do_element_render( $extra_data );
		}

		// If this is a repeater provider, reset the query
		if ( Repeater::is_repeater_provider( $element_instance ) ) {
			Plugin::$instance->repeater->reset_active_provider();
		}
	}

	/**
	 * Change all repeated element instances and replace HTML ids with css classes
	 *
	 * @param Element $element_instance
	 * @param integer $index
	 *
	 * @return Element
	 */
	private function setup_repeated_element( $element_instance, $index ) {
		$element_css_id = $element_instance->get_element_css_id();
		$css_class      = sprintf( '%s_%s', $element_css_id, $index );

		// Create a clone
		$element_data            = $element_instance->data;
		$element_data['uid']     = $css_class;
		$cloned_element_instance = FreePlugin::instance()->renderer->register_element_instance( $element_data );

		$clone_children = $cloned_element_instance->get_children();
		if ( is_array( $clone_children ) ) {
			foreach ( $clone_children as $child_index => $child_element ) {
				$child_element_instance                         = FreePlugin::instance()->renderer->get_element_instance( $child_element['uid'] );
				$cloned_child                                   = $this->setup_repeated_element( $child_element_instance, $index );
				$cloned_element_instance->content[$child_index] = $cloned_child->data;
			}
		}

		// Set CSS class
		$cloned_element_instance->render_attributes->add( 'wrapper', 'class', $element_css_id );
		$cloned_element_instance->render_attributes->add( 'wrapper', 'class', $css_class );
		$cloned_element_instance->render_attributes->add( 'wrapper', 'id', $css_class );

		// Check for dynamic background image
		if ( $cloned_element_instance->options->get_value( '_styles.wrapper.styles.default.default.__dynamic_content__.background-image', null ) ) {
			$background_styles        = $cloned_element_instance->options->get_value( '_styles.wrapper.styles.default.default' );
			$styles_with_dynamic_data = Plugin::instance()->dynamic_content_manager->apply_dynamic_content( $background_styles );

			if ( ! empty( $styles_with_dynamic_data['background-image'] ) ) {
				$cloned_element_instance->render_attributes->add( 'wrapper', 'style', "background-image: url('{$styles_with_dynamic_data['background-image']}')" );
			}
		}

		return $cloned_element_instance;
	}

	/**
	 * Returns only the dynamic data values
	 *
	 * @param array $model
	 *
	 * @return array
	 */
	public function get_only_dynamic_values( $model ) {
		$model_to_return = [];
		if ( is_array( $model ) ) {
			foreach ( $model as $key => $value ) {
				if ( $key === '__dynamic_content__' || $key === 'classes' || $key === 'attributes' ) {
					$model_to_return[$key] = $value;
				} else {
					$dynamic_values = $this->get_only_dynamic_values( $value );
					if ( null !== $dynamic_values ) {
						$model_to_return[$key] = $dynamic_values;
					}
				}
			}
		}

		if ( count( $model_to_return ) > 0 ) {
			return $model_to_return;
		}

		return null;
	}

	/**
	 * Removes dynamic data values
	 *
	 * @param array $model
	 *
	 * @return array
	 */
	public function remove_dynamic_values( $model ) {
		$model_to_return = [];
		if ( is_array( $model ) ) {
			foreach ( $model as $key => $value ) {
				if ( $key === '__dynamic_content__' ) {
					continue;
				} else {
					if ( is_array( $value ) ) {
						$model_to_return[$key] = $this->remove_dynamic_values( $value );
					} else {
						$model_to_return[$key] = $value;
					}
				}
			}
		}

		return $model_to_return;
	}
}
