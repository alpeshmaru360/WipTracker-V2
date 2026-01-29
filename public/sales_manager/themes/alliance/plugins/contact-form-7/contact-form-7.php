<?php
/* Contact Form 7 support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_cf7_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_cf7_theme_setup9', 9 );
	function alliance_cf7_theme_setup9() {
		if ( alliance_exists_cf7() ) {
			add_action( 'wp_enqueue_scripts', 'alliance_cf7_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_cf7', 'alliance_cf7_frontend_scripts', 10, 1 );
			add_filter( 'alliance_filter_merge_styles', 'alliance_cf7_merge_styles' );
			add_filter( 'alliance_filter_merge_scripts', 'alliance_cf7_merge_scripts' );
		}
		if ( is_admin() ) {
			add_filter( 'alliance_filter_tgmpa_required_plugins', 'alliance_cf7_tgmpa_required_plugins' );
			add_filter( 'alliance_filter_theme_plugins', 'alliance_cf7_theme_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'alliance_cf7_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('alliance_filter_tgmpa_required_plugins',	'alliance_cf7_tgmpa_required_plugins');
	function alliance_cf7_tgmpa_required_plugins( $list = array() ) {
		if ( alliance_storage_isset( 'required_plugins', 'contact-form-7' ) && alliance_storage_get_array( 'required_plugins', 'contact-form-7', 'install' ) !== false ) {
			// CF7 plugin
			$list[] = array(
				'name'     => alliance_storage_get_array( 'required_plugins', 'contact-form-7', 'title' ),
				'slug'     => 'contact-form-7',
				'required' => false,
			);
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'alliance_cf7_theme_plugins' ) ) {
	//Handler of the add_filter( 'alliance_filter_theme_plugins', 'alliance_cf7_theme_plugins' );
	function alliance_cf7_theme_plugins( $list = array() ) {
		return alliance_add_group_and_logo_to_slave( $list, 'contact-form-7', 'contact-form-7-' );
	}
}



// Check if cf7 installed and activated
if ( ! function_exists( 'alliance_exists_cf7' ) ) {
	function alliance_exists_cf7() {
		return class_exists( 'WPCF7' );
	}
}

// Enqueue custom scripts
if ( ! function_exists( 'alliance_cf7_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_cf7_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_cf7', 'alliance_cf7_frontend_scripts', 10, 1 );
	function alliance_cf7_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'cf7' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/contact-form-7/contact-form-7.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-contact-form-7', $alliance_url, array(), null );
			}
			$alliance_url = alliance_get_file_url( 'plugins/contact-form-7/contact-form-7.js' );
			if ( '' != $alliance_url ) {
				wp_enqueue_script( 'alliance-contact-form-7', $alliance_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'alliance_cf7_merge_styles' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles', 'alliance_cf7_merge_styles');
	function alliance_cf7_merge_styles( $list ) {
		$list[ 'plugins/contact-form-7/contact-form-7.css' ] = false;
		return $list;
	}
}

// Merge custom scripts
if ( ! function_exists( 'alliance_cf7_merge_scripts' ) ) {
	//Handler of the add_filter('alliance_filter_merge_scripts', 'alliance_cf7_merge_scripts');
	function alliance_cf7_merge_scripts( $list ) {
		$list[ 'plugins/contact-form-7/contact-form-7.js' ] = false;
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( alliance_exists_cf7() ) {
	$alliance_fdir = alliance_get_file_dir( 'plugins/contact-form-7/contact-form-7-style.php' );
	if ( ! empty( $alliance_fdir ) ) {
		require_once $alliance_fdir;
	}
}
