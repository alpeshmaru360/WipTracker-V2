<?php
/**
 * The Sticky template to display the sticky posts
 *
 * Used for index/archive
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

$alliance_columns     = max( 1, min( 3, count( get_option( 'sticky_posts' ) ) ) );
$alliance_post_format = get_post_format();
$alliance_post_format = empty( $alliance_post_format ) ? 'standard' : str_replace( 'post-format-', '', $alliance_post_format );

?><div class="column-1_<?php echo esc_attr( $alliance_columns ); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class( 'post_item post_layout_sticky post_format_' . esc_attr( $alliance_post_format ) );
	alliance_add_blog_animation( $alliance_template_args );
	?>
>

	<?php
	if ( is_sticky() && is_home() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	alliance_show_post_featured(
		array(
			'thumb_size' => alliance_get_thumb_size( 1 == $alliance_columns ? 'big' : ( 2 == $alliance_columns ? 'med' : 'avatar' ) ),
		)
	);

	if ( ! in_array( $alliance_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			the_title( sprintf( '<h6 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h6>' );
			// Post meta
			alliance_show_post_meta( apply_filters( 'alliance_filter_post_meta_args', array(), 'sticky', $alliance_columns ) );
			?>
		</div>
		<?php
	}
	?>
</article></div><?php

// div.column-1_X is a inline-block and new lines and spaces after it are forbidden
