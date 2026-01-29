<?php
/**
 * The template to display default site footer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$alliance_footer_scheme = alliance_get_theme_option( 'footer_scheme' );
if ( ! empty( $alliance_footer_scheme ) && ! alliance_is_inherit( $alliance_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $alliance_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/footer-socials' ) );

	// Menu
	get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/footer-menu' ) );

	// Copyright area
	get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer>
