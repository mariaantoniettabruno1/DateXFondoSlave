<?php

namespace ZionBuilderPro;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class ProMasks {
	public function __construct() {
		if ( License::has_valid_license() ) {
			add_filter( 'zionbuilder/masks', [ $this, 'add_shapes' ] );
		}
	}

	public function add_shapes( $shapes ) {
		$pro_masks       = [
			'bottom-pro-mask_01'               => [
				'url' => Utils::get_file_url( 'assets/masks/bottom-pro-mask_01.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-pro-mask_01.svg' ),
			],
			'bottom-pro-mask_02'               => [
				'url' => Utils::get_file_url( 'assets/masks/bottom-pro-mask_02.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-pro-mask_02.svg' ),
			],
			'bottom-pro-mask_03'               => [
				'url' => Utils::get_file_url( 'assets/masks/bottom-pro-mask_03.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-pro-mask_03.svg' ),
			],
			'bottom-pro-mask_04'               => [
				'url' => Utils::get_file_url( 'assets/masks/bottom-pro-mask_04.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-pro-mask_04.svg' ),
			],
			'bottom-pro-mask_05'               => [
				'url' => Utils::get_file_url( 'assets/masks/bottom-pro-mask_05.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-pro-mask_05.svg' ),
			],
			'bottom-pro-mask_06'               => [
				'url' => Utils::get_file_url( 'assets/masks/bottom-pro-mask_06.svg' ),
				'path' => Utils::get_file_path( 'assets/masks/bottom-pro-mask_06.svg' ),
			],
		];

		return array_merge( $shapes, $pro_masks );
	}
}
