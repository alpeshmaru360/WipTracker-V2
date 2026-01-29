<?php
/* WP Job Manager support functions
------------------------------------------------------------------------------- */


// Check if plugin installed and activated
if ( ! function_exists( 'alliance_exists_wp_job_manager' ) ) {
	function alliance_exists_wp_job_manager() {
		return class_exists( 'WP_Job_Manager' );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'alliance_wp_job_manager_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'alliance_wp_job_manager_theme_setup3', 3 );
	function alliance_wp_job_manager_theme_setup3() {
		if ( alliance_exists_wp_job_manager() ) {
			// Section 'WP Job Manager'
			alliance_storage_merge_array(
				'options', '', array_merge(
					array(
						'wp-job-manager'     => array(
							'title' => esc_html__( 'WP Job Manager', 'alliance' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the WP Job Manager pages', 'alliance' ) ),
							'icon'  => 'icon-briefcase',
							'type'  => 'section',
						)
					),
					alliance_options_get_list_cpt_options( 'wp-job-manager', esc_html__( 'WP Job Manager', 'alliance' ) ),
					array(
						'blog_single_info_wp-job-manager'      => array(
							'title' => esc_html__( 'WP Job Manager posts', 'alliance' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_author_info_wp-job-manager'		=> array(
							'title' => esc_html__( 'Show author info', 'alliance' ),
							'desc'  => wp_kses_data( __( "Display block with information about post's author", 'alliance' ) ),
							'std'   => 1,
							'type'  => 'switch',
						),
						'show_related_posts_wp-job-manager'		=> array(
							'title'    => esc_html__( 'Show related posts', 'alliance' ),
							'desc'     => wp_kses_data( __( "Show 'Related posts' section on single post pages", 'alliance' ) ),
							'std'      => 1,
							'type'     => 'switch',
						),
						'posts_navigation_wp-job-manager'		=> array(
							'title'   => esc_html__( 'Show post navigation', 'alliance' ),
							'desc'    => wp_kses_data( __( "Display post navigation on single post pages or load the next post automatically after the content of the current article.", 'alliance' ) ),
							'std'     => 'links',
							'options' => array(
								'none'   => esc_html__('None', 'alliance'),
								'links'  => esc_html__('Prev/Next links', 'alliance'),
							),
							'pro_only'=> ALLIANCE_THEME_FREE,
							'type'    => 'radio',
						)
					)
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_wp_job_manager_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_wp_job_manager_theme_setup9', 9 );
	function alliance_wp_job_manager_theme_setup9() {
		if ( alliance_exists_wp_job_manager() ) {
			add_action( 'wp_enqueue_scripts', 'alliance_wp_job_manager_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_wp_job_manager', 'alliance_wp_job_manager_frontend_scripts', 10, 1 );

			add_action( 'wp_enqueue_scripts', 'alliance_wp_job_manager_frontend_scripts_responsive', 2000 );
			add_action( 'trx_addons_action_load_scripts_front_wp_job_manager', 'alliance_wp_job_manager_frontend_scripts_responsive', 10, 1 );
			
			add_filter( 'alliance_filter_merge_styles', 'alliance_wp_job_manager_merge_styles' );
			add_filter( 'alliance_filter_merge_styles_responsive', 'alliance_wp_job_manager_merge_styles_responsive' );

			add_action( 'alliance_filter_detect_blog_mode', 'alliance_wp_job_manager_detect_blog_mode' );

			// Search theme-specific templates in the skin dir (if exists)
			add_filter( 'job_manager_locate_template', 'alliance_wp_job_manager_locate_template', 100, 3 );
		}
		if ( is_admin() ) {
            add_filter( 'alliance_filter_tgmpa_required_plugins', 'alliance_wp_job_manager_tgmpa_required_plugins' );
        }
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'alliance_wp_job_manager_tgmpa_required_plugins' ) ) {    
    function alliance_wp_job_manager_tgmpa_required_plugins( $list = array() ) {
        if ( alliance_storage_isset( 'required_plugins', 'wp-job-manager' ) && alliance_storage_get_array( 'required_plugins', 'wp-job-manager', 'install' ) !== false ) {
            $list[] = array(
                'name'     => alliance_storage_get_array( 'required_plugins', 'wp-job-manager', 'title' ),
                'slug'     => 'wp-job-manager',
                'required' => false,
            );
        }
        return $list;
    }
}


// Styles & Scripts
//------------------------------------------------------------------------
// Enqueue styles for frontend
if ( ! function_exists( 'alliance_wp_job_manager_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_wp_job_manager_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_wp_job_manager', 'alliance_wp_job_manager_frontend_scripts', 10, 1 );
	function alliance_wp_job_manager_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'wp_job_manager' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/wp-job-manager/wp-job-manager.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-wp-job-manager', $alliance_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'alliance_wp_job_manager_frontend_scripts_responsive' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_wp_job_manager_frontend_scripts_responsive', 2000 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_wp_job_manager', 'alliance_wp_job_manager_frontend_scripts_responsive', 10, 1 );
	function alliance_wp_job_manager_frontend_scripts_responsive( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'wp_job_manager' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/wp-job-manager/wp-job-manager-responsive.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-wp-job-manager-responsive', $alliance_url, array(), null, alliance_media_for_load_css_responsive( 'wp-job-manager' ) );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'alliance_wp_job_manager_merge_styles' ) ) {
	//Handler of the add_filter( 'alliance_filter_merge_styles', 'alliance_wp_job_manager_merge_styles');
	function alliance_wp_job_manager_merge_styles( $list ) {
		$list[ 'plugins/wp-job-manager/wp-job-manager.css' ] = true;
		return $list;
	}
}

// Merge responsive styles
if ( ! function_exists( 'alliance_wp_job_manager_merge_styles_responsive' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles_responsive', 'alliance_wp_job_manager_merge_styles_responsive');
	function alliance_wp_job_manager_merge_styles_responsive( $list ) {
		$list[ 'plugins/wp-job-manager/wp-job-manager-responsive.css' ] = true;
		return $list;
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( alliance_exists_wp_job_manager() ) {
	require_once alliance_get_file_dir( 'plugins/wp-job-manager/wp-job-manager-style.php' );
}

// Load required styles and scripts for the frontend
if ( !function_exists( 'alliance_wp_job_manager_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'alliance_wp_job_manager_load_scripts_front', 20 );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'alliance_wp_job_manager_load_scripts_front', 10, 1 );
	function alliance_wp_job_manager_load_scripts_front( $force = false ) {
		static $loaded = false;
		if ( ! alliance_exists_wp_job_manager() || !alliance_exists_trx_addons() ) return;
		$debug    = trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) );
		$optimize = ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) );
		$preview_elm = trx_addons_is_preview( 'elementor' );
		$preview_gb  = trx_addons_is_preview( 'gutenberg' );
		$theme_full  = current_theme_supports( 'styles-and-scripts-full-merged' );
		$need        = ! $loaded && ( ! $preview_elm || $debug ) && ! $preview_gb && $optimize && (
						$force === true
							|| ( $preview_elm && $debug )
							|| trx_addons_sc_check_in_content( array(
									'sc' => 'wp_job_manager',
									'entries' => array(
												array( 'type' => 'sc',  'sc' => 'job' ),
												//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/charts' ),// This sc is not exists for GB
												array( 'type' => 'elm', 'sc' => '"widgetType":"job"' ),
												array( 'type' => 'elm', 'sc' => '"shortcode":"[job' ),
												array( 'type' => 'elm', 'sc' => '"shortcode":"[submit_job_form' ),
									)
								) ) );
		if ( ! $loaded && ! $preview_gb && ( ( ! $optimize && $debug ) || ( $optimize && $need ) ) ) {
			$loaded = true;
			do_action( 'trx_addons_action_load_scripts_front', $force, 'wp_job_manager' );
		}
		if ( ! $loaded && $preview_elm && $optimize && ! $debug && ! $theme_full ) {
			do_action( 'trx_addons_action_load_scripts_front', false, 'wp_job_manager', 2 );
		}
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'alliance_wp_job_manager_check_in_html_output' ) ) {
	add_action( 'trx_addons_action_check_page_content', 'alliance_wp_job_manager_check_in_html_output', 10, 1 );
	function alliance_wp_job_manager_check_in_html_output( $content = '' ) {
		if ( alliance_exists_wp_job_manager()
			&& ! trx_addons_need_frontend_scripts( 'wp_job_manager' )
			&& ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) )
		) {
			$checklist = apply_filters( 'trx_addons_filter_check_in_html', array(
							'class=[\'"][^\'"]*job-',
							'class=[\'"][^\'"]*job_',
							),
							'wp_job_manager'
						);
			foreach ( $checklist as $item ) {
				if ( preg_match( "#{$item}#", $content, $matches ) ) {
					alliance_wp_job_manager_load_scripts_front( true );
					break;
				}
			}
		}
		return $content;
	}
}

// Remove plugin-specific styles if present in the page head output
if ( !function_exists( 'alliance_wp_job_manager_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'alliance_wp_job_manager_filter_head_output', 10, 1 );
	function alliance_wp_job_manager_filter_head_output( $content = '' ) {
		if ( alliance_exists_wp_job_manager()
			&& trx_addons_get_option( 'optimize_css_and_js_loading' ) == 'full'
			&& ! trx_addons_is_preview()
			&& ! trx_addons_need_frontend_scripts( 'wp_job_manager' )
			&& apply_filters( 'trx_addons_filter_remove_3rd_party_styles', true, 'wp_job_manager' )
		) {
			$content = preg_replace( '#<link[^>]*href=[\'"][^\'"]*/wp-job-manager/[^>]*>#', '', $content );
		}
		return $content;
	}
}

// Remove plugin-specific styles and scripts if present in the page body output
if ( !function_exists( 'alliance_wp_job_manager_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'alliance_wp_job_manager_filter_body_output', 10, 1 );
	function alliance_wp_job_manager_filter_body_output( $content = '' ) {
		if ( alliance_exists_wp_job_manager()
			&& trx_addons_get_option( 'optimize_css_and_js_loading' ) == 'full'
			&& ! trx_addons_is_preview()
			&& ! trx_addons_need_frontend_scripts( 'wp_job_manager' )
			&& apply_filters( 'trx_addons_filter_remove_3rd_party_styles', true, 'wp_job_manager' )
		) {
			$content = preg_replace( '#<link[^>]*href=[\'"][^\'"]*/wp-job-manager/[^>]*>#', '', $content );
			$content = preg_replace( '#<script[^>]*src=[\'"][^\'"]*/wp-job-manager/[^>]*>[\\s\\S]*</script>#U', '', $content );
			$content = preg_replace( '#<script[^>]*id=[\'"]wp-job-manager[^>]*>[\\s\\S]*</script>#U', '', $content );
		}
		return $content;
	}
}


// Other
//------------------------------------------------------------------------
// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_wp_job_manager_init' ) ) {
	add_action( 'wp', 'alliance_wp_job_manager_init' );
	function alliance_wp_job_manager_init() {
		if ( alliance_exists_wp_job_manager() ) {
			if ( is_wpjm() && is_single() ) {
				add_action( 'alliance_action_page_content_start', 'alliance_skin_post_title' );
			}
		}
	}
}

// Page title
if ( ! function_exists( 'alliance_wp_job_manager_page_title' ) ) {
	add_filter( 'alliance_skin_filter_page_title', 'alliance_wp_job_manager_page_title' );
	function alliance_wp_job_manager_page_title( $allow ) {	
		if ( alliance_exists_wp_job_manager() ) {
			return is_single() ? true : $allow;
		}
		return $allow;
	}
}

// Detect current blog mode
if ( ! function_exists( 'alliance_wp_job_manager_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'alliance_filter_detect_blog_mode', 'alliance_wp_job_manager_detect_blog_mode' );
	function alliance_wp_job_manager_detect_blog_mode( $mode = '' ) {
		if ( alliance_exists_wp_job_manager() ) {
			if ( is_wpjm() ) {
				$mode = 'wp-job-manager';
			}
		}
		return $mode;
	}
}

// Shortcode [jobs] - add title
if ( ! function_exists( 'alliance_wp_job_manager_output_jobs_defaults' ) ) {
	add_filter( 'job_manager_output_jobs_defaults', 'alliance_wp_job_manager_output_jobs_defaults' );
	function alliance_wp_job_manager_output_jobs_defaults( $atts ) {	
		$atts['title'] = '';
		return $atts;
	}
}

// Shortcode [jobs] - add title
if ( ! function_exists( 'alliance_wp_job_manager_jobs_shortcode_data_attributes' ) ) {
	add_filter( 'job_manager_jobs_shortcode_data_attributes', 'alliance_wp_job_manager_jobs_shortcode_data_attributes', 10, 2 );
	function alliance_wp_job_manager_jobs_shortcode_data_attributes( $data_attributes, $atts ) {	
		if ( !empty($atts['title']) ) {
			$data_attributes['title'] = $atts['title'];
		}
		return $data_attributes;
	}
}

// Search skin-specific templates in the skin dir (if exists)
if ( ! function_exists( 'alliance_wp_job_manager_locate_template' ) ) {
	//Handler of the add_filter( 'job_manager_locate_template', 'alliance_wp_job_manager_locate_template', 100, 3 );
	function alliance_wp_job_manager_locate_template( $template, $template_name, $template_path ) {
		$folders = apply_filters( 'alliance_filter_wp_job_manager_locate_template_folders', array(
			$template_path,
			'plugins/wp-job-manager/templates'
		) );
		foreach ( $folders as $f ) {
			$theme_dir = apply_filters( 'alliance_filter_get_theme_file_dir', '', trailingslashit( alliance_esc( $f ) ) . $template_name );
			if ( '' != $theme_dir ) {
				$template = $theme_dir;
				break;
			}
		}
		return $template;
	}
}

// Related posts 
if ( ! function_exists( 'alliance_wp_job_manager_related_post_output' ) ) {
	add_filter( 'alliance_filter_related_post_output', 'alliance_wp_job_manager_related_post_output', 10, 2 );
	function alliance_wp_job_manager_related_post_output( $output, $id ) {
		if ( get_post_type( $id ) == 'job_listing' ) {
			$output = do_shortcode( '[job_summary id="'. $id .'" align="none" width="100%"]' );
		}
		return $output;
	}
}

// Archive page link
if ( ! function_exists( 'alliance_wp_job_manager_post_type_archive_link' ) ) {
	add_filter( 'alliance_filter_post_type_archive_link', 'alliance_wp_job_manager_post_type_archive_link', 10, 2 );
	function alliance_wp_job_manager_post_type_archive_link( $link, $post_type ) {
		if ( $post_type == 'job_listing' ) {
			$jobs_page_id = get_option( 'job_manager_jobs_page_id' );
			if ( $jobs_page_id ) {
				$link = get_permalink( $jobs_page_id );
			}	
		}
		return $link;
	}
}


// One-click import support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'alliance_wp_job_manager_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options',	'alliance_wp_job_manager_importer_set_options' );
	function alliance_wp_job_manager_importer_set_options($options=array()) {
		if ( alliance_exists_wp_job_manager()  && in_array('wp-job-manager', $options['required_plugins']) ) {
			$options['additional_options'][]	= '%job_manager%';
		}
		return $options;
	}
}
