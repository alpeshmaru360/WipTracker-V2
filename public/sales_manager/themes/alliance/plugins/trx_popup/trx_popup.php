<?php
/* ThemeREX Popup support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_trx_popup_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_trx_popup_theme_setup9', 9 );
	function alliance_trx_popup_theme_setup9() {
		if ( alliance_exists_trx_popup() ) {
			add_action( 'wp_enqueue_scripts', 'alliance_trx_popup_frontend_scripts', 1100 );
			add_filter( 'alliance_filter_merge_styles', 'alliance_trx_popup_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'alliance_filter_tgmpa_required_plugins', 'alliance_trx_popup_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'alliance_trx_popup_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter( 'alliance_filter_tgmpa_required_plugins',	'alliance_trx_popup_tgmpa_required_plugins' );
	function alliance_trx_popup_tgmpa_required_plugins( $list = array() ) {
		if ( alliance_storage_isset( 'required_plugins', 'trx_popup' ) && alliance_storage_get_array( 'required_plugins', 'trx_popup', 'install' ) !== false && alliance_is_theme_activated() ) {
			$path = alliance_get_plugin_source_path( 'plugins/trx_popup/trx_popup.zip' );
			if ( ! empty( $path ) || alliance_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => alliance_storage_get_array( 'required_plugins', 'trx_popup', 'title' ),
					'slug'     => 'trx_popup',
					'source'   => ! empty( $path ) ? $path : 'upload://trx_popup.zip',
					'version'  => '1.0',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'alliance_exists_trx_popup' ) ) {
	function alliance_exists_trx_popup() {
		return defined( 'TRX_POPUP_URL' );
	}
}

// Enqueue custom scripts
if ( ! function_exists( 'alliance_trx_popup_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_trx_popup_frontend_scripts', 1100 );
	function alliance_trx_popup_frontend_scripts() {
		if ( alliance_is_on( alliance_get_theme_option( 'debug_mode' ) ) ) {
			$alliance_url = alliance_get_file_url( 'plugins/trx_popup/trx_popup.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-trx-popup', $alliance_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'alliance_trx_popup_merge_styles' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles', 'alliance_trx_popup_merge_styles');
	function alliance_trx_popup_merge_styles( $list ) {
		$list[ 'plugins/trx_popup/trx_popup.css' ] = true;
		return $list;
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( alliance_exists_trx_popup() ) {
	$alliance_fdir = alliance_get_file_dir( 'plugins/trx_popup/trx_popup-style.php' );
	if ( ! empty( $alliance_fdir ) ) {
		require_once $alliance_fdir;
	}
}
