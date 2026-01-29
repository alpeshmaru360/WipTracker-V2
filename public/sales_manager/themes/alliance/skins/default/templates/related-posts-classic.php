<?php
/**
 * The template 'Style 2' to displaying related posts
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

$alliance_link        = get_permalink();
$alliance_post_format = get_post_format();
$alliance_post_format = empty( $alliance_post_format ) ? 'standard' : str_replace( 'post-format-', '', $alliance_post_format );
ob_start();
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $alliance_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	alliance_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'alliance_filter_related_thumb_size', alliance_get_thumb_size( (int) alliance_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
		)
	);
	?>
	<div class="post_header entry-header">
		<?php
		if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
			?>
			<div class="post_meta">
				<a href="<?php echo esc_url( $alliance_link ); ?>" class="post_meta_item post_date"><?php echo wp_kses_data( alliance_get_date() ); ?></a>
			</div>
			<?php
		}
		?>
		<h6 class="post_title entry-title"><a href="<?php echo esc_url( $alliance_link ); ?>"><?php
			if ( '' == get_the_title() ) {
				esc_html_e( 'No title', 'alliance' );
			} else {
				the_title();
			}
		?></a></h6>
	</div>
</div><?php
$alliance_post = apply_filters( 'alliance_filter_related_post_output', ob_get_contents(), the_ID() );
ob_end_clean();
alliance_show_layout( $alliance_post );