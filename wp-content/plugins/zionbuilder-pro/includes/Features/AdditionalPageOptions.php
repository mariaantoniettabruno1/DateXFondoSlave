<?php

namespace ZionBuilderPro\Features;

class AdditionalPageOptions {
	const PAGE_CUSTOM_CSS_META_KEY = '_zn_page_custom_css';
	const PAGE_CUSTOM_JS_META_KEY  = '_zn_page_custom_js';

	public function __construct() {
		// Filters
		add_filter( 'zionbuilder/assets/page/css', [ $this, 'add_custom_css_to_page' ], 10, 2 );
		add_filter( 'zionbuilder/assets/page/js', [ $this, 'add_custom_javascript_to_page' ], 10, 2 );
		add_filter( 'zionbuilder/post/page_options_data', [ $this, 'add_data_to_page_options' ], 10, 2 );
		add_filter( 'zionbuilder/templates/export', [ $this, 'add_custom_data_to_template_export' ], 10, 2 );

		// Actions
		add_action( 'zionbuilder/post/save', [ $this, 'on_page_save' ], 10, 3 );
		add_action( 'zionbuilder/schema/page_options', [ $this, 'add_page_options' ] );
		add_action( 'zionbuilder/options_utils/replace_urls_meta_fields', [ $this, 'add_custom_data_to_replace_url' ] );
	}

	public function add_custom_data_to_template_export( $template_data, $request ) {
		$template_id = $request->get_param( 'id' ) ? $request->get_param( 'id' ) : false;

		if ( $template_id ) {
			$template_data['custom_css'] = get_post_meta( $template_id, self::PAGE_CUSTOM_CSS_META_KEY, true );
			$template_data['custom_js']  = get_post_meta( $template_id, self::PAGE_CUSTOM_JS_META_KEY, true );
		} else {
			$template_data['custom_css'] = $request->get_param( 'custom_css' );
			$template_data['custom_js']  = $request->get_param( 'custom_js' );
		}

		return $template_data;
	}

	public function add_custom_data_to_replace_url( $meta_fields ) {
		$meta_fields[] = self::PAGE_CUSTOM_CSS_META_KEY;
		$meta_fields[] = self::PAGE_CUSTOM_JS_META_KEY;

		return $meta_fields;
	}

	public function add_data_to_page_options( $page_options_data, $post_id ) {
		$page_options_data['_custom_css']        = get_post_meta( $post_id, self::PAGE_CUSTOM_CSS_META_KEY, true );
		$page_options_data['_custom_javascript'] = get_post_meta( $post_id, self::PAGE_CUSTOM_JS_META_KEY, true );

		return $page_options_data;
	}

	public function add_custom_css_to_page( $css, $post_id ) {
		// Add post custom css
		$custom_css = get_post_meta( $post_id, self::PAGE_CUSTOM_CSS_META_KEY, true );
		if ( ! empty( $custom_css ) ) {
			$css .= $custom_css;
		}

		return $css;
	}

	public function add_custom_javascript_to_page( $js, $post_id ) {
		// Add post custom js
		$custom_js = get_post_meta( $post_id, self::PAGE_CUSTOM_JS_META_KEY, true );
		if ( ! empty( $custom_js ) ) {
			$js .= $custom_js;
		}

		return $js;
	}

	public function on_page_save( $new_post_data, $page_settings, $post_id ) {
		if ( isset( $page_settings['_custom_css'] ) ) {
			update_post_meta( $post_id, self::PAGE_CUSTOM_CSS_META_KEY, $page_settings['_custom_css'] );
		}

		if ( isset( $page_settings['_custom_javascript'] ) ) {
			update_post_meta( $post_id, self::PAGE_CUSTOM_JS_META_KEY, $page_settings['_custom_javascript'] );
		}
	}

	public function add_page_options( $options ) {
		$custom_css_group = $options->get_option( 'page-options-group.custom-css-group' );
		$custom_css_group->replace_option(
			'_custom_css',
			[
				'type'        => 'code',
				'description' => esc_html__( 'Add extra css that will be applied to this page.', 'zionbuilder-pro' ),
				'title'       => esc_html__( 'Custom css', 'zionbuilder-pro' ),
				'mode'        => 'text/css',
				'css_style'   => [
					[
						'value' => '{{VALUE}}',
					],
				],
			]
		);

		$custom_js_group = $options->get_option( 'page-options-group.custom-js-group' );
		$custom_js_group->replace_option(
			'_custom_javascript',
			[
				'type'        => 'code',
				'description' => esc_html__( 'Add extra javascript that will be applied to this page.', 'zionbuilder-pro' ),
				'title'       => esc_html__( 'Custom javascript', 'zionbuilder-pro' ),
				'mode'        => 'text/javascript',
			]
		);
	}

}
