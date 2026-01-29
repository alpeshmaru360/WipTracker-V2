<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

// Page (category, tag, archive, author) title

if ( alliance_need_page_title() ) {
	alliance_sc_layouts_showed( 'title', true );
	alliance_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								alliance_show_post_meta(
									apply_filters(
										'alliance_filter_post_meta_args', array(
											'components' => join( ',', alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', alliance_array_get_keys_by_value( alliance_get_theme_option( 'counters' ) ) ),
											'seo'        => alliance_is_on( alliance_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$alliance_blog_title           = alliance_get_blog_title();
							$alliance_blog_title_text      = '';
							$alliance_blog_title_class     = '';
							$alliance_blog_title_link      = '';
							$alliance_blog_title_link_text = '';
							if ( is_array( $alliance_blog_title ) ) {
								$alliance_blog_title_text      = $alliance_blog_title['text'];
								$alliance_blog_title_class     = ! empty( $alliance_blog_title['class'] ) ? ' ' . $alliance_blog_title['class'] : '';
								$alliance_blog_title_link      = ! empty( $alliance_blog_title['link'] ) ? $alliance_blog_title['link'] : '';
								$alliance_blog_title_link_text = ! empty( $alliance_blog_title['link_text'] ) ? $alliance_blog_title['link_text'] : '';
							} else {
								$alliance_blog_title_text = $alliance_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $alliance_blog_title_class ); ?>">
								<?php
								$alliance_top_icon = alliance_get_term_image_small();
								if ( ! empty( $alliance_top_icon ) ) {
									$alliance_attr = alliance_getimagesize( $alliance_top_icon );
									?>
									<img src="<?php echo esc_url( $alliance_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'alliance' ); ?>"
										<?php
										if ( ! empty( $alliance_attr[3] ) ) {
											alliance_show_layout( $alliance_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $alliance_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $alliance_blog_title_link ) && ! empty( $alliance_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $alliance_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $alliance_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'alliance_action_breadcrumbs' );
						$alliance_breadcrumbs = ob_get_contents();
						ob_end_clean();
						alliance_show_layout( $alliance_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
