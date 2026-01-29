<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.06
 */

$alliance_header_css   = '';
$alliance_header_image = get_header_image();
$alliance_header_video = alliance_get_header_video();
if ( ! empty( $alliance_header_image ) && alliance_trx_addons_featured_image_override( is_singular() || alliance_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$alliance_header_image = alliance_get_current_mode_image( $alliance_header_image );
}

$alliance_header_id = alliance_get_custom_header_id();
$alliance_header_meta = alliance_get_custom_layout_meta( $alliance_header_id );
if ( ! empty( $alliance_header_meta['margin'] ) ) {
	alliance_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( alliance_prepare_css_value( $alliance_header_meta['margin'] ) ) ) );
	alliance_storage_set( 'custom_header_margin', alliance_prepare_css_value( $alliance_header_meta['margin'] ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $alliance_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $alliance_header_id ) ) ); ?>
				<?php
				echo ! empty( $alliance_header_image ) || ! empty( $alliance_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $alliance_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $alliance_header_image ) {
					echo ' ' . esc_attr( alliance_add_inline_css_class( 'background-image: url(' . esc_url( $alliance_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( alliance_is_on( alliance_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight alliance-full-height';
				}
				$alliance_header_scheme = alliance_get_theme_option( 'header_scheme' );
				if ( ! empty( $alliance_header_scheme ) && ! alliance_is_inherit( $alliance_header_scheme  ) ) {
					echo ' scheme_' . esc_attr( $alliance_header_scheme );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $alliance_header_video ) ) {
		get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'alliance_action_show_layout', $alliance_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
