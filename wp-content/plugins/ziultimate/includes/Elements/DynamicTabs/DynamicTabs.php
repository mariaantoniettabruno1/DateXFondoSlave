<?php
namespace ZiUltimate\Elements\DynamicTabs;

use ZiUltimate\UltimateElements;
use ZiUltimate\Admin\License;
use ZiUltimate\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class DynamicTabs
 *
 * @package ZiUltimate\Elements
 */
class DynamicTabs extends UltimateElements {

	/*
	 * Holds the term id and passing to the hook
	 */
	public $cat_id = 0;

	/*
	 * Holds the post id and passing to the hook
	 */
	public $post_ID = 0;

	/*
	 * Holds the taxonomy name
	 */ 
	public $taxonomy = 'category';
	
	public function get_type() {
		return 'zu_dynamic_tabs';
	}

	public function get_name() {
		return __( 'Dynamic Tabs', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'tabs', 'dynamic tabs', 'acf', 'acf tabs', 'metabox' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_category() {
		return $this->zu_elements_category();
	}

	public function get_element_icon() {
		return 'element-tabs';
	}

	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'With this tool you can build the dynamic tabs.';
			$options->add_option(
				'el',
				[
					'type' 		=> 'html',
					'content' 	=> self::getHTMLContent($title, $description)
				]
			);

			return;
		}

		$options->add_option(
			'layout',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Layout'),
				'default' 	=> 'horizontal',
				'width' 	=> 45,
				'options' 	=> [
					[
						'name' 	=> esc_html__('Horizontal'),
						'id'	=> 'horizontal'
					],
					[
						'name' 	=> esc_html__('Vertical'),
						'id' 	=> 'vertical'
					]
				]
			]
		);

