<?php
namespace ZiUltimate\WooElements\TabsInAccordion;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class TabsInAccordion
 *
 * @package ZiUltimate\WooElements
 */
class TabsInAccordion extends UltimateElements {
    public function get_type() {
		return 'zu_tabs_in_acrd';
	}

	public function get_name() {
		return __( 'Tabs In Accordion', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'tabs', 'accordion', 'product tabs' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function options( $options ) {

		$options->add_option(
			'product_id',
			[
				'type' 		=> 'text',
				'title' 	=> esc_html__('Product ID', 'ziultimate'),
				'description' => esc_html__('Keep it empty if you are using on single product page or query builder.', 'zilultimate'),
				'dynamic' 	=> [
					'enabled' => true
				]
			]
		);

		$options->add_option(
			'acrd_expand',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Expanding First Item on Page Load?', "ziultimate"),
				'default' 	=> true,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'remove_desc_tab',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Remove Description Tab?', "ziultimate"),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'remove_info_tab',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Remove Additional Information Tab?', "ziultimate"),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'remove_reviews_tab',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> __('Remove Reviews Tab?', "ziultimate"),
				'default' 	=> false,
				'layout' 	=> 'inline'
			]
		);

		$options->add_option(
			'toggle_speed',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 10000,
				'step' 		=> 10,
				'default' 	=> 650,
				'title' 	=> esc_html__('Transition Duration', 'zionbuilder')
			]
		);


