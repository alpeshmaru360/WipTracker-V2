<?php
/* M Chart support functions
------------------------------------------------------------------------------- */


// Check if plugin installed and activated
if ( ! function_exists( 'alliance_exists_m_chart' ) ) {
	function alliance_exists_m_chart() {
		return class_exists( 'M_Chart' );
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'alliance_exists_m_chart_highcharts' ) ) {
	function alliance_exists_m_chart_highcharts() {
		return class_exists( 'M_Chart_Highcharts_Library' );
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_m_chart_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_m_chart_theme_setup9', 9 );
	function alliance_m_chart_theme_setup9() {
		if ( alliance_exists_m_chart() ) {
			add_action( 'wp_enqueue_scripts', 'alliance_m_chart_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_m_chart', 'alliance_m_chart_frontend_scripts', 10, 1 );

			add_filter( 'alliance_filter_merge_styles', 'alliance_m_chart_merge_styles' );
			add_filter( 'alliance_filter_merge_scripts', 'alliance_m_chart_merge_scripts' );
		}
		if ( is_admin() ) {
            add_filter( 'alliance_filter_tgmpa_required_plugins', 'alliance_m_chart_tgmpa_required_plugins' );
        }
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'alliance_m_chart_tgmpa_required_plugins' ) ) {    
    function alliance_m_chart_tgmpa_required_plugins( $list = array() ) {
        if ( alliance_storage_isset( 'required_plugins', 'm-chart' ) && alliance_storage_get_array( 'required_plugins', 'm-chart', 'install' ) !== false ) {
            $list[] = array(
                'name'     => alliance_storage_get_array( 'required_plugins', 'm-chart', 'title' ),
                'slug'     => 'm-chart',
                'required' => false,
            );
        }
        if ( alliance_storage_isset( 'required_plugins', 'm-chart-highcharts-library' ) && alliance_storage_get_array( 'required_plugins', 'm-chart-highcharts-library', 'install' ) !== false ) {
            $path = alliance_get_plugin_source_path( 'plugins/m-chart-highcharts-library/m-chart-highcharts-library.zip' );
			if ( ! empty( $path ) || alliance_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => alliance_storage_get_array( 'required_plugins', 'm-chart-highcharts-library', 'title' ),
					'slug'     => 'm-chart-highcharts-library',
					'source'   => ! empty( $path ) ? $path : 'upload://m-chart-highcharts-library.zip',
					'version'  => '1.1',
					'required' => false,
				);
			}
        }
        return $list;
    }
}


