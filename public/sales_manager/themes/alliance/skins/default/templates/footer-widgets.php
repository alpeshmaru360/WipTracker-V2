<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.10
 */

// Footer sidebar
$alliance_footer_name    = alliance_get_theme_option( 'footer_widgets' );
$alliance_footer_present = ! alliance_is_off( $alliance_footer_name ) && is_active_sidebar( $alliance_footer_name );
if ( $alliance_footer_present ) {
	alliance_storage_set( 'current_sidebar', 'footer' );
	$alliance_footer_wide = alliance_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $alliance_footer_name ) ) {
		dynamic_sidebar( $alliance_footer_name );
	}
	$alliance_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $alliance_out ) ) {
		$alliance_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $alliance_out );
		$alliance_need_columns = true;   //or check: strpos($alliance_out, 'columns_wrap')===false;
		if ( $alliance_need_columns ) {
			$alliance_columns = max( 0, (int) alliance_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $alliance_columns ) {
				$alliance_columns = min( 4, max( 1, alliance_tags_count( $alliance_out, 'aside' ) ) );
			}
			if ( $alliance_columns > 1 ) {
				$alliance_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $alliance_columns ) . ' widget', $alliance_out );
			} else {
				$alliance_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $alliance_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'alliance_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $alliance_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $alliance_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'alliance_action_before_sidebar', 'footer' );
				alliance_show_layout( $alliance_out );
				do_action( 'alliance_action_after_sidebar', 'footer' );
				if ( $alliance_need_columns ) {
					?>
					</div>
					<?php
				}
				if ( ! $alliance_footer_wide ) {
					?>
					</div>
					<?php
				}
				?>
			</div>
			<?php do_action( 'alliance_action_after_sidebar_wrap', 'footer' ); ?>
		</div>
		<?php
	}
}
