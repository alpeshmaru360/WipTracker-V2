<?php
/**
 * The template to display the site logo in the footer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.10
 */

// Logo
if ( alliance_is_on( alliance_get_theme_option( 'logo_in_footer' ) ) ) {
	$alliance_logo_image = alliance_get_logo_image( 'footer' );
	$alliance_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $alliance_logo_image['logo'] ) || ! empty( $alliance_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $alliance_logo_image['logo'] ) ) {
					$alliance_attr = alliance_getimagesize( $alliance_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $alliance_logo_image['logo'] ) . '"'
								. ( ! empty( $alliance_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $alliance_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'alliance' ) . '"'
								. ( ! empty( $alliance_attr[3] ) ? ' ' . wp_kses_data( $alliance_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $alliance_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $alliance_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