// Styles & Scripts
//------------------------------------------------------------------------
// Enqueue custom scripts
if ( ! function_exists( 'alliance_m_chart_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_m_chart_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_m_chart', 'alliance_m_chart_frontend_scripts', 10, 1 );
	function alliance_m_chart_frontend_scripts( $force = false ) {
		static $loaded = false;
		if ( ! $loaded && (
			current_action() == 'wp_enqueue_scripts' && alliance_need_frontend_scripts( 'm_chart' )
			||
			current_action() != 'wp_enqueue_scripts' && $force === true
			)
		) {
			$loaded = true;
			$alliance_url = alliance_get_file_url( 'plugins/m-chart/m-chart.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-m-chart', $alliance_url, array(), null );
			}
			if ( alliance_exists_m_chart_highcharts() ) {
				$alliance_url = alliance_get_file_url( 'plugins/m-chart/m-chart.js' );
				if ( '' != $alliance_url ) {
					wp_enqueue_script( 'alliance-m-chart', $alliance_url, array( 'jquery', 'highcharts' ), null, true );
				}
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'alliance_m_chart_merge_styles' ) ) {
	//Handler of the add_filter('alliance_filter_merge_styles', 'alliance_m_chart_merge_styles');
	function alliance_m_chart_merge_styles( $list ) {
		$list[ 'plugins/m-chart/m-chart.css' ] = true;
		return $list;
	}
}

// Merge custom scripts
if ( ! function_exists( 'alliance_m_chart_merge_scripts' ) ) {
	//Handler of the add_filter('alliance_filter_merge_scripts', 'alliance_m_chart_merge_scripts');
	function alliance_m_chart_merge_scripts( $list ) {
		if ( alliance_exists_m_chart_highcharts() ) {
			$list[ 'plugins/m-chart/m-chart.js' ] = true;
		}
		return $list;
	}
}

// Merge custom scripts
if ( ! function_exists( 'alliance_m_chart_script_deps' ) ) {
	 add_filter('trx_addons_filter_script_deps', 'alliance_m_chart_script_deps');
	function alliance_m_chart_script_deps( $list ) {
		if ( alliance_exists_m_chart() ) {
			array_push($list, 'highcharts');
		}
		return $list;
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( alliance_exists_m_chart() ) {
	require_once alliance_get_file_dir( 'plugins/m-chart/m-chart-style.php' );
}

// Load required styles and scripts for the frontend
if ( !function_exists( 'alliance_m_chart_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'alliance_m_chart_load_scripts_front', 20 );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'alliance_m_chart_load_scripts_front', 10, 1 );
	function alliance_m_chart_load_scripts_front( $force = false ) {
		static $loaded = false;
		if ( ! alliance_exists_m_chart() || !alliance_exists_trx_addons() ) return;
		$debug    = trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) );
		$optimize = ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) );
		$preview_elm = trx_addons_is_preview( 'elementor' );
		$preview_gb  = trx_addons_is_preview( 'gutenberg' );
		$theme_full  = current_theme_supports( 'styles-and-scripts-full-merged' );
		$need        = ! $loaded && ( ! $preview_elm || $debug ) && ! $preview_gb && $optimize && (
						$force === true
							|| ( $preview_elm && $debug )
							|| trx_addons_sc_check_in_content( array(
									'sc' => 'm_chart',
									'entries' => array(
												array( 'type' => 'sc',  'sc' => 'chart' ),
												//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/charts' ),// This sc is not exists for GB
												array( 'type' => 'elm', 'sc' => '"widgetType":"chart"' ),
												array( 'type' => 'elm', 'sc' => '"shortcode":"[chart' ),
									)
								) ) );
		if ( ! $loaded && ! $preview_gb && ( ( ! $optimize && $debug ) || ( $optimize && $need ) ) ) {
			$loaded = true;
			do_action( 'trx_addons_action_load_scripts_front', $force, 'm_chart' );
		}
		if ( ! $loaded && $preview_elm && $optimize && ! $debug && ! $theme_full ) {
			do_action( 'trx_addons_action_load_scripts_front', false, 'm_chart', 2 );
		}
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'alliance_m_chart_check_in_html_output' ) ) {
	add_action( 'trx_addons_action_check_page_content', 'alliance_m_chart_check_in_html_output', 10, 1 );
	function alliance_m_chart_check_in_html_output( $content = '' ) {
		if ( alliance_exists_m_chart()
			&& ! trx_addons_need_frontend_scripts( 'm_chart' )
			&& ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) )
		) {
			$checklist = apply_filters( 'trx_addons_filter_check_in_html', array(
							'class=[\'"][^\'"]*m-chart',
							),
							'm_chart'
						);
			foreach ( $checklist as $item ) {
				if ( preg_match( "#{$item}#", $content, $matches ) ) {
					alliance_m_chart_load_scripts_front( true );
					break;
				}
			}
		}
		return $content;
	}
}

