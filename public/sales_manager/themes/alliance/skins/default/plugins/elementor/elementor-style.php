<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'alliance_elm_get_css' ) ) {
	add_filter( 'alliance_filter_get_css', 'alliance_elm_get_css', 10, 2 );
	function alliance_elm_get_css( $css, $args ) {
		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS

.elementor-alert .elementor-alert-title,
.h1 .elementor-heading-title {
	{$fonts['h1_font-family']}
	font-size: var(--theme-font-h1_font-size); /* replace with {$fonts['h1_font-size']} */
	line-height: var(--theme-font-h1_line-height); /* replace with {$fonts['h1_line-height']} */
	letter-spacing: var(--theme-font-h1_letter-spacing); /* replace with {$fonts['h1_letter-spacing']} */
	{$fonts['h1_font-weight']}
	{$fonts['h1_font-style']}
	{$fonts['h1_text-decoration']}
	{$fonts['h1_text-transform']}
}

CSS;
		}

		return $css;
	}
}


// Add theme-specific CSS-animations
if ( ! function_exists( 'alliance_elm_add_theme_animations' ) ) {
	add_filter( 'elementor/controls/animations/additional_animations', 'alliance_elm_add_theme_animations' );
	function alliance_elm_add_theme_animations( $animations ) {
		/* To add a theme-specific animations to the list:
			1) Merge to the array 'animations': array(
													esc_html__( 'Theme Specific', 'alliance' ) => array(
														'ta_custom_1' => esc_html__( 'Custom 1', 'alliance' )
													)
												)
			2) Add a CSS rules for the class '.ta_custom_1' to create a custom entrance animation
		*/
		$animations = array_merge(
						$animations,
						array(
							esc_html__( 'Theme Specific', 'alliance' ) => array(
																			'ta_fadeinup'     => esc_html__( 'Fade In Up (Short)', 'alliance' ),
																			'ta_fadeinright'  => esc_html__( 'Fade In Right (Short)', 'alliance' ),
																			'ta_fadeinleft'   => esc_html__( 'Fade In Left (Short)', 'alliance' ),
																			'ta_fadeindown'   => esc_html__( 'Fade In Down (Short)', 'alliance' ),
																			'ta_fadein'       => esc_html__( 'Fade In (Short)', 'alliance' ),
																			'ta_under_strips' => esc_html__( 'Under strips', 'alliance' ),
																			)
							)
						);
		return $animations;
	}
}

// Load skin-specific functions
$fdir = alliance_get_file_dir( 'plugins/elementor/elementor-skin.php' );
if ( ! empty( $fdir ) ) {
	require_once $fdir;
}
