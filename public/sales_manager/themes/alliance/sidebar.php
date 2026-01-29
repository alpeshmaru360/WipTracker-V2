<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

if ( alliance_sidebar_present() ) {
	
	$alliance_sidebar_type = alliance_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $alliance_sidebar_type && ! alliance_is_layouts_available() ) {
		$alliance_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $alliance_sidebar_type ) {
		// Default sidebar with widgets
		$alliance_sidebar_name = alliance_get_theme_option( 'sidebar_widgets' );
		alliance_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $alliance_sidebar_name ) ) {
			dynamic_sidebar( $alliance_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$alliance_sidebar_id = alliance_get_custom_sidebar_id();
		do_action( 'alliance_action_show_layout', $alliance_sidebar_id );
	}
	$alliance_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $alliance_out ) ) {
		$alliance_sidebar_position    = alliance_get_theme_option( 'sidebar_position' );
		$alliance_sidebar_position_ss = alliance_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $alliance_sidebar_position );
			echo ' sidebar_' . esc_attr( $alliance_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $alliance_sidebar_type );

			$alliance_sidebar_scheme = apply_filters( 'alliance_filter_sidebar_scheme', alliance_get_theme_option( 'sidebar_scheme' ) );
			if ( ! empty( $alliance_sidebar_scheme ) && ! alliance_is_inherit( $alliance_sidebar_scheme ) && 'custom' != $alliance_sidebar_type ) {
				echo ' scheme_' . esc_attr( $alliance_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="alliance_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'alliance_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $alliance_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$alliance_title = apply_filters( 'alliance_filter_sidebar_control_title', 'float' == $alliance_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'alliance' ) : '' );
				$alliance_text  = apply_filters( 'alliance_filter_sidebar_control_text', 'above' == $alliance_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'alliance' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $alliance_title ); ?>"><?php echo esc_html( $alliance_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'alliance_action_before_sidebar', 'sidebar' );
				alliance_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $alliance_out ) );
				do_action( 'alliance_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'alliance_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}
