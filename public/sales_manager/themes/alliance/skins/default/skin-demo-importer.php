<?php
/**
 * Skin Demo importer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.76.0
 */


// Theme storage
//-------------------------------------------------------------------------

alliance_storage_set( 'theme_demo_url', '//alliance.themerex.net' );


//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'alliance_skin_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'alliance_skin_importer_set_options', 9 );
	function alliance_skin_importer_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			$demo_type = function_exists( 'alliance_skins_get_current_skin_name' ) ? alliance_skins_get_current_skin_name() : 'default';
			if ( 'default' != $demo_type ) {
				$options['demo_type'] = $demo_type;
				$options['files'][ $demo_type ] = $options['files']['default'];	// Copy all settings from 'default' to the new demo type
				unset($options['files']['default']);
			}
			// Override some settings in the new demo type
			$options['files'][ $demo_type ]['title']       = esc_html__( 'Alliance Demo', 'alliance' );
			$options['files'][ $demo_type ]['domain_dev']  = alliance_add_protocol( '//alliance.dv.themerex.net' );                // Developers domain
			$options['files'][ $demo_type ]['domain_demo'] = alliance_add_protocol( alliance_storage_get( 'theme_demo_url' ) );   // Demo-site domain
		}
		return $options;
	}
}


//------------------------------------------------------------------------
// OCDI support
//------------------------------------------------------------------------

// Set theme specific OCDI options
if ( ! function_exists( 'alliance_skin_ocdi_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'alliance_skin_ocdi_set_options', 9 );
	function alliance_skin_ocdi_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Demo-site domain
			$options['files']['ocdi']['title']       = esc_html__( 'Alliance OCDI Demo', 'alliance' );
			$options['files']['ocdi']['domain_demo'] = alliance_add_protocol( alliance_storage_get( 'theme_demo_url' ) );
			// If theme need more demo - just copy 'default' and change required parameters
		}
		return $options;
	}
}
