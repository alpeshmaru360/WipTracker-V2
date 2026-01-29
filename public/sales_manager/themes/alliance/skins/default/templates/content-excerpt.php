<?php
/**
 * The default template to display the content
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

$alliance_expanded    = ! alliance_sidebar_present() && alliance_get_theme_option( 'expand_content' ) == 'expand';

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
				. ' post_layout_excerpt post_layout_excerpt_' . esc_attr( $alliance_columns )
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

	$alliance_hover      = ! empty( $alliance_template_args['hover'] ) && ! alliance_is_inherit( $alliance_template_args['hover'] )
								? $alliance_template_args['hover']
								: alliance_get_theme_option( 'image_hover' );

	$alliance_components = ! empty( $alliance_template_args['meta_parts'] )
						? ( is_array( $alliance_template_args['meta_parts'] )
							? $alliance_template_args['meta_parts']
							: array_map( 'trim', explode( ',', $alliance_template_args['meta_parts'] ) )
							)
						: alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) );
															

	if ( in_array( $alliance_post_format, array('audio', 'video') ) ) {
		// Post info inside featured image
		$alliance_post_info = '';
		$alliance_show_title = get_the_title() != '';
		$alliance_show_meta  = count( $alliance_components ) > 0;

		if ( $alliance_show_title ) {
			ob_start();
			?>
			<div class="post_info">
				<?php
				// Categories
				if ( apply_filters( 'alliance_filter_show_blog_categories', $alliance_show_meta && in_array( 'categories', $alliance_components ), array( 'categories' ), 'excerpt' ) ) {
					do_action( 'alliance_action_before_post_category' );
					?>
					<div class="post_category"><?php
						alliance_show_post_meta( apply_filters(
															'alliance_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																),
															'hover_' . $alliance_hover, 1
															)
											);
					?></div>
					<?php
					$alliance_components = alliance_array_delete_by_value( $alliance_components, 'categories' );
					do_action( 'alliance_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'alliance_filter_show_blog_title', true, 'excerpt' ) ) {
					do_action( 'alliance_action_before_post_title' );
					if ( empty( $alliance_template_args['no_links'] ) ) {
						the_title( sprintf( '<h2 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
					} else {
						the_title( '<h2 class="post_title entry-title">', '</h2>' );
					}
					do_action( 'alliance_action_after_post_title' );
				}

				// Post meta
				if ( apply_filters( 'alliance_filter_show_blog_meta', $alliance_show_meta, $alliance_components, 'excerpt' ) ) {
					if ( count( $alliance_components ) > 0 ) {
						do_action( 'alliance_action_before_post_meta' );
						alliance_show_post_meta(
							apply_filters(
								'alliance_filter_post_meta_args', array(
									'components' => join( ',', $alliance_components ),
									'seo'        => false,
									'echo'       => true,
								), 'excerpt', 1
							)
						);
						do_action( 'alliance_action_after_post_meta' );
					}
				}
				?>
			</div>
			<?php
			$alliance_post_info = ob_get_contents();
			ob_end_clean();
		}

		// Featured image
		alliance_show_post_featured( apply_filters( 'alliance_filter_args_featured', 
			array(
				'no_links'      => ! empty( $alliance_template_args['no_links'] ),
				'hover'      	=> 'none',
				'thumb_size'    => ! empty( $alliance_template_args['thumb_size'] )
									? $alliance_template_args['thumb_size']
									: alliance_get_thumb_size(
										alliance_is_blog_style_use_masonry( $alliance_blog_style[0] )
											? (	strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false || $alliance_columns < 3
												? 'masonry-big'
												: 'masonry'
												)
											: (	strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false || $alliance_columns < 3
												? 'huge'
												: 'med'
												)
									),
				'post_info'     => apply_filters('alliance_filter_post_info', $alliance_post_info, $alliance_template_args, 'excerpt' ),
				'thumb_bg'		=> true
			),
			'content-excerpt',
			$alliance_template_args
		) );
	} else {
		// Featured image	
		alliance_show_post_featured( apply_filters( 'alliance_filter_args_featured',
			array(
				'no_links'   => ! empty( $alliance_template_args['no_links'] ),
				'hover'      => $alliance_hover,
				'meta_parts' => $alliance_components,
				'thumb_size' => ! empty( $alliance_template_args['thumb_size'] )
									? $alliance_template_args['thumb_size']
									: alliance_get_thumb_size( strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false
										? 'full'
										: 'huge'
										),
			),
			'content-excerpt',
			$alliance_template_args
		) );

		// Title and post meta
		$alliance_show_title = get_the_title() != '';
		$alliance_show_meta  = count( $alliance_components ) > 0 && ! in_array( $alliance_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

		if ( $alliance_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'alliance_filter_show_blog_categories', $alliance_show_meta && in_array( 'categories', $alliance_components ), array( 'categories' ), 'excerpt' ) ) {
					do_action( 'alliance_action_before_post_category' );
					?>
					<div class="post_category"><?php
						alliance_show_post_meta( apply_filters(
															'alliance_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																),
															'hover_' . $alliance_hover, 1
															)
											);
					?></div>
					<?php
					$alliance_components = alliance_array_delete_by_value( $alliance_components, 'categories' );
					do_action( 'alliance_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'alliance_filter_show_blog_title', true, 'excerpt' ) ) {
					do_action( 'alliance_action_before_post_title' );
					if ( empty( $alliance_template_args['no_links'] ) ) {
						the_title( sprintf( '<h2 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
					} else {
						the_title( '<h2 class="post_title entry-title">', '</h2>' );
					}
					do_action( 'alliance_action_after_post_title' );
				}
				?>
			</div>
			<?php
		}

		// Post content
		$alliance_show_excerpt = in_array( $alliance_post_format, array('quote', 'aside', 'status', 'link') ) ? true : (isset( $alliance_template_args['hide_excerpt'] ) ? (int) $alliance_template_args['hide_excerpt'] == 0 : (int) alliance_get_theme_option( 'excerpt_length' ) > 0);
		if ( apply_filters( 'alliance_filter_show_blog_excerpt', $alliance_show_excerpt, 'excerpt' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				if ( alliance_get_theme_option( 'blog_content' ) == 'fullpost' ) {
					// Post content area
					?>
					<div class="post_content_inner">
						<?php
						do_action( 'alliance_action_before_full_post_content' );
						the_content( '' );
						do_action( 'alliance_action_after_full_post_content' );
						?>
					</div>
					<?php
					// Inner pages
					wp_link_pages(
						array(
							'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'alliance' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
							'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'alliance' ) . ' </span>%',
							'separator'   => '<span class="screen-reader-text">, </span>',
						)
					);
				} else {
					// Post content area
					alliance_show_post_content( $alliance_template_args, '<div class="post_content_inner">', '</div>' );
				}
				?>
			</div>
			<?php
		}
			
		// Post meta
		if ( apply_filters( 'alliance_filter_show_blog_meta', $alliance_show_meta, $alliance_components, 'excerpt' ) ) {
			if ( count( $alliance_components ) > 0 ) {
				do_action( 'alliance_action_before_post_meta' );
				alliance_show_post_meta(
					apply_filters(
						'alliance_filter_post_meta_args', array(
							'components' => join( ',', $alliance_components ),
							'seo'        => false,
							'echo'       => true,
						), 'excerpt', 1
					)
				);
				do_action( 'alliance_action_after_post_meta' );
			}
		}

		// More button
		if ( apply_filters( 'alliance_filter_show_blog_readmore',  ! isset( $alliance_template_args['more_button'] ) || ! empty( $alliance_template_args['more_button'] ), 'excerpt' ) && !empty($args['more_text']) ) {
			if ( empty( $alliance_template_args['no_links'] ) ) {
				do_action( 'alliance_action_before_post_readmore' );
				if ( alliance_get_theme_option( 'blog_content' ) != 'fullpost' ) {
					alliance_show_post_more_link( $alliance_template_args, '<p>', '</p>' );
				} else {
					alliance_show_post_comments_link( $alliance_template_args, '<p>', '</p>' );
				}
				do_action( 'alliance_action_after_post_readmore' );
			}
		}
	}
	?>
</article></div><?php 