		/**
		 * ACF Repeater Content Group
		 */
		$acf = $options->add_group(
			'acf_acrd',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__( 'ACF Repeater Content', 'ziultimate' ),
				'collapsed' => true
			]
		);

		$acf->add_option(
			'note',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__( 'Note', 'ziultimate' ),
				'content' 	=> '<p>You can use the ACF repeater and create the custom accordion items. This custom items will append below the existing items.</p><hr style="border: 1px solid #d5d5d5;"/>'
			]
		);

		$acf->add_option(
			"tab_rep",
			array(
				'type' 		=> 'text',
				"title" 	=> esc_html__("Repeater Field Name", "ziultimate")
			)
		);

		$acf->add_option(
			"tab_title",
			array(
				'type' 		=> 'text',
				"title" 		=> esc_html__("Title Field Name", "ziultimate")
			)
		);

		$acf->add_option(
			"tab_cnt",
			array(
				'type' 		=> 'text',
				"title" 		=> esc_html__("Content Field Name", "ziultimate")
			)
		);



		/**
		 * Arrow Group
		 */
		$arrow = $options->add_group(
			'arrow_icon',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__( 'Arrow Icon', 'ziultimate' ),
				'collapsed' => true
			]
		);

		$arrow->add_option(
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

		$arrow->add_option(
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

		$arrow->add_option(
			'anim_rotate',
			[
				'type' 		=> 'slider',
				'content' 	=> 'deg',
				'min' 		=> -180,
				'max' 		=> 180,
				'step' 		=> 5,
				'default' 	=> 45,
				'title' 	=> esc_html__('Rotate - Active State', 'zionbuilder'),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .wc-prd-accordion-item-active .rotate .wc-prd-accordion-icon",
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

		$arrow->add_option(
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
						'selector' 	=> "{{ELEMENT}} .wc-prd-accordion-icon",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);
	}

	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url('dist/css/elements/TabsInAccordions/frontend.css' ) );
	}

	public function enqueue_scripts() {
		$this->enqueue_editor_script( Utils::get_file_url('dist/js/elements/TabsInAccordions/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url('dist/js/elements/TabsInAccordions/frontend.js' ) );
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'acrd_title_styles',
			[
				'title'    => esc_html__( 'Title', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .wc-prd-accordion-button',
			]
		);

		$this->register_style_options_element(
			'acrd_actv_title_styles',
			[
				'title'    => esc_html__( 'Title for Active State', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .wc-prd-accordion-item-active .wc-prd-accordion-button',
			]
		);

		$this->register_style_options_element(
			'acrd_content_styles',
			[
				'title'    => esc_html__( 'Content Style', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .wc-prd-accordion-content',
			]
		);

		$this->register_style_options_element(
			'acrd_actv_content_styles',
			[
				'title'    => esc_html__( 'Content Style for Active State', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .wc-prd-accordion-item-active .wc-prd-accordion-content',
			]
		);

		$this->register_style_options_element(
			'icon_styles',
			[
				'title'    => esc_html__( 'Icon Style', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .wc-prd-accordion-pm',
			]
		);

		$this->register_style_options_element(
			'icon_styles_actv',
			[
				'title'    => esc_html__( 'Icon Style for Active State', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .wc-prd-accordion-item-active .wc-prd-accordion-pm',
			]
		);
	}

	public function render( $options ) {
		global $product;

		$product_id = $options->get_value( 'product_id', get_the_ID() );
		$product = \WC()->product_factory->get_product( $product_id );

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );
		$post_id = $product->get_id();

		if ( ! empty( $product_tabs ) ) :

			if( ! has_filter( 'comments_template', array( 'WC_Template_Loader', 'comments_template_loader' ) ) ) {
				add_filter( 'comments_template', array( 'WC_Template_Loader', 'comments_template_loader' ) );
			}

			$is_expand_first_item = $options->get_value( 'acrd_expand', true );
			$data = [
				'toggle_speed' 	=> absint( $options->get_value('toggle_speed', 650 ) ),
				'acrd_expand'	=> ! empty( $is_expand_first_item ) ? true : false
			];

			add_filter( 'woocommerce_product_description_heading', '__return_null' );
			add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

			$remove_desc_tab = $options->get_value('remove_desc_tab', false);
			$remove_info_tab = $options->get_value('remove_info_tab', false);
			$remove_reviews_tab = $options->get_value('remove_reviews_tab', false);

			if( ! empty( $remove_desc_tab ) ) {
				unset( $product_tabs['description'] );
			}

			if( ! empty( $remove_info_tab ) ) {
				unset( $product_tabs['additional_information'] );
			}

			if( ! empty( $remove_reviews_tab ) ) {
				unset( $product_tabs['reviews'] );
			}

			$icon = $options->get_value( 'arrow_icon' );
			$anim = $options->get_value( 'arrow_icon_anim', 'rotate' );
			$combined_icon_attr = $this->render_attributes->get_combined_attributes( 'icon_styles', [ 'class' => 'wc-prd-accordion-icon' ] );
		?>
			<div class="woocommerce-tabs wc-tabs-wrapper">
				<div class="wc-prd-accordion" role="tablist" data-acrd-config=<?php echo wp_json_encode( $data ); ?>>
					<?php
						foreach ( $product_tabs as $key => $tab ) :
							echo '<div class="wc-prd-accordion-item">';
								echo '<div class="wc-prd-accordion-button" aria-selected="false" aria-expanded="false" role="tab">';
									echo '<span class="wc-prd-accordion-label">' . apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) . '</span>';

									if( ! empty( $icon  ) ) {
										echo '<span class="wc-prd-accordion-pm ' . $anim . '">';
										$this->attach_icon_attributes( 'icon', $icon );
										$this->render_tag(
											'span',
											'icon',
											'',
											$combined_icon_attr
										);
										echo '</span>';
									}

								echo '</div>';

								if ( isset( $tab['callback'] ) ) {
									echo '<div class="wc-prd-accordion-content woocommerce-Tabs-panel--' . esc_attr( $key ) . ' clearfix" aria-selected="false" aria-hidden="true" role="tabpanel" aria-labelledby="tab-title-' . esc_attr( $key ) . '">';
									call_user_func( $tab['callback'], $key, $tab );
									echo '</div>';
								}
							echo '</div>';
						endforeach;

						$tabs_acf = $options->get_value('tab_rep', false);
						if( function_exists( 'have_rows' ) && ! empty( $tabs_acf ) && have_rows( $tabs_acf, $post_id ) ) :
							global $wp_embed;

							$tab_field_title = $options->get_value('tab_title', false);
							$tab_field_cnt = $options->get_value('tab_cnt', false);

							$i = 0;
							while( have_rows( $tabs_acf, $post_id ) ) : the_row();
								$tab_title = $tab_content = '';

								if( ! empty( $tab_field_title ) ) {
									$slug = $tabs_acf . '_' . $i . '_' . $tab_field_title;
									$tab_title = get_post_meta( $post_id, $slug, true );
								}

								if( ! empty( $tab_field_cnt ) ) {
									$slug = $tabs_acf . '_' . $i . '_' . $tab_field_cnt;
									$tab_content = get_post_meta( $post_id, $slug, true );

									echo '<div class="wc-prd-accordion-item">';
										echo '<div class="wc-prd-accordion-button" aria-selected="false" aria-expanded="false" role="tab">';
											echo '<span class="wc-prd-accordion-label">' . wp_kses_post( $tab_title ) . '</span>';
											if( ! empty( $icon  ) ) {
												echo '<span class="wc-prd-accordion-pm ' . $anim . '">';
												$this->attach_icon_attributes( 'icon', $icon );
												$this->render_tag(
													'span',
													'icon',
													'',
													$combined_icon_attr
												);
												echo '</span>';
											}
										echo '</div>';

										echo '<div class="wc-prd-accordion-content woocommerce-Tabs-panel--' . esc_attr( $slug ) . ' clearfix" aria-selected="false" aria-hidden="true" role="tabpanel" aria-labelledby="tab-title-' . esc_attr( $slug ) . '">';
											
											echo do_shortcode( wpautop( $wp_embed->autoembed( $tab_content ) ) );

										echo '</div>';

									echo '</div>';
								}

								$i++;
							endwhile;
						endif;
					?>
				</div>

				<?php do_action( 'woocommerce_product_after_tabs' ); ?>
			</div>
		<?php

			remove_filter( 'woocommerce_product_description_heading', '__return_null' );
			remove_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

		endif;
	}

	public function server_render( $request ) {

		if ( function_exists( 'WC' ) ) {
			\WC()->frontend_includes();
			\WC_Template_Loader::init();
		}

		parent::server_render( $request );
	}
}