<?php
/**
 * The Portfolio template to display the content
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
?>"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $alliance_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $alliance_columns )
		. ( 'portfolio' != $alliance_blog_style[0] ? ' ' . esc_attr( $alliance_blog_style[0] )  . '_' . esc_attr( $alliance_columns ) : '' )
	);
	alliance_add_blog_animation( $alliance_template_args );
	?>
>
<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	$alliance_hover   = ! empty( $alliance_template_args['hover'] ) && ! alliance_is_inherit( $alliance_template_args['hover'] )
								? $alliance_template_args['hover']
								: alliance_get_theme_option( 'image_hover' );

	if ( 'dots' == $alliance_hover ) {
		$alliance_post_link = empty( $alliance_template_args['no_links'] )
								? ( ! empty( $alliance_template_args['link'] )
									? $alliance_template_args['link']
									: get_permalink()
									)
								: '';
		$alliance_target    = ! empty( $alliance_post_link ) && false === strpos( $alliance_post_link, home_url() )
								? ' target="_blank" rel="nofollow"'
								: '';
	}
	
	// Meta parts
	$alliance_components = ! empty( $alliance_template_args['meta_parts'] )
								? ( is_array( $alliance_template_args['meta_parts'] )
									? $alliance_template_args['meta_parts']
									: explode( ',', $alliance_template_args['meta_parts'] )
									)
								: alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) );

	// Featured image
	alliance_show_post_featured( apply_filters( 'alliance_filter_args_featured', 
		array(
			'hover'         => $alliance_hover,
			'no_links'      => ! empty( $alliance_template_args['no_links'] ),
			'thumb_size'    => ! empty( $alliance_template_args['thumb_size'] )
								? $alliance_template_args['thumb_size']
								: alliance_get_thumb_size(
									alliance_is_blog_style_use_masonry( $alliance_blog_style[0] )
										? (	strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false || $alliance_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( alliance_get_theme_option( 'body_style' ), 'full' ) !== false || $alliance_columns < 3
											? 'big'
											: 'med'
											)
								),
			'show_no_image' => true,
			'meta_parts'    => $alliance_components,
			'class'         => in_array( $alliance_hover, apply_filters( 'alliance_filter_add_info_to_hovers', array( 'dots' ) ) )
										? 'hover_with_info'
										: '',
			'post_info'     => in_array( $alliance_hover, apply_filters( 'alliance_filter_add_info_to_hovers', array( 'dots' ) ) )
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $alliance_post_link )
												? '<a href="' . esc_url( $alliance_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $alliance_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
		),
		'content-portfolio',
		$alliance_template_args
	) );
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!