		$options->add_option(
			'template',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('TemplateFondo'),
				'default' 	=> 'no-gaps',
				'width' 	=> 55,
				'options' 	=> [
					[
						'name' 	=> esc_html__('Custom', 'ziultimate'),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__('Gaps between tabs', 'ziultimate'),
						'id' 	=> 'with-gaps'
					],
					[
						'name' 	=> esc_html__('No gaps between tabs', 'ziultimate'),
						'id'	=> 'no-gaps'
					],
					[
						'name' 	=> esc_html__('No borders arround the tabs', 'ziultimate'),
						'id' 	=> 'no-borders'
					]
				]
			]
		);

		$options->add_option(
			'tabindex',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Tab Index'),
				'placeholder' 	=> 10,
				'dynamic' 		=> [
					'enabled' => true
				]
			]
		);

		$dyndata = $options->add_group(
			'data_config',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Dynamic Data Config', 'ziultimate')
			]
		);

		$dyndata->add_option(
			'source',
			[
				'type' 		=> 'select',
				'title' 	=> __('Source', 'ziultimate'),			
				'options' 	=> [
					[
						'id'	=> 'acfrep',
						'name' 	=> __('ACF repeater', "ziultimate"),
					],
					[
						'id'	=> 'acfopt',
						'name' 	=> __('ACF options page with repeater', "ziultimate"),
					],
					[
						'id' 	=> 'mbgroup',
						'name' 	=> __('MetaBox cloneable group', "ziultimate")
					],
					[
						'id' 	=> 'mbsettings',
						'name' 	=> __('MetaBox settings page with group', "ziultimate")
					],
					[
						'id' 	=> 'postsbyterms',
						'name' 	=> __('Display Posts from selected taxonomy', "ziultimate")
					],
					[
						'id' 	=> 'postsbyids',
						'name' 	=> __('Show content via post IDs', "ziultimate")
					],
					[
						'id' 	=> 'wootabs',
						'name' 	=> __('WooCommerce Tabs', "ziultimate")
					]
				],
				'default' 	=> 'acfrep'
			]
		);

		$dyndata->add_option(
			'post_id',
			[
				'type' 			=> 'text',
				'title' 		=> __('Post ID', 'ziultimate'),
				'description' 	=> esc_html__('Leave empty if you are using it in the query builder or single post / page.', 'ziultimate'),
				'dynamic' 		=> [
					'enabled' 	=> true
				],
				'dependency' 	=> [
					[
						'option' 	=> 'source',
						'value' 	=> ['postsbyterms', 'postsbyids', 'wootabs'],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		include_once 'options-postsbyterms.php';

		include_once 'options-acfmb.php';

		include_once 'options-tabsbypostids.php';

		/**
		 * Animation Settings
		 */
		$anim = $options->add_group(
			'anim',
			[
				'type' 		=> 'panel_accordion', 
				'title' 	=> esc_html__('Animation', 'ziultimate'),
				'collapsed' => true
			]
		);

		$anim->add_option(
			'anim_type',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Transition Effect', 'ziultimate'),
				'default' 	=> 'default',
				'options' 	=> [
					[
						'name' 		=> esc_html__('None'),
						'id' 		=> 'default'
					],
					[
						'name' 		=> esc_html__('Fade'),
						'id' 		=> 'fade'
					],
					[
						'name' 		=> esc_html__('Slide left', 'ziultimate'),
						'id' 		=> 'slide-rl'
					],
					[
						'name' 		=> esc_html__('Slide Up', 'ziultimate'),
						'id' 		=> 'slide-top'
					],
					[
						'name' 		=> esc_html__('Slide Down', 'ziultimate'),
						'id' 		=> 'slide-bottom'
					]
				]
			]
		);

		$anim->add_option(
			'fade_td',
			[
				'type' 				=> 'slider',
				'content' 			=> 's',
				'min' 				=> 0,
				'max' 				=> 10,
				'step' 				=> 0.1,
				'default' 			=> 0.35,
				'title' 			=> esc_html__('Transition Duration', 'ziultimate'),
				'css_style' 		=> [
					[
						'selector' 		=> "{{ELEMENT}}",
						'value' 		=> '--zutabs-opacity-td: {{VALUE}}s'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'anim_type',
						'value' 	=> [ 'fade' ]
					]
				]
			]
		);

		$anim->add_option(
			'anim_td',
			[
				'type' 				=> 'slider',
				'content' 			=> 's',
				'min' 				=> 0,
				'max' 				=> 10,
				'step' 				=> 0.1,
				'default' 			=> 0.5,
				'title' 			=> esc_html__('Transition Duration', 'ziultimate'),
				'css_style' 		=> [
					[
						'selector' 		=> "{{ELEMENT}}",
						'value' 		=> '--zutabs-transform-td: {{VALUE}}s'
					]
				],
				'dependency' 	=> [
					[
						'option' 	=> 'anim_type',
						'value' 	=> [ 'fade', 'default' ],
						'type' 		=> 'not_in'
					]
				]
			]
		);

		/**
		 * Accordion Settings
		 */
		$acrd = $options->add_group(
			'acrd',
			[
				'type' 		=> 'panel_accordion', 
				'title' 	=> esc_html__('Accordion Config', 'ziultimate'),
				'collapsed' => true
			]
		);

		$acrd->add_option(
			'info',
			[
				'type' 		=> 'html', 
				'title' 	=> esc_html__('Notes:', 'ziultimate'),
				'content' 	=> '<p>' . esc_html__( 'Tabs are converting to accordion on small devices(width < 769px). You can see the preview when you will select the tablet or any other breakpoints.', 'ziultimate') . '</p>'
			]
		);

		/*$acrd->add_option(
			'collapse_breakpoint',
			[
				'type'        => 'slider',
				'title'       => esc_html__( 'Enable accordion effect at', 'ziultimate' ),
				'description' => esc_html__( 'Tabs layout will be displayed before the specified value (in pixels).', 'zionbuilder' ),
				'min'         => 0,
				'max'         => 2560,
				'default'     => 768,
			]
		);*/

		$acrd->add_option(
			'active_by_default',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'First tab active by default?', 'zionbuilder' ),	
				'options' 	=> [
					[
						'id'	=> 'yes',
						'name' 	=> __('Yes', "zionbuilder"),
					],
					[
						'id'	=> 'no',
						'name' 	=> __('No', "zionbuilder"),
					]
				],
				'default' 	=> 'yes'
			]
		);

		$acrd->add_option(
			'td_acrd',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 10000,
				'step' 		=> 50,
				'default' 	=> 500,
				'title' 	=> esc_html__('Transition Duration for Toggle')
			]
		);

		$acrd->add_option(
			'arrow_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'icon',
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'plus',
					'unicode' => 'uf067',
				]
			]
		);

		$acrd->add_option(
			'arrow_icon_anim',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Animation', 'ziultimate'),
				'options' 	=> [
					[
						'name' 	=> esc_html__('Rotate'),
						'id' 	=> 'rotate'
					],

					[
						'name' 	=> esc_html__('Vertical Flip', 'ziultimate'),
						'id' 	=> 'flip'
					]
				],
				'default' 	=> 'rotate'
			]
		);

		$acrd->add_option(
			'anim_rotate',
			[
				'type' 		=> 'slider',
				'content' 	=> 'deg',
				'min' 		=> -180,
				'max' 		=> 180,
				'step' 		=> 5,
				'default' 	=> 45,
				'title' 	=> esc_html__('Rotate(Active State)', 'zionbuilder'),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zu-tab-active.rotate .zutab-acrd-icon",
						'value' 	=> 'transform: rotate({{VALUE}}deg)'
					]
				],
				'dependency' => [
					[
						'option' 	=> 'arrow_icon_anim',
						'value' 	=> [ 'rotate' ]
					]
				]
			]
		);

		$acrd->add_option(
			'anim_duration',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'min' 		=> 0,
				'max' 		=> 10,
				'step' 		=> 0.1,
				'default' 	=> 0.3,
				'title' 	=> esc_html__('Transition Duration', 'zionbuilder'),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .zutab-acrd-icon",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);
	}

	protected function can_render() {
		if( ! License::has_valid_license() )
			return false;

		return true;
	}

	/**
	 * Loading the CSS
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url('dist/css/elements/DynamicTabs/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 */
	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/js/elements/DynamicTabs/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/js/elements/DynamicTabs/frontend.js' ) );
	}

	/**
	 * Registering the styles
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'tabs_wrapper_styles',
			[
				'title'    => esc_html__( 'Tabs Outer Wrapper Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-tabs-labels',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'tab_title_styles',
			[
				'title'    => esc_html__( 'Tabs Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-tabs-label',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'actv_tab_title_styles',
			[
				'title'    => esc_html__( 'Active Tab Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-tab-active.zu-tabs-label',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'content_wrapper_styles',
			[
				'title'    => esc_html__( 'Content Wrapper Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-tabs-panels',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'content_styles',
			[
				'title'    => esc_html__( 'Content Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-tabs-panel-content',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'actv_content_styles',
			[
				'title'    => esc_html__( 'Active Content Styles', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-tabs-panel-content.zu-tab-active',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'icon_styles',
			[
				'title'    => esc_html__( 'Icon Style', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zutab-acrd-icon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'actv_icon_styles',
			[
				'title'    => esc_html__( 'Active Icon Style', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .zu-tab-active .zutab-acrd-icon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}

	/**
	 * Render the tabs
	 */
	public function render( $options ) {
		global $wp_embed;

		$source 	= $options->get_value('source', 'acfrep');
		$layout 	= $options->get_value('layout', 'horizontal');
		$template 	= $options->get_value('template', 'no-borders');
		$anim_type 	= $options->get_value('anim_type', 'default');
		$post_id 	= $options->get_value('post_id', get_the_ID());
		$td_acrd	= $options->get_value('td_acrd', 500);
		$tabindex	= $options->get_value('tabindex', 10);
		$uid 		= $this->uid;

		$icon = $options->get_value( 'arrow_icon' );
		$anim = $options->get_value( 'arrow_icon_anim', 'rotate' );
		$combined_icon_attr = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'zutab-acrd-icon' ] );

		$zu_tabs = array(
			[
				'title' 	=> esc_html__('Tab 1'),
				'content' 	=> esc_html__('Sample tab content')
			],
			[
				'title' 	=> esc_html__('Tab 2'),
				'content' 	=> esc_html__('Sample tab content')
			],
			[
				'title' 	=> esc_html__('Tab 3'),
				'content' 	=> esc_html__('Sample tab content')
			]
		);

		$hasTabs = false;

		//ACF Repeater
		if( function_exists('have_rows') && ( $source == 'acfrep' || $source == 'acfopt' ) ) {
			$repfield = $options->get_value('acfrep_repfld');

			if( $source == 'acfopt' ) {
				$post_id = 'option';
			}

			if( ! empty( $repfield ) && have_rows( $repfield, $post_id ) ) {
				$zu_tabs 	= array();
				$tab_title 	= $options->get_value('acfrep_title');
				//$tab_subttl = $options->get_value('acfrep_subtitle');
				$tab_cnt 	= $options->get_value('acfrep_content');
				$hasTabs 	= true;

				$i = 0;
				while( have_rows( $repfield, $post_id ) ) { 

					the_row();

					if( isset( $tab_title) ) {
						$zu_tabs[$i]['title'] = wp_kses_post( get_sub_field( $tab_title ) );
					}

					/*if( isset( $tab_subttl) ) {
						$zu_tabs[$i]['subtitle'] = wp_kses_post( get_sub_field( $tab_subttl ) );
					}*/

					if( isset( $tab_cnt ) ) {
						$zu_tabs[$i]['content'] = wp_kses_post( get_sub_field( $tab_cnt ) );
					}

					$i++;
				}
			}
		} elseif( function_exists('rwmb_meta') && ( $source == 'mbgroup' || $source == 'mbsettings' ) ) { //Meta Box Group
			$mbgroup_id = $options->get_value('mbgroup_id', false);

			if ( ! empty( $mbgroup_id ) ) {

				$zu_tabs 		= array();
				$i 				= 0;
				$field_title 	= $options->get_value('mbgroup_title');
				$field_cnt 		= $options->get_value('mbgroup_content');
				$autop 			= $options->get_value('enable_autop', false);

				if( $source == 'mbsettings' ) {
					$args 		= ['object_type' => 'setting'];
					$post_id 	= $options->get_value('mbsettings_pg', false);
				} else {
					$args = '';
				}

				$group_values = rwmb_meta( $mbgroup_id, $args, $post_id );
				if( $group_values ) {
					$hasTabs = true;
					foreach( $group_values as $group_value ) {
						if( ! empty( $field_title ) && ! empty( $group_value[ $field_title ] ) ) {
							$zu_tabs[$i]['title'] = wp_kses_post( $group_value[ $field_title ] );
						}

						if( ! empty( $field_cnt ) && ! empty( $group_value[ $field_cnt ] ) ) {
							$mb_content = ! empty( $autop ) ? wpautop( $group_value[ $field_cnt ] ) : $group_value[ $field_cnt ];
							$zu_tabs[$i]['content'] = wp_kses_post( $mb_content );
						}

						$i++;
					}
				}
			}
		} elseif( $source == 'postsbyterms' ) { //* Show content via terms
			$this->taxonomy = $options->get_value('tax_name', 'category');
			$template_id = $options->get_value( 'saved_template_id', false );
			$args = array(
				'number' 	=> 3,
				'echo' 		=> 0,
				'taxonomy' 	=> $this->taxonomy
			);

			$include_ids = $options->get_value('include_ids', false );
			if( ! empty( $include_ids ) ) {
				$args['include'] = array_filter( array_map( 'trim', explode( ',', $include_ids ) ) );
			}

			$exclude_ids = $options->get_value('exclude_ids', false );
			if( ! empty( $exclude_ids ) ) {
				$args['exclude'] = array_filter( array_map( 'trim', explode( ',', $exclude_ids ) ) );
			}

			$child_of = $options->get_value('child_of', false );
			if( ! empty( $child_of ) ) {
				$args['child_of'] = absint( $child_of );
			}

			$limit = $options->get_value('limit', 3 );
			if( ! empty( $limit ) ) {
				$args['number'] = absint( $limit );
			}

			$args['hide_empty'] = $options->get_value('hide_empty', true);
			$args['orderby'] 	= $options->get_value('orderby', "name");
			$args['order'] 		= $options->get_value('order', "ASC");

			$terms = get_terms( $args );
			if ( $terms && ! empty( $template_id ) ) {
				$zu_tabs = array();
				$term_ids = array();
				$hasTabs = true;
				foreach( $terms as $key => $term ) {
					$zu_tabs[$key]['title'] 	= wp_kses_post( $term->name );
					$zu_tabs[$key]['content'] 	= sprintf( '[zionbuilder id="%s"]', $template_id );
					$term_ids[$key] 			= $term->term_id;
				}
			}
		} elseif( $source == 'postsbyids' ) { //* Show content via specific post IDs
			$postids = $options->get_value( 'post_ids' , false );
			if( ! empty( $postids ) ) {
				$title_source = $options->get_value( 'tab_title_source', 'post_title' );
				$cnt_source = $options->get_value( 'tab_content_source', 'post_excerpt' );
				$template_id = $options->get_value( 'zion_template_id', false );
				$postids = explode(",", $postids);
				$zu_tabs = array();
				$post_ids = array();
				$hasTabs = true;

				foreach( $postids as $key => $id ) {
					if( $title_source == 'post_title' ) {
						$zu_tabs[$key]['title'] = get_the_title( $id );
					} else {
						$title_metakey = $options->get_value( 'title_metakey' );
						$zu_tabs[$key]['title'] = get_post_meta( $id, $title_metakey, true ) ? get_post_meta( $id, $title_metakey, true ) : sprintf( 'Tab %s', ($key + 1) );
					}

					if( $cnt_source == 'post_excerpt' ) {
						$zu_tabs[$key]['content'] = wp_kses_post( get_the_excerpt( $id ) );
					} elseif( $cnt_source == 'post_content' ) {
						$zu_tabs[$key]['content'] = wp_kses_post( wpautop( get_the_content( '', false, $id ) ) );
					} elseif( $cnt_source == 'zion_template' ) {
						$zu_tabs[$key]['content'] = sprintf( '[zionbuilder id="%s"]', $template_id );
					} else {
						$cnt_metakey = $options->get_value( 'cnt_metakey' );
						$tab_content = get_post_meta( $id, $cnt_metakey, true );
						$zu_tabs[$key]['content'] = $tab_content ? wp_kses_post( $tab_content ) : esc_html__('Demo tab content', 'ziultimate');
					}

					$post_ids[$key] 			= (int) $id;
				}
			}
		} elseif( $source == 'wootabs' ) { //* WooCommerce Tabs
			global $product;

			$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

			if ( ! empty( $product_tabs ) ) : 
				$zu_tabs = array();
				$hasTabs = true;
				$tabloop = 0;

				add_filter( 'woocommerce_product_description_heading', '__return_null' );
				add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

				foreach ( $product_tabs as $key => $tab ) :
					$zu_tabs[$tabloop]['title'] = apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key );
					ob_start();
					call_user_func( $tab['callback'], $key, $tab );
					$zu_tabs[$tabloop]['content'] = ob_get_clean();
					$tabloop++;
				endforeach;

				remove_filter( 'woocommerce_product_description_heading', '__return_null' );
				remove_filter( 'woocommerce_product_additional_information_heading', '__return_null' );
				$tabloop = 0;
			endif;
		}

		if( ! self::isBuilderEditor() && !$hasTabs )
			return;

		$active_by_default = $options->get_value('active_by_default', 'yes');
	?>
	<article class="zu-tabs zu-tabs-<?php echo $layout; ?> tpl-<?php echo $template; ?> zu-tabs-clearfix" data-acrd-toggle-speed="<?php echo $td_acrd; ?>" data-first-tab-active="<?php echo $active_by_default; ?>">
		<nav class="zu-tabs-labels zu-tabs-clearfix" role="tablist">
			<?php foreach ($zu_tabs as $key => $tab_title) { if ( ! isset( $tab_title['title'] ) ) { continue; } ?>
				<div class="zu-tabs-label<?php if ( 0 == $key ) { echo ' zu-tab-active';} ?>" id="<?php echo $uid;?>-label-<?php echo $key;?>" data-index="<?php echo $key;?>" aria-selected="<?php echo ($key > 0) ? 'false' : 'true';?>" aria-expanded="<?php echo ($key > 0) ? 'false' : 'true';?>" aria-controls="<?php echo $uid;?>-panel-<?php echo $key;?>" tabindex="<?php echo $tabindex; ?>" role="tab"><?php echo $tab_title['title']; ?></div>
			<?php $tabindex++; } ?>
		</nav>
		<section class="zu-tabs-panels zu-tabs-clearfix">
			<?php 
				foreach ($zu_tabs as $key => $tab_content) { 
					if ( ! isset( $tab_content['content'] ) ) { continue; } 
					if( $source == 'postsbyterms') {
						$this->cat_id = $term_ids[$key];
						add_action( 'pre_get_posts', [ $this, 'zutabs_filter_query' ]);
					} 

					if( $source == 'postsbyids' ) {
						$this->post_ID = $post_ids[$key];
						add_action( 'pre_get_posts', [ $this, 'zutabs_filter_rep_query' ]);
					}
			?>
				<div class="zu-tabs-panel">
					<div class="zu-tabs-label zu-tabs-panel-label <?php echo $anim; ?><?php if ( 0 == $key && $active_by_default == 'yes' ) { echo ' zu-tab-active';} ?>" data-index="<?php echo $key; ?>" tabindex="<?php echo $tabindex; ?>">
						<span class="acrd-btn-title"><?php echo $tab_content['title']; ?></span>
						<?php
							if( ! empty( $icon  ) ) {
								$this->attach_icon_attributes( 'icon', $icon );
								$this->render_tag(
									'span',
									'icon',
									'',
									$combined_icon_attr
								);
							}
						?>
					</div>
					<div class="zu-tabs-panel-content zutab-<?php echo $anim_type; ?> zu-tabs-clearfix<?php if( 0 == $key ) { echo ' zu-tab-active in';} ?>" id="<?php echo $uid;?>-panel-<?php echo $key;?>" data-index="<?php echo $key;?>"<?php if ( $key > 0 ) { echo ' aria-hidden="true"';} ?> aria-labelledby="<?php echo $uid;?>-label-<?php echo $key;?>" role="tabpanel" aria-live="polite">
						<?php echo do_shortcode( $wp_embed->autoembed( $tab_content['content'] ) ); ?>
					</div>
				</div>
			<?php 
					$tabindex++; 

					if( $source == 'postsbyterms') { remove_action( 'pre_get_posts', [ $this, 'zutabs_filter_query' ]); }

					if( $source == 'postsbyids' ) { remove_action( 'pre_get_posts', [ $this, 'zutabs_filter_rep_query' ]); }
				}  

				// for editor only
				if( ( $source == 'postsbyterms' || $source == 'postsbyids' ) && self::isBuilderEditor() ) {
					do_action( 'wp_enqueue_scripts' );
					do_action('wp_footer');
				}
			?>
		</section>
	</article>
	<?php
	}

	public function zutabs_filter_query( $query ) {
		if ( ! is_admin() ) {
			$tax_query = array(
				array(
					'taxonomy' 	=> $this->taxonomy,
					'field' 	=> 'term_id',
					'terms' 	=> array($this->cat_id),
					'operator' 	=> 'IN'
				)
			);

			$query->set('tax_query', $tax_query);
		} else {
			return $query;
		}
	}

	public function zutabs_filter_rep_query( $query ) {
		if ( ! is_admin() ) {
			$query->set('post__in', [ $this->post_ID ]);
			$query->set('nopaging', true);
		} else {
			return $query;
		}
	}

	public function server_render( $request ) {

		if ( function_exists( 'WC' ) && $this->options->get_value( 'source', 'acfrep' ) == 'wootabs') {
			\WC()->frontend_includes();
			\WC_Template_Loader::init();
			\wc_load_cart();
		}

		parent::server_render( $request );
	}
}