<?php
/* BBPress and BuddyPress support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'alliance_bbpress_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'alliance_bbpress_theme_setup1', 1 );
	function alliance_bbpress_theme_setup1() {
		add_filter( 'alliance_filter_list_sidebars', 'alliance_bbpress_list_sidebars' );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'alliance_bbpress_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'alliance_bbpress_theme_setup3', 3 );
	function alliance_bbpress_theme_setup3() {
		if ( alliance_exists_bbpress() ) {

			// Section 'BBPress and BuddyPress'
			alliance_storage_merge_array(
				'options', '', array_merge(
					array(
						'bbpress'     => array(
							'title' => esc_html__( 'BB(Buddy) Press', 'alliance' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the community pages', 'alliance' ) ),
							'icon'  => 'icon-bb-buddy-press',
							'type'  => 'section',
						),
						'forum_style' => array(
							'title'   => esc_html__( 'Forum style', 'alliance' ),
							'desc'    => wp_kses_data( __( 'Select style to display forums list on the community pages', 'alliance' ) ),
							'std'     => 'default',
							'options' => array(
								'default'  => esc_html__( 'Default', 'alliance' ),
								'light'    => esc_html__( 'Light', 'alliance' ),
								'callouts' => esc_html__( 'Callouts', 'alliance' ),
							),
							'type'    => 'select',
						),
					),
					alliance_options_get_list_cpt_options( 'bbpress', esc_html__( 'community', 'alliance' ) )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_bbpress_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_bbpress_theme_setup9', 9 );
	function alliance_bbpress_theme_setup9() {

		if ( alliance_exists_bbpress() ) {
			add_action( 'wp_enqueue_scripts', 'alliance_bbpress_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_bbpress', 'alliance_bbpress_frontend_scripts', 10, 1 );
			add_action( 'wp_enqueue_scripts', 'alliance_bbpress_frontend_scripts_responsive', 2000 );
			add_action( 'trx_addons_action_load_scripts_front_bbpress', 'alliance_bbpress_frontend_scripts_responsive', 10, 1 );
			add_filter( 'alliance_filter_merge_styles', 'alliance_bbpress_merge_styles' );
			add_filter( 'alliance_filter_merge_styles_responsive', 'alliance_bbpress_merge_styles_responsive' );
			add_filter( 'alliance_filter_detect_blog_mode', 'alliance_bbpress_detect_blog_mode' );
			add_filter( 'post_class', 'alliance_bbpress_add_post_classes' );
		}
		if ( is_admin() ) {
			add_filter( 'alliance_filter_tgmpa_required_plugins', 'alliance_bbpress_tgmpa_required_plugins' );
			add_filter( 'alliance_filter_theme_plugins', 'alliance_bbpress_theme_plugins' );
		}

	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'alliance_bbpress_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('alliance_filter_tgmpa_required_plugins',	'alliance_bbpress_tgmpa_required_plugins');
	function alliance_bbpress_tgmpa_required_plugins( $list = array() ) {
		if ( alliance_storage_isset( 'required_plugins', 'bbpress' ) && alliance_storage_get_array( 'required_plugins', 'bbpress', 'install' ) !== false ) {
			$list[] = array(
				'name'     => esc_html__( 'BBPress', 'alliance' ),
				'slug'     => 'bbpress',
				'required' => false,
			);
			$list[] = array(
				'name'     => esc_html__( 'BuddyPress', 'alliance' ),
				'slug'     => 'buddypress',
				'required' => false,
			);
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'alliance_bbpress_theme_plugins' ) ) {
	//Handler of the add_filter( 'alliance_filter_theme_plugins', 'alliance_bbpress_theme_plugins' );
	function alliance_bbpress_theme_plugins( $list = array() ) {
		if ( ! empty( $list['bbpress']['group'] ) ) {
			$list['bbpress']['title'] = esc_html__( 'BBPress', 'alliance' );
			$list = alliance_add_group_and_logo_to_slave( $list, 'bbpress', 'buddypress' );
		}
		return $list;
	}
}

// Check if BBPress and BuddyPress is installed and activated
if ( ! function_exists( 'alliance_exists_bbpress' ) ) {
	function alliance_exists_bbpress() {
		return class_exists( 'BuddyPress' ) || class_exists( 'bbPress' );
	}
}

// Return true, if current page is any bbpress page
if ( ! function_exists( 'alliance_is_bbpress_page' ) ) {
	function alliance_is_bbpress_page() {
		$rez = false;
		if ( alliance_exists_bbpress() ) {
			if ( ! is_search() ) {
				$rez = ( function_exists( 'is_buddypress' ) && is_buddypress() )
					|| ( function_exists( 'is_bbpress' ) && is_bbpress() )
					|| ( ! is_user_logged_in() && in_array( get_query_var( 'post_type' ), array( 'forum', 'topic', 'reply' ) ) );
			}
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'alliance_bbpress_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'alliance_filter_detect_blog_mode', 'alliance_bbpress_detect_blog_mode' );
	function alliance_bbpress_detect_blog_mode( $mode = '' ) {
		if ( alliance_is_bbpress_page() ) {
			$mode = 'bbpress';
		}
		return $mode;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'alliance_bbpress_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_bbpress_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_bbpress', 'alliance_bbpress_frontend_scripts', 10, 1 );
	function alliance_bbpress_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'bbpress' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/bbpress/bbpress.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-bbpress', $alliance_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'alliance_bbpress_frontend_scripts_responsive' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_bbpress_frontend_scripts_responsive', 2000 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_bbpress', 'alliance_bbpress_frontend_scripts_responsive', 10, 1 );
	function alliance_bbpress_frontend_scripts_responsive( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'bbpress' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/bbpress/bbpress-responsive.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-bbpress-responsive', $alliance_url, array(), null, alliance_media_for_load_css_responsive( 'bbpress' ) );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'alliance_bbpress_merge_styles' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles', 'alliance_bbpress_merge_styles');
	function alliance_bbpress_merge_styles( $list ) {
		$list[ 'plugins/bbpress/bbpress.css' ] = false;
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'alliance_bbpress_merge_styles_responsive' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles_responsive', 'alliance_bbpress_merge_styles_responsive');
	function alliance_bbpress_merge_styles_responsive( $list ) {
		$list[ 'plugins/bbpress/bbpress-responsive.css' ] = false;
		return $list;
	}
}

// Add plugin specific classes to the posts
if ( ! function_exists( 'alliance_bbpress_add_post_classes' ) ) {
	//Handler of the add_filter( 'post_class', 'alliance_bbpress_add_post_classes' );
	function alliance_bbpress_add_post_classes( $classes ) {
		if ( alliance_is_bbpress_page() ) {
			$classes[] = 'bbpress_style_' . esc_attr( alliance_get_theme_option( 'forum_style' ) );
		}
		return $classes;
	}
}



// Add BBPress and BuddyPress specific items into list of sidebars
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'alliance_bbpress_list_sidebars' ) ) {
	//Handler of the add_filter( 'alliance_filter_list_sidebars', 'alliance_bbpress_list_sidebars' );
	function alliance_bbpress_list_sidebars( $list = array() ) {
		$list['bbpress_widgets'] = array(
			'name'        => esc_html__( 'BBPress and BuddyPress Widgets', 'alliance' ),
			'description' => esc_html__( 'Widgets to be shown on the BBPress and BuddyPress pages', 'alliance' ),
		);
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( alliance_exists_bbpress() ) {
	$alliance_fdir = alliance_get_file_dir( 'plugins/bbpress/bbpress-style.php' );
	if ( ! empty( $alliance_fdir ) ) {
		require_once $alliance_fdir;
	}
}
