<?php

namespace ZionBuilderPro\Conditions;

use ZionBuilderPro\Conditions\PageRequest;
use ZionBuilderPro\Conditions\Conditions;

class Validations {
	private static $user_logged_in = null;


	/**
	 * Returns true if the frontpage of the site is serverd
	 *
	 * @param PageRequest $page_request
	 *
	 * @return boolean
	 */
	public static function is_homepage( $page_request ) {
		return $page_request->is_type( PageRequest::TYPE_FRONT_PAGE );
	}


	/**
	 * Returns true if the search page of the site is serverd
	 *
	 * @param PageRequest $page_request
	 *
	 * @return boolean
	 */
	public static function is_search( $page_request ) {
		return $page_request->is_type( PageRequest::TYPE_SEARCH );
	}


	/**
	 * Returns true if the 404 page of the site is serverd
	 *
	 * @param PageRequest $page_request
	 *
	 * @return boolean
	 */
	public static function is_404( $page_request ) {
		return $page_request->is_type( PageRequest::TYPE_404 );
	}


	/**
	 * Returns true if any singular page of the site is serverd
	 *
	 * @param PageRequest $page_request
	 *
	 * @return boolean
	 */
	public static function is_singular( $page_request ) {
		return $page_request->is_type( PageRequest::TYPE_SINGULAR );
	}


	/**
	 * Returns true if any archive page of the site is serverd
	 *
	 * @param PageRequest $page_request
	 *
	 * @return boolean
	 */
	public static function is_archive( $page_request ) {
		return $page_request->is_type( PageRequest::TYPE_POST_TYPE_ARCHIVE );
	}

	/**
	 * Returns true if any user archive page is displayed
	 *
	 * @param PageRequest $page_request
	 *
	 * @return boolean
	 */
	public static function is_author_archive( $page_request ) {
		return $page_request->is_type( PageRequest::TYPE_AUTHOR_ARCHIVE );
	}


	/**
	 * Returns true if a specific user archive page is displayed
	 *
	 * @param PageRequest $page_request
	 *
	 * @return boolean
	 */
	public static function is_author_archive_user( $page_request, $condition_id, $condition_value ) {
		$passed_value = false;

		if ( is_array( $condition_value ) ) {
			$passed_value = in_array( $page_request->get_id(), $condition_value, true );
		} else {
			$passed_value = $condition_value;
		}

		return $page_request->is_type( PageRequest::TYPE_AUTHOR_ARCHIVE ) && $passed_value;
	}



	/**
	 * Returns true if the user is logged in
	 *
	 * @return boolean
	 */
	public static function is_logged_in() {
		if ( null === self::$user_logged_in ) {
			self::$user_logged_in = is_user_logged_in();
		}
		return self::$user_logged_in;
	}


	/**
	 * Returns true if the user is not logged in
	 *
	 * @return boolean
	 */
	public static function is_not_logged_in() {
		return ! self::is_logged_in();
	}

	public static function in_single_post_type( $page_request, $condition_id, $condition_value ) {
		$condition_id_data = Conditions::get_condition_id_data( $condition_id );
		$passed_value      = false;

		if ( is_array( $condition_value ) ) {
			$passed_value = in_array( $page_request->get_id(), $condition_value, true );
		} else {
			$passed_value = $condition_value;
		}

		return $page_request->is_type( PageRequest::TYPE_SINGULAR ) && $page_request->is_subtype( $condition_id_data[1] ) && $passed_value;
	}

	public static function in_single_post_type_in_taxonomy( $page_request, $condition_id, $condition_value ) {
		$condition_id_data = Conditions::get_condition_id_data( $condition_id );
		$passed_value      = $page_request->is_type( PageRequest::TYPE_SINGULAR ) && $page_request->is_subtype( $condition_id_data[1] );
		$taxonomy          = $condition_id_data[3];

		if ( ! $passed_value ) {
			return false;
		}

		return has_term( $condition_value, $taxonomy );
	}

	public static function in_archive_post_type( $page_request, $condition_id, $condition_value ) {
		$condition_id_data = Conditions::get_condition_id_data( $condition_id );
		return $page_request->is_type( PageRequest::TYPE_POST_TYPE_ARCHIVE ) && $page_request->is_subtype( $condition_id_data[1] ) && $condition_value;
	}

	public static function in_date_archive( $page_request, $condition_id, $condition_value ) {
		$condition_id_data = Conditions::get_condition_id_data( $condition_id );
		return $page_request->is_type( PageRequest::TYPE_DATE_ARCHIVE ) && $page_request->is_subtype( $condition_id_data[1] ) && $condition_value;
	}

	public static function in_taxonomy_archive( $page_request, $condition_id, $condition_value ) {
		$condition_id_data = Conditions::get_condition_id_data( $condition_id );
		$passed_value      = false;
		if ( is_array( $condition_value ) ) {
			$passed_value = in_array( $page_request->get_id(), $condition_value, true );
		} else {
			$passed_value = $condition_value;
		}

		return $page_request->is_type( PageRequest::TYPE_TAXONOMY ) && $page_request->is_subtype( $condition_id_data[1] ) && $passed_value;
	}
}
