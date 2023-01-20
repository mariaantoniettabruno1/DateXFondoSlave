<?php

namespace ZionBuilderPro\DynamicContent\Fields;

use ZionBuilderPro\DynamicContent\BaseField;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CommentsNumber
 *
 * @package ZionBuilderPro\DynamicContent\Fields
 */
class CommentsNumber extends BaseField {
	public function get_category() {
		return self::CATEGORY_TEXT;
	}

	/**
	 * Will load the field only if the global post is set
	 *
	 * @return boolean
	 */
	public function can_load() {
		return isset( $GLOBALS['post'] );
	}

	public function get_group() {
		return 'post';
	}

	public function get_id() {
		return 'comments-number';
	}

	public function get_name() {
		return esc_html__( 'Comments number', 'zionbuilder-pro' );
	}

	/**
	 * Render the text for comments
	 *
	 * @param mixed $options
	 */
	public function render( $options ) {
		$post                 = $GLOBALS['post'];
		$noCommentsText       = ( empty( $options['no_comments_text'] ) ? esc_html__( 'No comments', 'zionbuilder-pro' ) : $options['no_comments_text'] );
		$oneCommentText       = ( empty( $options['one_comment_text'] ) ? esc_html__( '1 comment', 'zionbuilder-pro' ) : $options['one_comment_text'] );
		$multipleCommentsText = ( empty( $options['multiple_comments_text'] ) ? esc_html__( '%s comments', 'zionbuilder-pro' ) : $options['multiple_comments_text'] );

		$numComments = get_comments(
			[
				'count'   => true,
				'post_id' => $post->ID,
			]
		);
		if ( empty( $numComments ) ) {
			echo wp_kses_post( $noCommentsText );
		} elseif ( $numComments == 1 ) {
			echo wp_kses_post( $oneCommentText );
		} else {
			echo wp_kses_post( sprintf( $multipleCommentsText, $numComments ) );
		}
	}

	/**
	 * Return the data for this field used in preview/editor
	 */
	public function get_data() {
		// Defaults
		$post                 = $GLOBALS['post'];
		$noCommentsText       = esc_html__( 'No comments', 'zionbuilder-pro' );
		$oneCommentText       = esc_html__( '1 comment', 'zionbuilder-pro' );
		$multipleCommentsText = esc_html__( '%s comments', 'zionbuilder-pro' );

		$numComments = get_comments(
			[
				'count'   => true,
				'post_id' => $post->ID,
			]
		);
		if ( empty( $numComments ) ) {
			return wp_kses_post( $noCommentsText );
		} elseif ( $numComments == 1 ) {
			return wp_kses_post( $oneCommentText );
		}
		return wp_kses_post( sprintf( $multipleCommentsText, $numComments ) );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return [
			'no_comments_text'       => [
				'type'        => 'text',
				'title'       => esc_html__( 'No comments text', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Set the desired text that will be displayed when there are no comments set to this post.', 'zionbuilder-pro' ),
				'default'     => esc_html__( 'No comments', 'zionbuilder-pro' ),
				'dynamic'     => false,
			],
			'one_comment_text'       => [
				'type'        => 'text',
				'title'       => esc_html__( 'One comment text', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Set the desired text that will be displayed when there is only 1 comment.', 'zionbuilder-pro' ),
				'default'     => esc_html__( '1 comment', 'zionbuilder-pro' ),
				'dynamic'     => false,
			],
			'multiple_comments_text' => [
				'type'        => 'text',
				'title'       => esc_html__( 'Multiple comments text', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Set the desired text that will be displayed when there are multiple comments. Use %s to where you want to display the comments number.', 'zionbuilder-pro' ),
				'default'     => esc_html__( '%s comments', 'zionbuilder-pro' ),
				'dynamic'     => false,
			],
		];
	}
}
