<?php
/* The Events Calendar support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_tribe_events_skin_init' ) ) {
	add_action( 'wp', 'alliance_tribe_events_skin_init' );
	function alliance_tribe_events_skin_init() {
		if ( alliance_exists_tribe_events() ) {
			if ( tribe_is_event_query() && !is_single() ) {
				add_action( 'alliance_action_page_content_start', 'alliance_skin_post_title' );
			}
		}
	}
}

// Before event content
if ( ! function_exists( 'alliance_tribe_events_single_event_before_the_content' ) ) {
	add_action( 'tribe_events_single_event_before_the_content', 'alliance_tribe_events_single_event_before_the_content' );
	function alliance_tribe_events_single_event_before_the_content() {
		if ( tribe_get_cost() ) {
			?><span class="tribe-events-cost"><?php echo tribe_get_cost( null, true ) ?></span><?php
		}
	}
}

// Excerpt length
if ( ! function_exists( 'alliance_tribe_events_excerpt_length' ) ) {
	add_filter( 'tribe_events_get_the_excerpt', 'alliance_tribe_events_excerpt_length', 11, 2 );
	function alliance_tribe_events_excerpt_length( $cache_excerpts, $post ) {
		if ( tribe_is_month() ) {
			if ( strlen($cache_excerpts) > 140 ) {
				$cache_excerpts = substr( $cache_excerpts, 0, 140)  . '...' ;
			}
		}
		return $cache_excerpts;
	} 
}

// Organizer website link and Venue website Link
if ( ! function_exists( 'alliance_tribe_get_website_links' ) ) {
	add_filter( 'tribe_get_event_organizer_link_target', 'alliance_tribe_get_website_links', 11, 3 );
	add_filter( 'tribe_get_venue_website_link_target', 'alliance_tribe_get_website_links', 11, 3 );
	function alliance_tribe_get_website_links( $target, $url, $post_id ) {
		return '_blank';
	} 
}