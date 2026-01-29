<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$alliance_copyright_scheme = alliance_get_theme_option( 'copyright_scheme' );
if ( ! empty( $alliance_copyright_scheme ) && ! alliance_is_inherit( $alliance_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $alliance_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$alliance_copyright = alliance_get_theme_option( 'copyright' );
			if ( ! empty( $alliance_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$alliance_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $alliance_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$alliance_copyright = alliance_prepare_macros( $alliance_copyright );
				// Display copyright
				echo wp_kses( nl2br( $alliance_copyright ), 'alliance_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
