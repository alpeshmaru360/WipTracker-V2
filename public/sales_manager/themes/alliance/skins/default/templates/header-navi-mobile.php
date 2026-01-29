<?php
/**
 * The template to show mobile menu
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */
?>
<div class="menu_mobile menu_mobile_<?php echo esc_attr( alliance_get_theme_option( 'menu_mobile_fullscreen' ) > 0 ? 'fullscreen' : 'narrow' );
echo esc_attr( in_array( alliance_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) && alliance_get_theme_option( 'menu_side_open' ) > 0 ? ' is_opened' : '' );
$alliance_menu_scheme = alliance_get_theme_option( 'menu_scheme' );
$alliance_header_scheme = alliance_get_theme_option( 'header_scheme' );
if ( ! empty( $alliance_menu_scheme ) && ! alliance_is_inherit( $alliance_menu_scheme  ) ) {
	echo ' scheme_' . esc_attr( $alliance_menu_scheme );
} elseif ( ! empty( $alliance_header_scheme ) && ! alliance_is_inherit( $alliance_header_scheme ) ) {
	echo ' scheme_' . esc_attr( $alliance_header_scheme );
}
?>">
	<div class="menu_mobile_inner">
		<div class="menu_mobile_top_panel">
			<a class="menu_mobile_close theme_button_close" tabindex="0"><span class="theme_button_close_icon"></span></a>
			<?php

			// Logo
			set_query_var( 'alliance_logo_args', array( 'type' => 'side' ) );
			get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/header-logo' ) );
			set_query_var( 'alliance_logo_args', array() ); 
		?></div><?php

		// Mobile menu
		$alliance_menu_mobile = alliance_get_nav_menu( 'menu_mobile' );
		if ( empty( $alliance_menu_mobile ) ) {
			$alliance_menu_mobile = apply_filters( 'alliance_filter_get_mobile_menu', '' );
			if ( empty( $alliance_menu_mobile ) ) {
				$alliance_menu_mobile = alliance_get_nav_menu( 'menu_main' );
				if ( empty( $alliance_menu_mobile ) ) {
					$alliance_menu_mobile = alliance_get_nav_menu();
				}
			}
		}
		if ( ! empty( $alliance_menu_mobile ) ) {
			// Change attribute 'id' - add prefix 'mobile-' to prevent duplicate id on the page
			$alliance_menu_mobile = preg_replace( '/([\s]*id=")/', '${1}mobile-', $alliance_menu_mobile );
			// Change main menu classes
			$alliance_menu_mobile = str_replace(
				array( 'menu_main',   'sc_layouts_menu_nav', 'sc_layouts_menu ' ),	// , 'sc_layouts_hide_on_mobile', 'hide_on_mobile'
				array( 'menu_mobile', '',                    ' ' ),					// , '',                          ''
				$alliance_menu_mobile
			);
			// Wrap menu to the <nav> if not present
			if ( strpos( $alliance_menu_mobile, '<nav ' ) !== 0 ) {	// condition !== false is not allowed, because menu can contain inner <nav> elements (in the submenu layouts)
				$alliance_menu_mobile = sprintf( '<nav class="menu_mobile_nav_area" itemscope="itemscope" itemtype="%1$s//schema.org/SiteNavigationElement">%2$s</nav>', esc_attr( alliance_get_protocol( true ) ), $alliance_menu_mobile );
			}
			// Show menu
			alliance_show_layout( apply_filters( 'alliance_filter_menu_mobile_layout', $alliance_menu_mobile ) );
		}

		// Search field
		if ( alliance_get_theme_option( 'menu_mobile_search' ) > 0 ) {
			do_action(
				'alliance_action_search',
				array(
					'style' => 'normal',
					'class' => 'search_mobile',
					'ajax'  => false
				)
			);
		}

		// Social icons
		if ( alliance_get_theme_option( 'menu_mobile_socials' ) > 0 ) {
			alliance_show_layout( alliance_get_socials_links(), '<div class="socials_mobile">', '</div>' );
		}
		?>
	</div>
</div>
