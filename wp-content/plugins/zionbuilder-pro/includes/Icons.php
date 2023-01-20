<?php

namespace ZionBuilderPro;
use ZionBuilder\FileSystem;
use ZionBuilder\Plugin as FreePlugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Icons {
	const ICONS_CACHE_OPTION = '_zionbuilder_icons';
	const ICONS_CACHE_FOLDER = 'icons';

	private $temp_folder_dir = null;

	public function __construct() {
		add_filter( 'zionbuilder/icons/locations', [ $this, 'add_icon_locations' ] );
	}

	public function get_icons_folder_dir() {
		$zionbuilder_upload_dir = FileSystem::get_zionbuilder_upload_dir();

		$icons_folder = [
			'baseurl' => set_url_scheme( esc_url( $zionbuilder_upload_dir['baseurl'] . trailingslashit( self::ICONS_CACHE_FOLDER ) ) ),
			'basedir' => $zionbuilder_upload_dir['basedir'] . trailingslashit( self::ICONS_CACHE_FOLDER ),
		];

		if ( ! is_dir( $icons_folder['basedir'] ) ) {
			wp_mkdir_p( $icons_folder['basedir'] );
		}

		return $icons_folder;
	}

	public function add_icon_locations( $locations ) {
		$zionbuilder_upload_dir = $this->get_icons_folder_dir();

		// Uploaded icons
		$uploaded_icons = get_option( self::ICONS_CACHE_OPTION );

		if ( ! empty( $uploaded_icons ) ) {
			// Set the location for each font
			foreach ( $uploaded_icons as $font_name => $font_config ) {
				$font_config['url']  = trailingslashit( $zionbuilder_upload_dir['baseurl'] . $font_name );
				$font_config['path'] = trailingslashit( $zionbuilder_upload_dir['basedir'] . $font_name );

				$locations[$font_config['id']] = $font_config;
			}
		}

		return $locations;
	}

	public function upload_icons_package( $zip_file ) {
		$allowed_extensions    = [ 'eot', 'svg', 'ttf', 'woff' ];
		$folder_name           = trailingslashit( 'icons/' . uniqid() );
		$this->temp_folder_dir = FileSystem::get_temp_upload_dir( $folder_name );
		$unzipped_package      = FileSystem::unzip_archive( $zip_file, $this->temp_folder_dir['basedir'], $allowed_extensions );

		if ( is_wp_error( $unzipped_package ) ) {
			return $unzipped_package;
		}

		// ADD THE FONT INFO TO DB AND CREATE ICON_LIST
		$font_info = $this->get_icons_info();
		if ( is_wp_error( $font_info ) ) {
			return $font_info;
		}

		// Remove from DB
		$saved_icons = get_option( self::ICONS_CACHE_OPTION );

		if ( isset( $saved_icons[$font_info['id']] ) ) {
			// Cleanup
			$this->cleanup_import();
			return new \WP_Error( 'existing_font', __( 'The uploaded font already exists. Please delete it before re-uploading', 'zionbuilder-pro' ) );
		}

		// Rename icons folder name
		$icons_folder_location = $this->get_icons_folder_dir();
		$new_name              = $icons_folder_location['basedir'] . $font_info['id'];
		rename( $this->temp_folder_dir['basedir'], $new_name );

		// Save fonts
		if ( empty( $saved_icons ) ) {
			$saved_icons = [];
		}

		$icon_package_config = [
			'name'      => $font_info['name'],
			'file_name' => $font_info['file_name'],
			'id'        => $font_info['id'],
			'url'       => trailingslashit( $icons_folder_location['baseurl'] . $font_info['id'] ),
		];

		$saved_icons[$font_info['id']] = $icon_package_config;
		$saved_icons                   = update_option( self::ICONS_CACHE_OPTION, $saved_icons );

		// Cleanup
		$this->cleanup_import();

		$font_info['css'] = FreePlugin::$instance->icons->get_icon_package_css( $icon_package_config );

		return $font_info;
	}


	public function cleanup_import() {
		FileSystem::get_file_system()->rmdir( $this->temp_folder_dir['basedir'], true );
	}

	public function get_icons_info() {
		$temp_folder_dir = $this->temp_folder_dir['basedir'];
		$svg_file        = FileSystem::find_file( $temp_folder_dir, '.svg' );
		$return          = [];
		if ( empty( $svg_file ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'The zip did not contained any svg files.', 'zionbuilder-pro' ) );
		}

		$svgFile = trailingslashit( $temp_folder_dir ) . $svg_file;
		if ( ! is_file( $svgFile ) || ! is_readable( $svgFile ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'Could not find or read the svg file.', 'zionbuilder-pro' ) );
		}

		$fs        = FileSystem::get_file_system();
		$file_data = $fs->get_contents( $svgFile );

		if ( is_wp_error( $file_data ) || empty( $file_data ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'The svg file could not be opened.', 'zionbuilder-pro' ) );
		}

		$xml = simplexml_load_string( $file_data );

		// make sure this is a valid font archive
		if ( ! is_object( $xml ) || ! isset( $xml->defs ) || ! isset( $xml->defs->font ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'Could not find or read the svg file.', 'zionbuilder-pro' ) );
		}
		$font_attr = $xml->defs->font->attributes();

		// Don't proceed if we don't have a name for this icons
		if ( empty( $font_attr['id'] ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'Could not extract icons package name from archive.', 'zionbuilder-pro' ) );
		}

		// Get the font family name
		if ( ! isset( $xml->defs->font->{'font-face'} ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'Could not find or read the svg file.', 'zionbuilder-pro' ) );
		}
		$font_face      = $xml->defs->font->{'font-face'}->attributes();
		$font_name      = isset( $font_face['font-family'] ) ? $font_face['font-family'] : $font_attr['id'];
		$icon_pack_data = [
			'id'   => (string)$font_attr['id'],
			'name' => (string)$font_name,
		];

		$icons  = [];
		$glyphs = $xml->defs->font->children();

		foreach ( $glyphs as $item => $glyph ) {
			if ( $item == 'glyph' ) {
				$attributes = $glyph->attributes();
				$unicode    = (string)$attributes['unicode'];
				$d          = (string)$attributes['d'];
				$glyph_name = (string)$attributes['glyph-name'];
				if ( ! empty( $d ) ) {
					$unicode_key = trim( json_encode( $unicode ), '\\\"' );
					if ( $item == 'glyph' && ! empty( $unicode_key ) && trim( $unicode_key ) != '' ) {
						$icons[] = [
							'name'    => $glyph_name,
							'unicode' => $unicode_key,
						];
					}
				}
			}
		}

		// Don't proceed if we don't have valid icons
		if ( empty( $icons ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'Could not extract icons from uploaded package.', 'zionbuilder-pro' ) );
		}

		$icon_pack_data['icons'] = $icons;

		$icon_list_file    = $temp_folder_dir . '/icons.json';
		$icons_config_data = json_encode( $icon_pack_data );
		$fs->put_contents( $icon_list_file, $icons_config_data, 0644 );

		$file_name                   = pathinfo( $svg_file )['filename'];
		$icon_pack_data['file_name'] = $file_name;

		return $icon_pack_data;
	}

	/**
	 * Delete a font package by name
	 *
	 * @param $icons_package_name
	 *
	 * @return mixed|\WP_Error
	 */
	public function delete_icons_package( $icons_package_name ) {

		// Remove from DB
		$uploaded_icons = get_option( self::ICONS_CACHE_OPTION );

		if ( ! isset( $uploaded_icons[$icons_package_name] ) ) {
			return new \WP_Error( 'missing_font_folder', __( 'Could not delete the icon package.', 'zionbuilder-pro' ) );
		}

		unset( $uploaded_icons[$icons_package_name] );

		update_option( self::ICONS_CACHE_OPTION, $uploaded_icons );

		// Remove folders
		$zionbuilder_upload_dir = $this->get_icons_folder_dir();
		FileSystem::get_file_system()->rmdir( $zionbuilder_upload_dir['basedir'] . $icons_package_name, true );

		return FreePlugin::instance()->icons->get_icons_locations( false );
	}

	public function get_icon_package_dir( $icon_package_name ) {
		$icons_directory = $this->get_icons_folder_dir();

		return $icons_directory['basedir'] . $icon_package_name;
	}



	/**
	 * Delete a font package by name
	 *
	 * @param mixed $icons_package_name
	 */
	public function create_icons_package( $icons_package_name ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new \WP_Error( 'zip_archive_not_installed', __( 'ZipArchive not installed. Contact your hosting provider and ask them to enable Zip Archive PHP extension.', 'zionbuilder-pro' ) );
		}

		// Create the temp dir
		$tempdir = FileSystem::get_temp_upload_dir( 'icons' );
		if ( ! $tempdir ) {
			return new \WP_Error( 'cannot_create_temp_folder', 'Could not create temporarry folder ' . $tempdir['basedir'] );
		}

		$export_path       = trailingslashit( $tempdir['basedir'] ) . $icons_package_name . '.zip';
		$icon_package_data = FreePlugin::$instance->icons->get_icon_package_data( $icons_package_name );

		if ( ! $icon_package_data ) {
			return new \WP_Error( 'cannot_find_icon_data', 'Could not find icon data' );
		}

		$font_path = $icon_package_data['path'];

		$zip     = new \ZipArchive;
		$success = $zip->open( $export_path, \ZIPARCHIVE::CREATE | \ZipArchive::OVERWRITE );

		if ( $success !== true ) {
			return new \WP_Error( 'cannot_create_zip_file', 'Could not create the export file in ' . $export_path );
		}

		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $font_path ),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		// Add all font files to zip
		foreach ( $files as $file ) {
			// Skip directories (they would be added automatically)
			if ( ! $file->isDir() ) {
				// Get real and relative path for current file
				$filePath = $file->getRealPath();

				// Add current file to archive
				$zip->addFile( $filePath, $file->getFilename() );
			}
		}

		// Close the zip
		$zip->close();

		return $icons_package_name;
	}


	public function download_icons_package( $icons_package_name ) {
		$fs                    = FileSystem::get_file_system();
		$tempdir               = FileSystem::get_temp_upload_dir( 'icons' );
		$exported_icon_package = trailingslashit( $tempdir['basedir'] ) . $icons_package_name . '.zip';

		if ( ! is_file( $exported_icon_package ) ) {
			return new \WP_Error( 'cannot_create_temp_folder', 'The icon package archive was not found' );
		}

		$archive_file_name = basename( $exported_icon_package );
		header( 'Content-type: application/zip' );
		header( "Content-Disposition: attachment; filename=$archive_file_name" );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo FileSystem::get_file_system()->get_contents( $exported_icon_package );
		FileSystem::get_file_system()->delete( $exported_icon_package );
		exit();
	}
}
