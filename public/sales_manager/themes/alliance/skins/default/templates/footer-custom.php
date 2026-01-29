<?php
/**
 * The template to display default site footer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.10
 */

$alliance_footer_id = alliance_get_custom_footer_id();
$alliance_footer_meta = alliance_get_custom_layout_meta( $alliance_footer_id );
if ( ! empty( $alliance_footer_meta['margin'] ) ) {
	alliance_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( alliance_prepare_css_value( $alliance_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $alliance_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $alliance_footer_id ) ) ); ?>
						<?php
						$alliance_footer_scheme = alliance_get_theme_option( 'footer_scheme' );
						if ( ! empty( $alliance_footer_scheme ) && ! alliance_is_inherit( $alliance_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $alliance_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'alliance_action_show_layout', $alliance_footer_id );
	?>
</footer>
