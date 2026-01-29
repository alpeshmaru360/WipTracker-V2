<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

$alliance_args = get_query_var( 'alliance_logo_args' );

// Site logo
$alliance_logo_type   = isset( $alliance_args['type'] ) ? $alliance_args['type'] : '';
$alliance_logo_image  = alliance_get_logo_image( $alliance_logo_type );
$alliance_logo_text   = alliance_is_on( alliance_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$alliance_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $alliance_logo_image['logo'] ) || ! empty( $alliance_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/login' ) ); ?>">
		<?php
		if ( ! empty( $alliance_logo_image['logo'] ) ) {
			if ( empty( $alliance_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric( $alliance_logo_image['logo'] ) && (int) $alliance_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$alliance_attr = alliance_getimagesize( $alliance_logo_image['logo'] );
				echo '<img src="' . esc_url( $alliance_logo_image['logo'] ) . '"'
						. ( ! empty( $alliance_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $alliance_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $alliance_logo_text ) . '"'
						. ( ! empty( $alliance_attr[3] ) ? ' ' . wp_kses_data( $alliance_attr[3] ) : '' )
						. '>';
			}
		} else {
			alliance_show_layout( alliance_prepare_macros( $alliance_logo_text ), '<span class="logo_text">', '</span>' );
			alliance_show_layout( alliance_prepare_macros( $alliance_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
