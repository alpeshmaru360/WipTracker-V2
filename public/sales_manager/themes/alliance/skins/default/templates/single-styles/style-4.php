<?php
/**
 * The "Style 4" template to display the post header of the single post or attachment:
 * featured image and title placed in the post header
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.75.0
 */

if ( apply_filters( 'alliance_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
	$alliance_post_format = str_replace( 'post-format-', '', get_post_format() );
	$post_meta = in_array( $alliance_post_format, array( 'video' ) ) ? get_post_meta( get_the_ID(), 'trx_addons_options', true ) : false;
	$video_autoplay = ! empty( $post_meta['video_autoplay'] ) 
						&& ! empty( $post_meta['video_list'] ) 
						&& is_array( $post_meta['video_list'] ) 
						&& count( $post_meta['video_list'] ) == 1
						&& ( ! empty( $post_meta['video_list'][0]['video_url'] ) || ! empty( $post_meta['video_list'][0]['video_embed'] ) );

	// Post title and meta
	ob_start();
	alliance_show_post_title_and_meta( array(
										'share_type'    => 'list',
										'author_avatar' => false,
										'show_labels'   => true,
										'add_spaces'    => true,
										)
									);
	$alliance_post_header = ob_get_contents();
	ob_end_clean();

	// Featured image
	ob_start();
	alliance_show_post_featured_image( array(
		'thumb_bg'  => false,
		'popup'     => false,
		'class_avg' => $video_autoplay
							? 'with_video with_video_autoplay'	// 'with_thumb' is removed
							: '',
		'autoplay'  => $video_autoplay,
		'post_meta' => $post_meta
	) );
	$alliance_post_header .= ob_get_contents();
	ob_end_clean();

	$alliance_with_featured_image = alliance_is_with_featured_image( $alliance_post_header );

	if ( strpos( $alliance_post_header, 'post_featured' ) !== false
		|| strpos( $alliance_post_header, 'post_title' ) !== false
		|| strpos( $alliance_post_header, 'post_meta' ) !== false
	) {
		?>
		<div class="post_header_wrap post_header_wrap_in_header post_header_wrap_style_<?php
			echo esc_attr( alliance_get_theme_option( 'single_style' ) );
			if ( $alliance_with_featured_image ) {
				echo ' with_featured_image';
			}
		?>">
			<div class="content_wrap">
				<?php
				do_action( 'alliance_action_before_post_header' );
				alliance_show_layout( $alliance_post_header );
				do_action( 'alliance_action_after_post_header' );
				?>
			</div>
		</div>
		<?php
	}
}
