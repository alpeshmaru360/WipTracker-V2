<?php
/* One Click Demo Import support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_ocdi_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_ocdi_theme_setup9', 9 );
	function alliance_ocdi_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'alliance_filter_tgmpa_required_plugins', 'alliance_ocdi_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'alliance_ocdi_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('alliance_filter_tgmpa_required_plugins',	'alliance_ocdi_tgmpa_required_plugins');
	function alliance_ocdi_tgmpa_required_plugins( $list = array() ) {
		if ( alliance_storage_isset( 'required_plugins', 'one-click-demo-import' ) && alliance_storage_get_array( 'required_plugins', 'one-click-demo-import', 'install' ) !== false ) {
			$list[] = array(
				'name'     => alliance_storage_get_array( 'required_plugins', 'one-click-demo-import', 'title' ),
				'slug'     => 'one-click-demo-import',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin is installed and activated
if ( ! function_exists( 'alliance_exists_ocdi' ) ) {
	function alliance_exists_ocdi() {
		return class_exists( 'OCDI_Plugin' );
	}
}

