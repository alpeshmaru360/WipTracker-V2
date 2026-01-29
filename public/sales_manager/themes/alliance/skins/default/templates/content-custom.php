<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.50
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
$alliance_blog_id       = alliance_get_custom_blog_id( join( '_', $alliance_blog_style ) );
$alliance_blog_style[0] = str_replace( 'blog-custom-', '', $alliance_blog_style[0] );
$alliance_expanded      = ! alliance_sidebar_present() && alliance_get_theme_option( 'expand_content' ) == 'expand';
$alliance_components    = ! empty( $alliance_template_args['meta_parts'] )
							? ( is_array( $alliance_template_args['meta_parts'] )
								? join( ',', $alliance_template_args['meta_parts'] )
								: $alliance_template_args['meta_parts']
								)
							: alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) );
$alliance_post_format   = get_post_format();
$alliance_post_format   = empty( $alliance_post_format ) ? 'standard' : str_replace( 'post-format-', '', $alliance_post_format );

$alliance_blog_meta     = alliance_get_custom_layout_meta( $alliance_blog_id );
$alliance_custom_style  = ! empty( $alliance_blog_meta['scripts_required'] ) ? $alliance_blog_meta['scripts_required'] : 'none';

if ( ! empty( $alliance_template_args['slider'] ) || $alliance_columns > 1 || ! alliance_is_off( $alliance_custom_style ) ) {
	?><div class="<?php
		if ( ! empty( $alliance_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( alliance_is_off( $alliance_custom_style )
							? $alliance_columns_class
							: sprintf( '%1$s_item %1$s_item-1_%2$d', $alliance_custom_style, $alliance_columns )
							);
		}
	?>">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $alliance_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $alliance_columns )
					. ' post_layout_' . esc_attr( $alliance_blog_style[0] )
					. ' post_layout_' . esc_attr( $alliance_blog_style[0] ) . '_' . esc_attr( $alliance_columns )
					. ( ! alliance_is_off( $alliance_custom_style )
						? ' post_layout_' . esc_attr( $alliance_custom_style )
							. ' post_layout_' . esc_attr( $alliance_custom_style ) . '_' . esc_attr( $alliance_columns )
						: ''
						)
		);
	alliance_add_blog_animation( $alliance_template_args );
	?>
>
	<?php
	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}
	// Custom layout
	do_action( 'alliance_action_show_layout', $alliance_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $alliance_template_args['slider'] ) || $alliance_columns > 1 || ! alliance_is_off( $alliance_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
