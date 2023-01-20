<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class GalleryBuilder extends RepeaterProvider {

	public static function get_id() {
		return 'zu_gallery_builder';
	}

	public static function get_name() {
		return esc_html__( 'Gallery Builder - ACF/MB', 'ziultimate' );
	}

	public function the_item( $index = null ) {
		$real_index = null === $index ? $this->get_real_index() : $index;
		$current_item = $this->get_item_by_index( $real_index );

		global $post;

		if ( $current_item ) {
			$post = get_post( $current_item );
			setup_postdata( $post );
		}
	}

	public function reset_item() {
		wp_reset_postdata();
	}

	public function perform_query() {
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];
		$post_id = ( isset( $this->config['is_options_page'] ) && $this->config['is_options_page'] !== null ) ? 'option' : false;
		$query_args = [];
		$images = false;

		$field_type = isset( $config['field_type'] ) ? $config['field_type'] : 'acf';
		$gallery_field = isset( $config['gallery_field'] ) ? $config['gallery_field'] : '';

		if( $field_type == 'acf' && ! empty( $gallery_field ) ) {
			$images = get_field( $gallery_field, $post_id, false );

			if( $images ) {
				$this->query = [
					'query' => [],
					'items' => is_array( $images ) ? $images : []
				];

				return;
			}
		}


		$this->query = [
			'query' => null,
			'items' => []
		];
	}

	public function get_schema() {
		$options_schema = new Options( 'ziultimate/repeater_provider/zu_gallery_builder' );

		$options_schema->add_option(
			'field_type',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__('Field type', 'ziultimate' ),
				'placeholder' 	=> esc_html__('select field type', 'ziultimate'),
				'options' 		=> [
					[
						'id' 	=> 'acf',
						'name' 	=> esc_html__('ACF gallery field', 'ziultimate')
					],
					[
						'id' 	=> 'mb',
						'name' 	=> esc_html__('Metabox clonable field', 'ziultimate')
					]
				],
				'default' 		=> 'acf'
			]
		);

		$options_schema->add_option(
			'gallery_field',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Field name', 'ziultimate' ),
				'placeholder' 	=> esc_html__('enter key', 'ziultimate')
			]
		);

		$options_schema->add_option(
			'is_options_page',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__('Options page?', 'ziultimate' ),
				'description' 	=> esc_html__('Are you fetching the images from options/settings page?', 'ziultimate'),
				'default' 		=> false,
				'layout' 		=> 'inline'
			]
		);

		return $options_schema->get_schema();
	}
}