<?php

namespace ZionBuilderPro\Conditions;

class PageRequest {
	const TYPE_FRONT_PAGE        = 'front_page';
	const TYPE_SEARCH            = 'search';
	const TYPE_404               = '404';
	const TYPE_SINGULAR          = 'singular';
	const TYPE_TAXONOMY          = 'taxonomy';
	const TYPE_POST_TYPE_ARCHIVE = 'post_type_archive';
	const TYPE_AUTHOR_ARCHIVE    = 'author_archive';
	const TYPE_DATE_ARCHIVE      = 'date_archive';

	private $type    = null;
	private $subtype = null;
	private $id      = null;

	public function __construct() {
		$id      = get_queried_object_id();
		$object  = get_queried_object();
		$type    = null;
		$subtype = null;

		// Handle blog posts page
		$page_for_posts  = (int) get_option( 'page_for_posts' );
		$is_blog_archive = 0 !== $page_for_posts && is_page( $page_for_posts );

		if ( is_front_page() ) {
			$type = self::TYPE_FRONT_PAGE;
		} elseif ( is_search() ) {
			$type = self::TYPE_SEARCH;
		} elseif ( is_404() ) {
			$type = self::TYPE_404;
		} elseif ( is_home() || $is_blog_archive ) {
			$type    = self::TYPE_POST_TYPE_ARCHIVE;
			$subtype = 'post';
		} elseif ( is_singular() ) {
			$type    = self::TYPE_SINGULAR;
			$subtype = get_post_type( $id );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$type    = self::TYPE_TAXONOMY;
			$subtype = $object->taxonomy;
		} elseif ( is_post_type_archive() ) {
			$type    = self::TYPE_POST_TYPE_ARCHIVE;
			$subtype = $object->name;
		} elseif ( is_author() ) {
			$type = self::TYPE_AUTHOR_ARCHIVE;
		} elseif ( is_date() ) {
			$type    = self::TYPE_DATE_ARCHIVE;
			$subtype = get_post_type();
		}

		$this->type    = apply_filters( 'zionbuilderpro/conditions/page_request/type', $type, $id, $object );
		$this->subtype = apply_filters( 'zionbuilderpro/conditions/page_request/subtype', $subtype, $type, $id, $object );
		$this->id      = apply_filters( 'zionbuilderpro/conditions/page_request/subtype', $id, $type, $subtype, $object );
	}

	public function get_type() {
		return $this->type;
	}

	public function get_subtype() {
		return $this->subtype;
	}

	public function get_id() {
		return $this->id;
	}

	public function is_type( $type ) {
		return $this->get_type() === $type;
	}

	public function is_subtype( $subtype ) {
		return $this->get_subtype() === $subtype;
	}

}
