<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class FeaturedImage
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class FeaturedImage extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	public function get_group() {
		return 'post';
	}

	/**
	 * Will load the field only if the global post is set
	 *
	 * @return boolean
	 */
	public function can_load() {
		return isset( $GLOBALS['post'] );
	}

	public function get_id() {
		return 'featured-image';
	}

	public function get_name() {
		return esc_html__( 'Featured image', 'zionbuilder-pro' );
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
		$field  = isset( $options['name'] ) ? strtolower( $options['name'] ) : '';
		$output = '';

		if ( ! empty( $field ) && $post ) {
			if ( has_post_thumbnail( $post ) ) {
				$imageID = get_post_thumbnail_id( $post );
				if ( ! empty( $imageID ) ) {
					$thePost = get_post( $imageID );

					switch ( $field ) {
						case 'file_url': {
							$output = $thePost->guid;
							break;
						}
						case 'attachment_page': {
							$output = get_permalink( $thePost->ID );
							break;
						}
						case 'title': {
							$output = $thePost->post_title;
							break;
						}
						case 'alt': {
							$output = get_post_meta( $imageID, '_wp_attachment_image_alt', true );
							break;
						}
						case 'caption' : {
							$output = $thePost->post_excerpt;
							break;
						}
						case 'description' : {
							$output = $thePost->post_content;
							break;
						}
						default: {
							$output = $imageID;
						}
					}
				}
			}
		}
		echo wp_kses_post( $output );
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		// Display the default value
		$post   = $GLOBALS['post'];
		$output = '';
		if ( has_post_thumbnail( $post ) ) {
			$imageID = get_post_thumbnail_id( $post );
			if ( ! empty( $imageID ) ) {
				$thePost = get_post( $imageID );
				$output  = $thePost->guid;
			}
		}
		return $output;
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
		];
	}
}
