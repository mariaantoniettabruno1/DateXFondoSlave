<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;
use ZionBuilderPro\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class WpImage
 *
 * @package ZiUltimate\DynamicContent\Fields;
 */
class WpImage extends BaseField {
	public function get_category() {
		return [ self::CATEGORY_TEXT, self::CATEGORY_LINK, self::CATEGORY_IMAGE ];
	}

	public function get_group() {
		return 'others';
	}

	public function get_id() {
		return 'wp-image';
	}

	public function get_name() {
		return esc_html__( 'WP image', 'ziultimate' );
	}

	/**
	 * Get Content
	 *
	 * Render the selected post's custom field
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$post   = $GLOBALS['post'];
		$field  = isset( $options['name'] ) ? strtolower( $options['name'] ) : 'file_url';
		$output = '';

		if ( ! empty( $field ) && $post ) {
			switch ( $field ) {
				case 'file_url': 
					if( isset( $options['size'] ) ) {
						$image = wp_get_attachment_image_src( $post->ID, $options['size'] );
						if( ! empty( $image ) ) {
							$output = $image[0];
						} elseif ( has_post_thumbnail( $post ) ) {
							$imageID = get_post_thumbnail_id( $post );
							if ( ! empty( $imageID ) ) {
								$thePost = get_post( $imageID );
								$image = wp_get_attachment_image_src( $thePost->ID, $options['size'] );
								$output = $image[0];
							}
						} else {
							$output = '';
						}
					} else {
						$output = $post->guid;
					}
					break;
				
				case 'attachment_page':
					$output = get_permalink( $post->ID );
					break;
				
				case 'title': 
					$output = $post->post_title;
					break;
				
				case 'alt': 
					$output = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
					break;
				
				case 'caption': 
					$output = $post->post_excerpt;
					break;
				
				case 'description': 
					$output = $post->post_content;
					break;
				
				default: 
					$output = $post->ID;
					break;
			}
		}

		echo wp_kses_post( $output );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		$options = [
			[
				'id'   => 'file_url',
				'name' => esc_html__( 'File URL', 'zionbuilder-pro' ),
			],
			[
				'id'   => 'attachment_page',
				'name' => esc_html__( 'Attachment page URL', 'zionbuilder-pro' ),
			],
			[
				'id'   => 'title',
				'name' => esc_html__( 'Title', 'zionbuilder-pro' ),
			],
			[
				'id'   => 'alt',
				'name' => esc_html__( 'Alternative title', 'zionbuilder-pro' ),
			],
			[
				'id'   => 'caption',
				'name' => esc_html__( 'Caption', 'zionbuilder-pro' ),
			],
			[
				'id'   => 'description',
				'name' => esc_html__( 'Description', 'zionbuilder-pro' ),
			],
		];
		return [
			'name' => [
				'type'        => 'select',
				'title'       => esc_html__( 'Info to display', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Select the desired info you want to display', 'zionbuilder-pro' ),
				'default'     => ( empty( $options ) ? '' : $options[0]['id'] ),
				'dynamic'     => false,
				'options'     => $options,
			],

			'size' => [
				'type'        => 'select',
				'title'       => esc_html__( 'Size', 'zionbuilder-pro' ),
				'default'     => 'thumbnail',
				'dynamic'     => false,
				'options'     => ( new \ZiUltimate\UltimateElements() )->get_thumbnail_sizes(),
				'dependency' => [
					[
						'option' 	=> 'name',
						'value' 	=> [ 'file_url' ]
					]
				]
			],
		];
	}
}
