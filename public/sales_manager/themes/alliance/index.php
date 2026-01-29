<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

$alliance_template = apply_filters( 'alliance_filter_get_template_part', alliance_blog_archive_get_template() );

if ( ! empty( $alliance_template ) && 'index' != $alliance_template ) {

	get_template_part( $alliance_template );

} else {

	alliance_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$alliance_stickies   = is_home()
								|| ( in_array( alliance_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) alliance_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$alliance_post_type  = alliance_get_theme_option( 'post_type' );
		$alliance_args       = array(
								'blog_style'     => alliance_get_theme_option( 'blog_style' ),
								'post_type'      => $alliance_post_type,
								'taxonomy'       => alliance_get_post_type_taxonomy( $alliance_post_type ),
								'parent_cat'     => alliance_get_theme_option( 'parent_cat' ),
								'posts_per_page' => alliance_get_theme_option( 'posts_per_page' ),
								'sticky'         => alliance_get_theme_option( 'sticky_style' ) == 'columns'
															&& is_array( $alliance_stickies )
															&& count( $alliance_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		alliance_blog_archive_start();

		do_action( 'alliance_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'alliance_action_before_page_author' );
			get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'alliance_action_after_page_author' );
		}

		if ( alliance_get_theme_option( 'show_filters' ) ) {
			do_action( 'alliance_action_before_page_filters' );
			alliance_show_filters( $alliance_args );
			do_action( 'alliance_action_after_page_filters' );
		} else {
			do_action( 'alliance_action_before_page_posts' );
			alliance_show_posts( array_merge( $alliance_args, array( 'cat' => $alliance_args['parent_cat'] ) ) );
			do_action( 'alliance_action_after_page_posts' );
		}

		do_action( 'alliance_action_blog_archive_end' );

		alliance_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
