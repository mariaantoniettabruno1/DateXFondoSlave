<?php
namespace ZiUltimate;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use ZiUltimate\Conditions\Acf;
use ZiUltimate\Conditions\Post;
use ZiUltimate\Conditions\User;
use ZiUltimate\Conditions\Browser;
use ZiUltimate\Conditions\WooCommerce;
use ZiUltimate\Admin\License;

class RegisterConditions {

	public function __construct() {
		if( ! License::has_valid_license() )
			return;
		
		add_action( 'zionbuilder-pro/element-conditions/init', [$this, 'init_conditions'] );
	}

	public function init_conditions() {
		Post::init_conditions();
		User::init_conditions();
		Browser::init_conditions();

		if( class_exists( 'WooCommerce' ) ) {
			WooCommerce::init_conditions();
		}

		if( function_exists( 'acf' ) ) {
			Acf::init_conditions();
		}
	}
}