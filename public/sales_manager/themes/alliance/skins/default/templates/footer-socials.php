<?php
/**
 * The template to display the socials in the footer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.10
 */


// Socials
if ( alliance_is_on( alliance_get_theme_option( 'socials_in_footer' ) ) ) {
	$alliance_output = alliance_get_socials_links();
	if ( '' != $alliance_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php alliance_show_layout( $alliance_output ); ?>
			</div>
		</div>
		<?php
	}
}
