<?php
/**
 * The template 'Style 1' to displaying related posts
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

$alliance_link        = get_permalink();
$alliance_post_format = get_post_format();
$alliance_post_format = empty( $alliance_post_format ) ? 'standard' : str_replace( 'post-format-', '', $alliance_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $alliance_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	alliance_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'alliance_filter_related_thumb_size', alliance_get_thumb_size( (int) alliance_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
			'post_info'     => '<div class="post_header entry-header">'
									. '<div class="post_categories">' . wp_kses( alliance_get_post_categories( '' ), 'alliance_kses_content' ) . '</div>'
									. '<h6 class="post_title entry-title"><a href="' . esc_url( $alliance_link ) . '">'
										. wp_kses_data( '' == get_the_title() ? esc_html__( 'No title', 'alliance' ) : get_the_title() )
									. '</a></h6>'
									. ( in_array( get_post_type(), array( 'post', 'attachment' ) )
											? '<div class="post_meta"><a href="' . esc_url( $alliance_link ) . '" class="post_meta_item post_date">' . wp_kses_data( alliance_get_date() ) . '</a></div>'
											: '' )
								. '</div>',
		)
	);
	?>
</div>
