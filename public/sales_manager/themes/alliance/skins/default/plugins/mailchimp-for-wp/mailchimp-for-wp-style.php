<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'alliance_mailchimp_get_css' ) ) {
	add_filter( 'alliance_filter_get_css', 'alliance_mailchimp_get_css', 10, 2 );
	function alliance_mailchimp_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS



CSS;
		}

		return $css;
	}
}

