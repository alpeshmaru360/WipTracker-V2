<?php
/**
 * The template to display the user's avatar, bio and socials on the Author page
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.71.0
 */
?>

<div class="author_page author vcard" itemprop="author" itemscope="itemscope" itemtype="<?php echo esc_attr( alliance_get_protocol( true ) ); ?>//schema.org/Person">

	<div class="author_avatar" itemprop="image">
		<?php
		$alliance_mult = alliance_get_retina_multiplier();
		echo get_avatar( get_the_author_meta( 'user_email' ), 120 * $alliance_mult );
		?>
	</div>

	<h4 class="author_title" itemprop="name"><span class="fn"><?php the_author(); ?></span></h4>

	<?php
	$alliance_author_description = get_the_author_meta( 'description' );
	if ( ! empty( $alliance_author_description ) ) {
		?>
		<div class="author_bio" itemprop="description"><?php echo wp_kses( wpautop( $alliance_author_description ), 'alliance_kses_content' ); ?></div>
		<?php
	}
	?>

	<div class="author_details">
		<span class="author_posts_total">
			<?php
			$alliance_posts_total = count_user_posts( get_the_author_meta('ID'), 'post' );	// get_the_author_posts() return posts number by post_type from first post in the result
			if ( $alliance_posts_total > 0 ) {
				// Translators: Add the author's posts number to the message
				echo wp_kses( sprintf( _n( '%s article published', '%s articles published', $alliance_posts_total, 'alliance' ),
										'<span class="author_posts_total_value">' . number_format_i18n( $alliance_posts_total ) . '</span>'
								 		),
							'alliance_kses_content'
							);
			} else {
				esc_html_e( 'No posts published.', 'alliance' );
			}
			?>
		</span><?php
			ob_start();
			do_action( 'alliance_action_user_meta', 'author-page' );
			$alliance_socials = ob_get_contents();
			ob_end_clean();
			alliance_show_layout( $alliance_socials,
				'<span class="author_socials"><span class="author_socials_caption">' . esc_html__( 'Follow:', 'alliance' ) . '</span>',
				'</span>'
			);
		?>
	</div>

</div>
