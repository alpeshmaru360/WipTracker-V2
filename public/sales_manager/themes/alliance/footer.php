<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

							do_action( 'alliance_action_page_content_end_text' );
							
							// Widgets area below the content
							alliance_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'alliance_action_page_content_end' );
							?>
						</div>
						<?php
						
						do_action( 'alliance_action_after_page_content' );

						// Show main sidebar
						get_sidebar();

						do_action( 'alliance_action_content_wrap_end' );
						?>
					</div>
					<?php

					do_action( 'alliance_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$alliance_body_style = alliance_get_theme_option( 'body_style' );
					$alliance_widgets_name = alliance_get_theme_option( 'widgets_below_page' );
					$alliance_show_widgets = ! alliance_is_off( $alliance_widgets_name ) && is_active_sidebar( $alliance_widgets_name );
					$alliance_show_related = alliance_is_single() && alliance_get_theme_option( 'related_position' ) == 'below_page';
					if ( $alliance_show_widgets || $alliance_show_related ) {
						if ( 'fullscreen' != $alliance_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $alliance_show_related ) {
							do_action( 'alliance_action_related_posts' );
						}

						// Widgets area below page content
						if ( $alliance_show_widgets ) {
							alliance_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $alliance_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'alliance_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'alliance_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! alliance_is_singular( 'post' ) && ! alliance_is_singular( 'attachment' ) ) || ! in_array ( alliance_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="alliance_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'alliance_action_before_footer' );

				// Footer
				$alliance_footer_type = alliance_get_theme_option( 'footer_type' );
				if ( 'custom' == $alliance_footer_type && ! alliance_is_layouts_available() ) {
					$alliance_footer_type = 'default';
				}
				get_template_part( apply_filters( 'alliance_filter_get_template_part', "templates/footer-" . sanitize_file_name( $alliance_footer_type ) ) );

				do_action( 'alliance_action_after_footer' );

			}
			?>

			<?php do_action( 'alliance_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'alliance_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'alliance_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>