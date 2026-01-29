<?php
/**
 * The template to display the widgets area in the header
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

// Header sidebar
$alliance_header_name    = alliance_get_theme_option( 'header_widgets' );
$alliance_header_present = ! alliance_is_off( $alliance_header_name ) && is_active_sidebar( $alliance_header_name );
if ( $alliance_header_present ) {
	alliance_storage_set( 'current_sidebar', 'header' );
	$alliance_header_wide = alliance_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $alliance_header_name ) ) {
		dynamic_sidebar( $alliance_header_name );
	}
	$alliance_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $alliance_widgets_output ) ) {
		$alliance_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $alliance_widgets_output );
		$alliance_need_columns   = strpos( $alliance_widgets_output, 'columns_wrap' ) === false;
		if ( $alliance_need_columns ) {
			$alliance_columns = max( 0, (int) alliance_get_theme_option( 'header_columns' ) );
			if ( 0 == $alliance_columns ) {
				$alliance_columns = min( 6, max( 1, alliance_tags_count( $alliance_widgets_output, 'aside' ) ) );
			}
			if ( $alliance_columns > 1 ) {
				$alliance_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $alliance_columns ) . ' widget', $alliance_widgets_output );
			} else {
				$alliance_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $alliance_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'alliance_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $alliance_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $alliance_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'alliance_action_before_sidebar', 'header' );
				alliance_show_layout( $alliance_widgets_output );
				do_action( 'alliance_action_after_sidebar', 'header' );
				if ( $alliance_need_columns ) {
					?>
					</div>
					<?php
				}
				if ( ! $alliance_header_wide ) {
					?>
					</div>
					<?php
				}
				?>
			</div>
			<?php do_action( 'alliance_action_after_sidebar_wrap', 'header' ); ?>
		</div>
		<?php
	}
}
