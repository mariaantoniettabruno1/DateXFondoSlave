<?php
namespace ZiUltimate\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class TermImage
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class TermImage extends BaseField {
	public function get_category() {
		return [self::CATEGORY_IMAGE, self::CATEGORY_TEXT];
	}

	public function get_group() {
		return 'taxonomy';
	}

	public function get_id() {
		return 'term-image';
	}

	public function get_name() {
		return esc_html__( 'Term Image URL', 'ziultimate' );
	}

	/**
	 * Will load the field
	 *
	 * @return boolean
	 */
	public function can_load() {
		return true;
	}

	/**
	 * Making thumbnail size list 
	 */ 
	private function get_thumbnail_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = $img_sizes =array();

		foreach( get_intermediate_image_sizes() as $s ) {
			$sizes[ $s ] = array( 0, 0 );
			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $s ][0] = get_option( $s . '_size_w' );
				$sizes[ $s ][1] = get_option( $s . '_size_h' );
			} else {
				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
			}
		}

		$i = 0;
		foreach( $sizes as $size => $atts ) {
			$size_title = ucwords(str_replace("-"," ", $size));
			$img_sizes[$i]['id'] = $size;
			$img_sizes[$i]['name'] =  $size_title . ' (' . implode( 'x', $atts ) . ')';
			$i++;
		}

		$img_sizes[$i]['id'] = 'full';
		$img_sizes[$i]['name'] =  esc_html__('Full');


		return $img_sizes;
	}

	private function get_thumbnail( $options = []) {
		$thumbnail 	= '';
		$term 		= get_queried_object();

		if ( ! $term || empty( $term->term_id ) )
			return;

		$image_source = ( empty( $options['image_source'] ) ? 'thumbnail_id' : $options['image_source'] );
		$attachment = get_term_meta( $term->term_id, $image_source, true );

		if( empty( $attachment ) )
			return $thumbnail;

		if( is_numeric( $attachment ) ) 
		{
			$thumbsize = ( empty( $options['image_size'] ) ? 'full' : $options['image_size'] );
			$thumbnail = wp_get_attachment_image_url( $attachment, $thumbsize );
		}
		else 
		{
			$supported_image 	= [ 'gif', 'jpg', 'jpeg', 'png', 'svg', 'webp' ];
			$dots 				= explode( ".", $attachment );
			$ext 				= $dots[ ( count( $dots ) - 1 ) ];

			if ( in_array( $ext, $supported_image ) )
				$thumbnail = $attachment;
		}
			
		return $thumbnail;
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'image_source' => [
				'type' 			=> 'text',
				'title' 		=> esc_html__('Meta Field'),
				'description' 	=> esc_html__( 'Enter term meta key of your image. It would either return the image ID or URL.', 'ziultimate' ),
			],
			'image_size' => [
				'type' 			=> 'select',
				'title' 		=> esc_html__('Image Size'),
				'description' 	=> esc_html__( 'It will work when return format is image ID.', 'ziultimate' ),
				'default' 		=> 'full',
				'options' 		=> $this->get_thumbnail_sizes()
			]
		];
	}

	/**
	 * Get Content
	 *
	 * Returns the current post title
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		echo $this->get_thumbnail( $options ); 
	}
}
