<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

$alliance_template_args = get_query_var( 'alliance_template_args' );

if ( is_array( $alliance_template_args ) ) {
	$alliance_columns       = empty( $alliance_template_args['columns'] ) ? 2 : max( 1, $alliance_template_args['columns'] );
	$alliance_blog_style    = array( $alliance_template_args['type'], $alliance_columns );
	$alliance_columns_class = alliance_get_column_class( 1, $alliance_columns, ! empty( $alliance_template_args['columns_tablet']) ? $alliance_template_args['columns_tablet'] : '', ! empty($alliance_template_args['columns_mobile']) ? $alliance_template_args['columns_mobile'] : '' );
} else {
	$alliance_template_args = array();
	$alliance_blog_style    = explode( '_', alliance_get_theme_option( 'blog_style' ) );
	$alliance_columns       = empty( $alliance_blog_style[1] ) ? 2 : max( 1, $alliance_blog_style[1] );
	$alliance_columns_class = alliance_get_column_class( 1, $alliance_columns );
}

$alliance_expanded   = ! alliance_sidebar_present() && alliance_get_theme_option( 'expand_content' ) == 'expand';

$alliance_post_format = get_post_format();
$alliance_post_format = empty( $alliance_post_format ) ? 'standard' : str_replace( 'post-format-', '', $alliance_post_format );

?><div class="<?php
	if ( ! empty( $alliance_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( alliance_is_blog_style_use_masonry( $alliance_blog_style[0] )
			? 'masonry_item masonry_item-1_' . esc_attr( $alliance_columns )
			: esc_attr( $alliance_columns_class )
			);
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $alliance_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $alliance_columns )
				. ' post_layout_' . esc_attr( $alliance_blog_style[0] )
				. ' post_layout_' . esc_attr( $alliance_blog_style[0] ) . '_' . esc_attr( $alliance_columns )
	);
	alliance_add_blog_animation( $alliance_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	$alliance_hover      = ! empty( $alliance_template_args['hover'] ) && ! alliance_is_inherit( $alliance_template_args['hover'] )
							? $alliance_template_args['hover']
							: alliance_get_theme_option( 'image_hover' );

	$alliance_components = ! empty( $alliance_template_args['meta_parts'] )
							? ( is_array( $alliance_template_args['meta_parts'] )
								? $alliance_template_args['meta_parts']
								: explode( ',', $alliance_template_args['meta_parts'] )
								)
							: alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) );

	alliance_show_post_featured( apply_filters( 'alliance_filter_args_featured',
		array(
			'thumb_size' => ! empty( $alliance_template_args['thumb_size'] )
								? $alliance_template_args['thumb_size']
								: alliance_get_thumb_size(
									'classic' == $alliance_blog_style[0]
											? ( strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false
													? ( $alliance_columns > 2 ? 'big' : 'huge' )
													: ( $alliance_columns > 2
														? ( $alliance_expanded ? 'big' : 'small' )
														: ( $alliance_expanded ? 'big' : 'huge' )
														)
												)
											: ( strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false
													? ( $alliance_columns > 2 ? 'masonry-big' : 'full' )
													: ( $alliance_columns <= 2 && $alliance_expanded ? 'masonry-big' : 'masonry' )
												)
								),
			'hover'      => $alliance_hover,
			'meta_parts' => $alliance_components,
			'no_links'   => ! empty( $alliance_template_args['no_links'] ),
		),
		'content-classic',
		$alliance_template_args
	) );

	?><div class="post_content_wrap"><?php

	// Title and post meta
	$alliance_show_title = get_the_title() != '';
	$alliance_show_meta  = count( $alliance_components ) > 0 && ! in_array( $alliance_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $alliance_show_title ) {
		?><div class="post_header entry-header"><?php
			// Categories
			if ( apply_filters( 'alliance_filter_show_blog_categories', $alliance_show_meta && in_array( 'categories', $alliance_components ), array( 'categories' ), 'classic' ) ) {
				do_action( 'alliance_action_before_post_category' );
				
				alliance_show_post_meta( apply_filters(
													'alliance_filter_post_meta_args',
													array(
														'components' => 'categories,date',
														'seo'        => false,
														'echo'       => true,
														),
													'hover_' . $alliance_hover, 1
													)
									);
			
				$alliance_components = alliance_array_delete_by_value( $alliance_components, 'categories' );
				$alliance_components = alliance_array_delete_by_value( $alliance_components, 'date' );
				do_action( 'alliance_action_after_post_category' );
			}
			// Post title
			if ( apply_filters( 'alliance_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'alliance_action_before_post_title' );
				if ( empty( $alliance_template_args['no_links'] ) ) {
					the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
				} else {
					the_title( '<h3 class="post_title entry-title">', '</h3>' );
				}
				do_action( 'alliance_action_after_post_title' );
			}
		?></div><?php
	}

	// Post content
	ob_start();
	$alliance_show_excerpt = in_array( $alliance_post_format, array('quote', 'aside', 'status', 'link') ) ? true : (isset( $alliance_template_args['hide_excerpt'] ) ? (int) $alliance_template_args['hide_excerpt'] == 0 : (int) alliance_get_theme_option( 'excerpt_length' ) > 0);
	if ( apply_filters( 'alliance_filter_show_blog_excerpt', $alliance_show_excerpt, 'classic' ) ) {
		alliance_show_post_content( $alliance_template_args, '<div class="post_content_inner">', '</div>' );
	}
	$alliance_content = ob_get_contents();
	ob_end_clean();

	alliance_show_layout( $alliance_content, '<div class="post_content entry-content">', '</div>' );

	// Post meta
	if ( apply_filters( 'alliance_filter_show_blog_meta', $alliance_show_meta, $alliance_components, 'classic' ) ) {
		if ( count( $alliance_components ) > 0 ) {
			do_action( 'alliance_action_before_post_meta' );
			alliance_show_post_meta(
				apply_filters(
					'alliance_filter_post_meta_args', array(
						'components' => join( ',', $alliance_components ),
						'seo'        => false,
						'echo'       => true,
					), $alliance_blog_style[0], $alliance_columns
				)
			);
			do_action( 'alliance_action_after_post_meta' );
		}
	}
		
	// More button
	if ( apply_filters( 'alliance_filter_show_blog_readmore', ( ! $alliance_show_title || ! empty( $alliance_template_args['more_button'] ) ) && ! empty( $args['more_text'] ), 'classic' ) ) {
		if ( empty( $alliance_template_args['no_links'] ) ) {
			do_action( 'alliance_action_before_post_readmore' );
			alliance_show_post_more_link( $alliance_template_args, '<p>', '</p>' );
			do_action( 'alliance_action_after_post_readmore' );
		}
	}
	?>
	</div>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
