<?php

namespace ZionBuilderPro;

use ZionBuilderPro\Plugin;
use Zionbuilder\Plugin as FreePlugin;
use ZionBuilder\Elements\Style;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Settings
 *
 * @package ZionBuilderPro
 *
 * @throws \LogicException if manifest file is not found
 */
class Assets {
	private $inline_css = '';

	public function __construct() {
		add_action( 'zionbuilder/assets/enqueue_assets_for_post', [ $this, 'maybe_add_inline_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'on_enqueue_scripts' ] );
	}

	public function on_enqueue_scripts() {
		wp_register_style( 'zb-pro-inline-css', false );
		wp_enqueue_style( 'zb-pro-inline-css' );

		wp_add_inline_style( 'zb-pro-inline-css', $this->inline_css );
	}

	public function maybe_add_inline_styles( $post_id ) {
		$theme_builder_templates = Plugin::instance()->theme_builder->get_registered_template_ids();

		if ( in_array( $post_id, $theme_builder_templates ) ) {
			$this->extract_dynamic_css( $post_id );
		}

	}

	public function extract_dynamic_css( $post_id ) {
		$template_data = FreePlugin::instance()->renderer->get_content_for_area( $post_id );

		foreach ( $template_data as $element ) {
			$element_intance = FreePlugin::instance()->renderer->get_element_instance( $element['uid'] );

			if ( $element_intance ) {
				$this->extract_dynamic_css_for_element( $element_intance );
			}
		}
	}

	public function extract_dynamic_css_for_element( $element_instance ) {
		$style_options = $element_instance->options->get_value( '_styles', false );

		if ( false !== $style_options ) {
			$only_dynamic_values      = $this->get_only_dynamic_values( $style_options );
			$styles_with_dynamic_data = Plugin::instance()->dynamic_content_manager->apply_dynamic_content( $only_dynamic_values );
			$registered_styles        = $element_instance->get_style_elements_for_editor();

			if ( ! empty( $styles_with_dynamic_data ) && is_array( $registered_styles ) ) {
				foreach ( $registered_styles as $id => $style_config ) {
					if ( ! empty( $styles_with_dynamic_data[$id] ) ) {
						$css_selector = $element_instance->get_css_selector();
						$css_selector = str_replace( '{{ELEMENT}}', $css_selector, $style_config['selector'] );
						$css_selector = apply_filters( 'zionbuilder/element/full_css_selector', array( $css_selector ), $element_instance );

						$this->inline_css .= Style::get_css_from_selector( $css_selector, $styles_with_dynamic_data[$id] );
					}
				}
			}
		}

		$children = $element_instance->get_children();
		if ( is_array( $children ) ) {
			foreach ( $children as $element ) {
				$child_element = FreePlugin::instance()->renderer->get_element_instance( $element['uid'] );

				if ( $child_element ) {
					$this->extract_dynamic_css_for_element( $child_element );
				}
			}
		}
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
}
