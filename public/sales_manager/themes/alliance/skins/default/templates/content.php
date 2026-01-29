<?php
/**
 * The default template to display the content of the single post or attachment
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */
?>
<article id="post-<?php the_ID(); ?>"
	<?php
	post_class( 'post_item_single'
		. ' post_type_' . esc_attr( get_post_type() ) 
		. ' post_format_' . esc_attr( str_replace( 'post-format-', '', get_post_format() ) )
	);
	alliance_add_seo_itemprops();
	?>
>
<?php

	do_action( 'alliance_action_before_post_data' );
	alliance_add_seo_snippets();
	do_action( 'alliance_action_after_post_data' );

	do_action( 'alliance_action_before_post_content' );

	// Post content
	$alliance_share_position = alliance_array_get_keys_by_value( alliance_get_theme_option( 'share_position' ) );
	?>
	<div class="post_content post_content_single entry-content<?php
		if ( in_array( 'left', $alliance_share_position ) ) {
			echo ' post_info_vertical_present' . ( in_array( 'top', $alliance_share_position ) ? ' post_info_vertical_hide_on_mobile' : '' );
		}
	?>" itemprop="mainEntityOfPage">
		<?php
		if ( in_array( 'left', $alliance_share_position ) ) {
			?><div class="post_info_vertical<?php
				if ( alliance_get_theme_option( 'share_fixed' ) > 0 ) {
					echo ' post_info_vertical_fixed';
				}
			?>"><?php
				alliance_show_post_meta(
					apply_filters(
						'alliance_filter_post_meta_args',
						array(
							'components'      => 'share',
							'class'           => 'post_share_vertical',
							'share_type'      => 'block',
							'share_direction' => 'vertical',
						),
						'single',
						1
					)
				);
			?></div><?php
		}
		the_content();
		?>
	</div>
	<?php

	do_action( 'alliance_action_after_post_content' );
	
	// Post footer: Tags, likes, share, author, prev/next links and comments
	do_action( 'alliance_action_before_post_footer' );
	?>
	<div class="post_footer post_footer_single entry-footer"><?php
		alliance_show_post_pagination();
		if ( is_single() && ! is_attachment() ) {
			alliance_show_post_footer();
		}	
	?></div>
	<?php
	do_action( 'alliance_action_after_post_footer' );
	?>
</article>
