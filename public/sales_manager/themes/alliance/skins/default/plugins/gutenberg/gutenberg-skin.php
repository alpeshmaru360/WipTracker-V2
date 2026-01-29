<?php
/* Gutenberg skin-specific functions
------------------------------------------------------------------------------- */

// Theme init priorities:
//10 - standard Theme init procedures (not ordered)
if ( ! function_exists( 'alliance_gutenberg_skin_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_gutenberg_skin_theme_setup9', 9 );
	function alliance_gutenberg_skin_theme_setup9() {
		remove_action( 'alliance_action_skin_switched', 'alliance_gutenberg_fse_update_theme_json' );
		remove_action( 'alliance_action_save_options', 'alliance_gutenberg_fse_update_theme_json' );
		remove_action( 'trx_addons_action_save_options', 'alliance_gutenberg_fse_update_theme_json' );
		remove_action( 'alliance_filter_list_footer_styles', 'alliance_gutenberg_fse_list_footer_styles');
		remove_action( 'alliance_filter_list_header_styles', 'alliance_gutenberg_fse_list_header_styles');

		add_filter( 'alliance_filter_localize_script_admin',	'alliance_gutenberg_skin_localize_script');
	}
}

// Add plugin's specific variables to the scripts
if ( ! function_exists( 'alliance_gutenberg_skin_localize_script' ) ) {
	//Handler of the add_filter( 'alliance_filter_localize_script_admin',	'alliance_gutenberg_skin_localize_script');
	function alliance_gutenberg_skin_localize_script( $arr ) {
		// Color scheme
		$arr['page_content'] = alliance_skin_page_content_type();
		return $arr;
	}
}