<article <?php post_class( 'post_item_single post_item_404' ); ?>>
	<div class="post_content">
		<h1 class="page_title"><?php esc_html_e( '404', 'alliance' ); ?></h1>
		<div class="page_info">
			<h1 class="page_subtitle"><?php esc_html_e( 'Page Not Found', 'alliance' ); ?></h1>
			<p class="page_description">
				<?php echo wp_kses( __( "It looks like nothing was found at this location.", 'alliance' ), 'alliance_kses_content' ); ?>
				<br>
				<?php echo wp_kses( __( "Maybe try a search below?", 'alliance' ), 'alliance_kses_content' ); ?>
			</p>
			<!--<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="go_home theme_button"><?php esc_html_e( 'Homepage', 'alliance' ); ?></a>-->
			<?php 
			ob_start();
			do_action(
				'alliance_action_search',
				array(
					'style' => 'normal',
					'class' => '',
					'ajax'  => false
				)
			);
			$alliance_action_output = ob_get_contents();
			ob_end_clean();
			if ( ! empty( $alliance_action_output ) ) {
				alliance_show_layout( $alliance_action_output );
			}
			?>
		</div>
	</div>
</article>
