<?php
namespace ZiUltimate;

use ZionBuilderPro\Plugin;
use ZiUltimate\UltimateElements;
use ZionBuilderPro\ElementConditions\Rest;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Helpers {
	private $zu_active_els = [];

	function __construct() {
		$this->zu_active_els = (array) get_option('ziultimate_active_els');

		if( in_array('tglcnt', $this->zu_active_els ) ) {
			add_action( 'zionbuilder\element\zion_section\options', [ $this, 'change_element_options' ] );
			add_action( 'zionbuilder\element\zion_column\options', [ $this, 'change_element_options' ] );
			add_action( 'zionbuilder\element\container\options', [ $this, 'change_element_options' ] );
		}

		add_action( 'zionbuilderpro/repeater_provider/acf_repeater', [ $this, 'change_acf_repeater_provider_options' ] );
		add_action( 'zionbuilder/schema/advanced_options', [ $this, 'add_repeater_options' ] );

		if( class_exists( 'ZionBuilderPro\Plugin' ) && ! UltimateElements::isBuilderEditor() ) {
			add_action( 'zionbuilder/element/before_render', [ $this, 'zu_do_elements_before_render' ], 10, 2 );
			add_action( 'zionbuilder/element/after_render', [ $this, 'zu_add_content_after_element'], 10, 2);

			add_action( 'admin_init', [ $this, 'zu_taxonomy_add_columns' ] );
		} 

		add_action( 'zionbuilder/element/before_element_extract_assets', [ $this, 'zu_generate_own_css' ], 990 );
	}

	public function zu_taxonomy_add_columns() {
		$taxonomies = ( new Rest() )->get_taxonomies('');
		if( $taxonomies ) {
			foreach( $taxonomies as $taxonomy ) {
				if( isset( $taxonomy['is_label'] ) && $taxonomy['is_label'] >= 1 )
					continue;

				add_action( "manage_edit-{$taxonomy['id']}_columns", [$this, 'add_custom_columns' ] );
				add_action( "manage_{$taxonomy['id']}_custom_column", [$this, 'tax_add_value_custom_columns' ], 10, 3 );
			}
		}
	}

	public function add_custom_columns( $columns ) {
		$new_cols['cb'] = array_shift( $columns );
		$new_cols['term_id'] = __('Term ID');
    	
    	return array_merge( $new_cols, $columns );
	}

	public function tax_add_value_custom_columns( $content,$column_name,$term_id ) {

		if ($column_name == 'term_id' ) {
			$content = $term_id;
		}

    	return $content;
	}

	public function zu_generate_own_css( $element ) { //$css, $options, 

		$css = '';

		if( $element->get_type() == 'zu_free_shipping_notice' ) {
			$options = $element->options;
			$pb_hide = $options->get_value( 'pb_hide', false );
			$action = $options->get_value( 'after_action', 'hide' );
			$outer_wrap_sel = $options->get_value( 'outer_wrap_sel', false );
			$cta_sel = $options->get_value( 'cta_sel', false );

			$css .= 'body.' . $element->uid . ':not(.' . $element->uid . '-hide-fstxt) .' . $element->uid .' {
				height: 0!important;
				padding:0!important;
				margin:0!important;
			}
			body.' . $element->uid . ':not(.' . $element->uid . '-hide-fstxt) .' . $element->uid .' .free-shipping-content {
				opacity: 0;
			}';

			if( !empty( $outer_wrap_sel ) ) {
				$wrapper_selector = str_replace( array( '#', '.'), '', $outer_wrap_sel );

				$css .= $outer_wrap_sel .'{
					-webkit-transition: all ' . $options->get_value('anim_td', 0.15) . 's ease-in-out; 
					-moz-transition: all ' . $options->get_value('anim_td', 0.15) . 's ease-in-out; 
					transition: all ' . $options->get_value('anim_td', 0.15) . 's ease-in-out;
					position: relative;
				}
				body.' . $wrapper_selector . ':not(.' . $wrapper_selector . '-hide-fstxt) ' . $outer_wrap_sel .' {
					height: 0!important;
					max-height: 0!important;
					min-height: 0!important;
					padding:0!important;
					margin:0!important;
					border: none;
				}
				body.' . $wrapper_selector . ':not(.' . $wrapper_selector . '-hide-fstxt) ' . $outer_wrap_sel .' > * {
					opacity: 0;
				}';

				
				if( ! empty( $pb_hide ) || $action == 'hide' ) {
					$css .= 'body.' . $wrapper_selector . ' .' . $element->uid .' .fsn-progress-bar-wrap {display: none;}';
				}

				if( ! empty( $cta_sel ) ) {
					$css .= 'body.' . $wrapper_selector . ' ' . $cta_sel . '{display: none;}';
				}	
			} else {
				if( ! empty( $pb_hide ) || $action == 'hide' ) {
					$css .= 'body.' . $element->uid . ' .' . $element->uid .' .fsn-progress-bar-wrap {display: none;}';
				}

				if( ! empty( $cta_sel ) ) {
					$css .= 'body.' . $element->uid . ' ' . $cta_sel . '{display: none;}';
				}
			}
		}

		if( $element->get_type() == 'zu_lightbox' ) {
			$options = $element->options;
			$selectors = $options->get_value( 'trigger_selector', false );
			if( ! empty( $selectors ) ) {
				$css .=  $selectors . '{cursor: pointer}';
			}
		}

		$element->custom_css->add_raw_css( $css );
	}

	public function zu_do_elements_before_render( $element_instance, $extra_render_data ) {
		if ( Plugin::instance()->repeater::is_repeater_provider( $element_instance ) ) {
			$element_instance->render_attributes->add( 'wrapper', 'class', 'zu-repeater-wrapper' );

			if ( ! empty( $element_instance->content ) && is_array( $element_instance->content ) && in_array( 'infscroll', $this->zu_active_els ) ) {
				foreach ( $element_instance->content as $child_content_data ) {
					if( $child_content_data['element_type'] == 'zu_infinite_scroll' ) {
						$element_instance->render_attributes->add( 
							'wrapper', 
							'class', 
							'zu-infinite-scroll-container ' . $child_content_data['uid']
						);
						break;
					}
				}
			}
		}

		if( Plugin::instance()->repeater::is_repeater_consumer( $element_instance ) ) 
		{
			$element_instance->render_attributes->add(
				'wrapper', 
				'class',
				'zu-consumer-wrapper',
			);
		}

		if( in_array( $element_instance->get_type(), [ 'zion_section', 'zion_column', 'container' ] ) ) {
			$accordion = $element_instance->options->get_value( 'accordion', 'no' );
			$first_item_active = $element_instance->options->get_value( 'first_item_active', 'no' );
			if( $accordion == 'yes' ) {
				$element_instance->render_attributes->add(
					'wrapper', 
					'class',
					'zu-toggle-content--accordion',
				);
			}

			if( $first_item_active == 'yes' ) {
				$element_instance->render_attributes->add(
					'wrapper', 
					'data-tglcnt-active-item',
					'yes',
				);
			}
		}
	}

	public function zu_add_content_after_element( $element_instance, $extra_render_data ) {
		if( Plugin::instance()->repeater::is_repeater_consumer( $element_instance ) ) 
		{
			$is_content_after_nth_post = $element_instance->options->get_value( '_advanced_options.is_content_after_nth_post', false );

			if( $is_content_after_nth_post ) {
				$show_on_first_page = $element_instance->options->get_value( '_advanced_options.is_show_first_page', false );
				$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

				if( $show_on_first_page && $paged >= 2 ) {
					return;
				}

				$number 		= (int) Plugin::instance()->repeater->get_active_provider()->get_real_index() + 1 ;
				$content_type 	= $element_instance->options->get_value( '_advanced_options.content_type', 'html' );
				$content 		= ( $content_type == 'html' ) ? $element_instance->options->get_value( '_advanced_options.content_html' ) : do_shortcode( $element_instance->options->get_value( '_advanced_options.shortcode' ) );
				$will_repeat 	= ! $element_instance->options->get_value( '_advanced_options.will_repeat', false ) ? 'no_repeat' : 'repeat';
				$nth_post_position = absint( $element_instance->options->get_value( '_advanced_options.nth_post_position', 3 ) );

				switch( $will_repeat ) {
					case 'repeat' :
							if( $number % $nth_post_position == 0 ) {
								echo $content;
							}
						break;

					case 'no_repeat' : 
							if( $number / $nth_post_position == 1 ) {
								echo $content;
							}
						break;

					default: break;
				}
			}
		}

		return;
	}

	/**
	 * Settings for Toggle Content element
	 */
	public function change_element_options( $options ) {
		$acrd = $options->add_group(
			'toggle_content_panel',
			[
				'type' 	=> 'panel_accordion',
				'title' => esc_html__('Toggle Content Options', 'ziultimate'),
				'collapsed' => true
			]
		);

		$acrd->add_option(
			'accordion',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __( 'Enable accordion effect?', 'ziultimate' ),
				'default' 	=> 'no',
				'options' 	=>[
					[
						'id' 	=> 'no',
						'name' 	=> esc_html__('No')
					],
					[
						'id' 	=> 'yes',
						'name' 	=> esc_html__('Yes')
					]
				]
			]
		);

		$acrd->add_option(
			'first_item_active',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> __( 'First item active by default?', 'ziultimate' ),
				'default' 	=> 'no',
				'options' 	=>[
					[
						'id' 	=> 'no',
						'name' 	=> esc_html__('No')
					],
					[
						'id' 	=> 'yes',
						'name' 	=> esc_html__('Yes')
					]
				]
			]
		);
	}

	/**
	 * Option for ACF Repeater provider
	 */
	public function change_acf_repeater_provider_options( $options ) {
		$options->add_option(
			'options_page',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __( 'Picking data from options page?', 'ziultimate' ),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);
	}

	/**
	 * Adds the repeater related options to Element Advanced options
	 *
	 * @param [type] $options
	 * @return void
	 */
	public function add_repeater_options( $options ) {
		$repeater_options = $options->get_option( 'repeater-provider-options' );
		$repeater_options->add_option(
			'is_content_after_nth_post',
			[
				'type'    		=> 'checkbox_switch',
				'default' 		=> false,
				'layout'  		=> 'inline',
				'title' 		=> esc_html__( 'Show content after nth post?', 'ziultimate' ),
				'description' 	=> __('Preview will not show on the builder editor.', 'ziultimate'),
				'dependency' 	=> [
					[
						'option' => 'is_repeater_consumer',
						'value'  => [ true ],
					],
				],
			]
		);

		$repeater_options->add_option(
			'nth_post_position',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Nth post', 'ziultimate' ),
				'placeholder' 	=> __('Enter number like 2, 3, 5, etc', 'ziultimate'),
				'description' 	=> __('Content will show after this post.', 'ziultimate'),
				'default' 		=> 3,
				'dependency' 	=> [
					[
						'option' => 'is_content_after_nth_post',
						'value'  => [ true ],
					],
				],
			]
		);

		$repeater_options->add_option(
			'content_type',
			[
				'type' 			=> 'custom_selector',
				'default' 		=> 'html',
				'title' 		=> esc_html__( 'Content type', 'ziultimate' ),
				'options' 		=> [
					[
						'id' 	=> 'html',
						'name' 	=> esc_html__('HTML')
					],
					[
						'id' 	=> 'shortcode',
						'name' 	=> esc_html__('Shortcode')
					]
				],
				'dependency' 	=> [
					[
						'option' => 'is_content_after_nth_post',
						'value'  => [ true ],
					],
				],
			]
		);

		$repeater_options->add_option(
			'content_html',
			[
				'type'        => 'code',
				'description' => __( 'Using this option you can enter you own custom HTML code. If you plan on adding CSS or JavaScript, wrap the codes into <style type="text/css">...</style> respectively <script>...</script> . Please make sure your JS code is fully functional as it might break the entire page!!', 'zionbuilder' ),
				'title'       => esc_html__( 'Custom html', 'zionbuilder' ),
				'mode'        => 'htmlmixed',
				'default'     => esc_html__( '// Your custom HTML or Adsense code here', 'zionbuilder' ),
				'dependency' => [
					[
						'option' => 'content_type',
						'value'  => [ 'html' ],
					],
					[
						'option' => 'is_content_after_nth_post',
						'value'  => [ true ],
					],
				],
			]
		);

		$repeater_options->add_option(
			'shortcode',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Shortcode', 'ziultimate' ),
				'placeholder' 	=> '[caption] Shortcode [/caption]',
				'dependency' 	=> [
					[
						'option' => 'content_type',
						'value'  => [ 'shortcode' ],
					],
					[
						'option' => 'is_content_after_nth_post',
						'value'  => [ true ],
					],
				],
			]
		);

		$repeater_options->add_option(
			'will_repeat',
			[
				'type' 			=> 'checkbox_switch',
				'default' 		=> false,
				'layout'  		=> 'inline',
				'title' 		=> esc_html__( 'Will repeat the content?', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' => 'is_content_after_nth_post',
						'value'  => [ true ],
					],
				],
			]
		);

		$repeater_options->add_option(
			'is_show_first_page',
			[
				'type' 			=> 'checkbox_switch',
				'default' 		=> false,
				'layout' 		=> 'inline',
				'title' 		=> esc_html__( 'Show on first page only?', 'ziultimate' ),
				'dependency' 	=> [
					[
						'option' => 'is_content_after_nth_post',
						'value'  => [ true ],
					],
				],
			]
		);
	}
}