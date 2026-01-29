<?php
/**
 * The template to display single post
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

// Full post loading
$full_post_loading          = alliance_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = alliance_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = alliance_get_theme_option( 'posts_navigation_scroll_which_block' );

// Position of the related posts
$alliance_related_position   = alliance_get_theme_option( 'related_position' );

// Type of the prev/next post navigation
$alliance_posts_navigation   = alliance_get_theme_option( 'posts_navigation' );
$alliance_prev_post          = false;
$alliance_prev_post_same_cat = alliance_get_theme_option( 'posts_navigation_scroll_same_cat' );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( alliance_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	alliance_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'alliance_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $alliance_posts_navigation ) {
		$alliance_prev_post = get_previous_post( $alliance_prev_post_same_cat );  // Get post from same category
		if ( ! $alliance_prev_post && $alliance_prev_post_same_cat ) {
			$alliance_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $alliance_prev_post ) {
			$alliance_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $alliance_prev_post ) ) {
		alliance_sc_layouts_showed( 'featured', false );
		alliance_sc_layouts_showed( 'title', false );
		alliance_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $alliance_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/content', 'single-' . alliance_get_theme_option( 'single_style' ) ), 'single-' . alliance_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $alliance_related_position, 'inside' ) === 0 ) {
		$alliance_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'alliance_action_related_posts' );
		$alliance_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $alliance_related_content ) ) {
			$alliance_related_position_inside = max( 0, min( 9, alliance_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $alliance_related_position_inside ) {
				$alliance_related_position_inside = mt_rand( 1, 9 );
			}

			$alliance_p_number         = 0;
			$alliance_related_inserted = false;
			$alliance_in_block         = false;
			$alliance_content_start    = strpos( $alliance_content, '<div class="post_content' );
			$alliance_content_end      = strrpos( $alliance_content, '</div>' );

			for ( $i = max( 0, $alliance_content_start ); $i < min( strlen( $alliance_content ) - 3, $alliance_content_end ); $i++ ) {
				if ( $alliance_content[ $i ] != '<' ) {
					continue;
				}
				if ( $alliance_in_block ) {
					if ( strtolower( substr( $alliance_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$alliance_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $alliance_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $alliance_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$alliance_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $alliance_content[ $i + 1 ] && in_array( $alliance_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$alliance_p_number++;
					if ( $alliance_related_position_inside == $alliance_p_number ) {
						$alliance_related_inserted = true;
						$alliance_content = ( $i > 0 ? substr( $alliance_content, 0, $i ) : '' )
											. $alliance_related_content
											. substr( $alliance_content, $i );
					}
				}
			}
			if ( ! $alliance_related_inserted ) {
				if ( $alliance_content_end > 0 ) {
					$alliance_content = substr( $alliance_content, 0, $alliance_content_end ) . $alliance_related_content . substr( $alliance_content, $alliance_content_end );
				} else {
					$alliance_content .= $alliance_related_content;
				}
			}
		}

		alliance_show_layout( $alliance_content );
	}

	// Comments
	do_action( 'alliance_action_before_comments' );
	comments_template();
	do_action( 'alliance_action_after_comments' );

	// Related posts
	if ( 'below_content' == $alliance_related_position
		&& ( 'scroll' != $alliance_posts_navigation || alliance_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || alliance_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'alliance_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $alliance_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $alliance_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $alliance_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $alliance_prev_post ) ); ?>"
			<?php do_action( 'alliance_action_nav_links_single_scroll_data', $alliance_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();
