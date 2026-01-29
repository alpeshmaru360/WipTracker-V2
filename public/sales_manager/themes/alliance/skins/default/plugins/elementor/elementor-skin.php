<?php
/* Elementor support functions
------------------------------------------------------------------------------- */


// Return true if Elementor exists and current mode is edit
if ( !function_exists( 'alliance_elm_skin_is_edit_mode' ) ) {
	function alliance_elm_skin_is_edit_mode() {
		static $is_edit_mode = -1;
		if ( $is_edit_mode === -1 ) {
			$is_edit_mode = alliance_exists_elementor()
								&& ( \Elementor\Plugin::instance()->editor->is_edit_mode()
									|| ( alliance_get_value_gp( 'post' ) > 0
										&& alliance_get_value_gp( 'action' ) == 'elementor'
										)
									|| ( is_admin()
										&& in_array( alliance_get_value_gp( 'action' ), array( 'elementor', 'elementor_ajax', 'wp_ajax_elementor_ajax' ) )
										)
									);
		}
		return $is_edit_mode;
	}
}

// Return list of the empty_space heights
if ( ! function_exists( 'alliance_trx_addons_get_list_sc_empty_space_heights' ) ) {
	add_filter( 'trx_addons_filter_get_list_sc_empty_space_heights', 'alliance_trx_addons_get_list_sc_empty_space_heights' );
	function alliance_trx_addons_get_list_sc_empty_space_heights( $array ) {
		alliance_array_insert_after( $array, 'huge', array( 'ginormous' => esc_html__( 'Ginormous', 'alliance' ) ) );
		return $array;
	}
}