// Remove plugin-specific styles if present in the page head output
if ( !function_exists( 'alliance_m_chart_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'alliance_m_chart_filter_head_output', 10, 1 );
	function alliance_m_chart_filter_head_output( $content = '' ) {
		if ( alliance_exists_m_chart()
			&& trx_addons_get_option( 'optimize_css_and_js_loading' ) == 'full'
			&& ! trx_addons_is_preview()
			&& ! trx_addons_need_frontend_scripts( 'm_chart' )
			&& apply_filters( 'trx_addons_filter_remove_3rd_party_styles', true, 'm_chart' )
		) {
			$content = preg_replace( '#<link[^>]*href=[\'"][^\'"]*/m-chart/[^>]*>#', '', $content );
		}
		return $content;
	}
}

// Remove plugin-specific styles and scripts if present in the page body output
if ( !function_exists( 'alliance_m_chart_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'alliance_m_chart_filter_body_output', 10, 1 );
	function alliance_m_chart_filter_body_output( $content = '' ) {
		if ( alliance_exists_m_chart()
			&& trx_addons_get_option( 'optimize_css_and_js_loading' ) == 'full'
			&& ! trx_addons_is_preview()
			&& ! trx_addons_need_frontend_scripts( 'm_chart' )
			&& apply_filters( 'trx_addons_filter_remove_3rd_party_styles', true, 'm_chart' )
		) {
			$content = preg_replace( '#<link[^>]*href=[\'"][^\'"]*/m-chart/[^>]*>#', '', $content );
			$content = preg_replace( '#<script[^>]*src=[\'"][^\'"]*/m-chart/[^>]*>[\\s\\S]*</script>#U', '', $content );
			$content = preg_replace( '#<script[^>]*id=[\'"]m-chart[^>]*>[\\s\\S]*</script>#U', '', $content );
		}
		return $content;
	}
}


// Other
//------------------------------------------------------------------------
// This filter hook is triggered after all of the Highcharts/Chart.js chart args for a given chart have been generated.
if ( ! function_exists( 'alliance_m_chart_chart_args' ) ) {
	add_filter( 'm_chart_chart_args', 'alliance_m_chart_chart_args', 10, 4 );
	function alliance_m_chart_chart_args( $chart_args, $post, $post_meta, $args ) {
		// Type: Columns & Pie & Polar
		if ( in_array( $chart_args['chart']['type'], array( 'column', 'pie', 'polar' ) )  ) {		
			$chart_args['colors'] = array(
				alliance_get_scheme_color( 'accent_link' ),
				alliance_get_scheme_color( 'accent_link5' ),
				alliance_get_scheme_color( 'accent_link3' ),
				alliance_get_scheme_color( 'accent_link4' ),
				alliance_get_scheme_color( 'accent_link2' ),
				alliance_get_scheme_color( 'text_dark' ),
			);
		}

		// Type: Area
		if ( $chart_args['chart']['type'] == 'area' ) {			
			$chart_args['colors'] = array(
				alliance_get_scheme_color( 'accent_link2' ),
				alliance_get_scheme_color( 'accent_link3' ),
				alliance_get_scheme_color( 'accent_link' ),
				alliance_get_scheme_color( 'text_dark' ),
				alliance_get_scheme_color( 'accent_link4' ),
				alliance_get_scheme_color( 'accent_link5' ),
			);
		}

		// Type: Spline
		if ( $chart_args['chart']['type'] == 'spline' ) {			
			$chart_args['colors'] = array(
				alliance_get_scheme_color( 'accent_link5' ),
				alliance_get_scheme_color( 'accent_link' ),
				alliance_get_scheme_color( 'accent_link3' ),
				alliance_get_scheme_color( 'accent_link4' ),
				alliance_get_scheme_color( 'accent_link2' ),
				alliance_get_scheme_color( 'text_dark' ),
			);
		}
		
		return $chart_args;
	}
}


// One-click import support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'alliance_m_chart_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options',	'alliance_m_chart_importer_set_options' );
	function alliance_m_chart_importer_set_options($options=array()) {
		if ( alliance_exists_m_chart()  && in_array('m-chart', $options['required_plugins']) ) {
			$options['additional_options'][]	= 'm-chart%';
		}
		return $options;
	}
}