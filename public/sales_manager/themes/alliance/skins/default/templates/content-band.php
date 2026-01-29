<?php
/**
 * 'Band' template to display the content
 *
 * Used for index/archive/search.
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.71.0
 */

$alliance_template_args = get_query_var( 'alliance_template_args' );
if ( ! is_array( $alliance_template_args ) ) {
	$alliance_template_args = array(
								'type'    => 'band',
								'columns' => 1
								);
}

$alliance_columns       = 1;

$alliance_expanded      = ! alliance_sidebar_present() && alliance_get_theme_option( 'expand_content' ) == 'expand';

$alliance_post_format   = get_post_format();
$alliance_post_format   = empty( $alliance_post_format ) ? 'standard' : str_replace( 'post-format-', '', $alliance_post_format );

if ( is_array( $alliance_template_args ) ) {
	$alliance_columns    = empty( $alliance_template_args['columns'] ) ? 1 : max( 1, $alliance_template_args['columns'] );
	$alliance_blog_style = array( $alliance_template_args['type'], $alliance_columns );
	if ( ! empty( $alliance_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide"><?php
	} elseif ( $alliance_columns > 1 ) {
		$alliance_columns_class = alliance_get_column_class( 1, $alliance_columns, ! empty( $alliance_template_args['columns_tablet']) ? $alliance_template_args['columns_tablet'] : '', ! empty($alliance_template_args['columns_mobile']) ? $alliance_template_args['columns_mobile'] : '' );
		?><div class="<?php echo esc_attr( $alliance_columns_class ); ?>"><?php
	}
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_band post_format_' . esc_attr( $alliance_post_format ) );
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
								: array_map( 'trim', explode( ',', $alliance_template_args['meta_parts'] ) )
								)
							: alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) );
	alliance_show_post_featured( apply_filters( 'alliance_filter_args_featured', 
		array(
			'no_links'   => ! empty( $alliance_template_args['no_links'] ),
			'hover'      => $alliance_hover,
			'meta_parts' => $alliance_components,
			'thumb_bg'   => true,
			'thumb_size' => ! empty( $alliance_template_args['thumb_size'] )
								? $alliance_template_args['thumb_size']
								: alliance_get_thumb_size( 
									in_array( $alliance_post_format, array( 'gallery', 'audio', 'video' ) )
										? ( strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false
											? 'full'
											: ( $alliance_expanded 
												? 'big' 
												: 'med'
												)
											)
										: 'masonry-big'
									)
		),
		'content-band',
		$alliance_template_args
	) );

	?><div class="post_content_wrap"><?php

		// Title and post meta
		$alliance_show_title = get_the_title() != '';
		$alliance_show_meta  = count( $alliance_components ) > 0 && ! in_array( $alliance_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );
		if ( $alliance_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'alliance_filter_show_blog_categories', $alliance_show_meta && in_array( 'categories', $alliance_components ), array( 'categories' ), 'band' ) ) {
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
				if ( apply_filters( 'alliance_filter_show_blog_title', true, 'band' ) ) {
					do_action( 'alliance_action_before_post_title' );
					if ( empty( $alliance_template_args['no_links'] ) ) {
						the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
					} else {
						the_title( '<h3 class="post_title entry-title">', '</h3>' );
					}
					do_action( 'alliance_action_after_post_title' );
				}
				?>
			</div>
			<?php
		}

		// Post content
		if ( ! isset( $alliance_template_args['excerpt_length'] ) && ! in_array( $alliance_post_format, array( 'gallery', 'audio', 'video' ) ) ) {
			$alliance_template_args['excerpt_length'] = 30;
		}
		$alliance_show_excerpt = in_array( $alliance_post_format, array('quote', 'aside', 'status', 'link') ) ? true : (isset( $alliance_template_args['hide_excerpt'] ) ? (int) $alliance_template_args['hide_excerpt'] == 0 : (int) alliance_get_theme_option( 'excerpt_length' ) > 0);
		if ( apply_filters( 'alliance_filter_show_blog_excerpt', $alliance_show_excerpt, 'band' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				// Post content area
				alliance_show_post_content( $alliance_template_args, '<div class="post_content_inner">', '</div>' );
				?>
			</div>
			<?php
		}
		// Post meta
		if ( apply_filters( 'alliance_filter_show_blog_meta', $alliance_show_meta, $alliance_components, 'band' ) ) {
			if ( count( $alliance_components ) > 0 ) {
				do_action( 'alliance_action_before_post_meta' );
				alliance_show_post_meta(
					apply_filters(
						'alliance_filter_post_meta_args', array(
							'components' => join( ',', $alliance_components ),
							'seo'        => false,
							'echo'       => true,
						), 'band', 1
					)
				);
				do_action( 'alliance_action_after_post_meta' );
			}
		}
		// More button
		if ( apply_filters( 'alliance_filter_show_blog_readmore', ( ! $alliance_show_title || ! empty( $alliance_template_args['more_button'] ) ) && ! empty( $args['more_text'] ), 'band' ) ) {
			if ( empty( $alliance_template_args['no_links'] ) ) {
				do_action( 'alliance_action_before_post_readmore' );
				alliance_show_post_more_link( $alliance_template_args, '<p>', '</p>' );
				do_action( 'alliance_action_after_post_readmore' );
			}
		}
		?>
	</div>
</article>
<?php

if ( is_array( $alliance_template_args ) ) {
	if ( ! empty( $alliance_template_args['slider'] ) || $alliance_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
