<?php
/**
 * The template to display menu in the footer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.10
 */

// Footer menu
$alliance_menu_footer = alliance_get_nav_menu( 'menu_footer' );
if ( ! empty( $alliance_menu_footer ) ) {
	?>
	<div class="footer_menu_wrap">
		<div class="footer_menu_inner">
			<?php
			alliance_show_layout(
				$alliance_menu_footer,
				'<nav class="menu_footer_nav_area sc_layouts_menu sc_layouts_menu_default"'
					. ' itemscope="itemscope" itemtype="' . esc_attr( alliance_get_protocol( true ) ) . '//schema.org/SiteNavigationElement"'
					. '>',
				'</nav>'
			);
			?>
		</div>
	</div>
	<?php
}
