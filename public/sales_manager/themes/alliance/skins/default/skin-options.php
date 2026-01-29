<?php
/**
 * Skin Options
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.76.0
 */


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc. or overriden values from the post/page meta)

if ( ! function_exists( 'alliance_create_theme_options' ) ) {

	function alliance_create_theme_options() {

		// Message about options override.
		// Attention! Not need esc_html() here, because this message put in wp_kses_data() below
		$msg_override = esc_html__( 'Attention! Some of these options can be overridden in the following sections (Blog, Plugins settings, etc.) or in the settings of individual pages. If you changed such parameter and nothing happened on the page, this option may be overridden in the corresponding section or in the Page Options of this page. These options are marked with an asterisk (*) in the title.', 'alliance' );

		// Color schemes number: if < 2 - hide fields with selectors
		$hide_schemes = count( alliance_storage_get( 'schemes' ) ) < 2;

		alliance_storage_set(

			'options', array(

				// 'Logo & Site Identity'
				//---------------------------------------------
				'login_privacy'                 => array(
					'title'    => esc_html__( 'Login privacy', 'alliance' ),
					'desc'     => '',
					'priority' => 5,
					'icon'     => 'icon-lock',
					'type'     => 'section',
				),
				'enable_login_privacy'                     => array(
					'title'    => esc_html__( 'Enable login privacy', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Allows the Administrator to restrict a web site to viewing only by registered users who are logged on. Any attempt, by anyone not logged, to view any Page, Post or other part of the site will display a WordPress login screen.', 'alliance' ) ),
					'priority' => 5,
					'std'      => 0,
					'type'     => 'switch',
				),
				'login_privacy_styles'                     => array(
					'title'    => esc_html__( 'Enable custom login page styles', 'alliance' ),
					'dependency' => array(
						'enable_login_privacy' => array( 1 ),
					),
					'priority' => 5,
					'std'      => 1,
					'type'     => 'switch',
				),
				'login_privacy_bg'                     => array(
					'title'    => esc_html__( 'Background Image', 'alliance' ),
					'dependency' => array(
						'login_privacy_styles' => array( 1 ),
						'enable_login_privacy' => array( 1 ),
					),
					'std'      => '',
					'type'  => 'image',
				),
				'login_privacy_logo'                     => array(
					'title'    => esc_html__( 'Custom Logo', 'alliance' ),
					'dependency' => array(
						'login_privacy_styles' => array( 1 ),
						'enable_login_privacy' => array( 1 ),
					),
					'std'      => '',
					'type'  => 'image',
				),
				'login_privacy_message'                     => array(
					'title'    => esc_html__( 'Custom Message', 'alliance' ),
					'dependency' => array(
						'login_privacy_styles' => array( 1 ),
						'enable_login_privacy' => array( 1 ),
					),
					'std'      => '',
					'allow_html' => true,
					'type'  => 'text_editor',
				),

				'title_tagline'                 => array(
					'title'    => esc_html__( 'Logo & Site Identity', 'alliance' ),
					'desc'     => '',
					'priority' => 10,
					'icon'     => 'icon-home-2',
					'type'     => 'section',
				),
				'logo_info'                     => array(
					'title'    => esc_html__( 'Logo Settings', 'alliance' ),
					'desc'     => '',
					'priority' => 20,
					'qsetup'   => esc_html__( 'General', 'alliance' ),
					'type'     => 'info',
				),
				'logo_text'                     => array(
					'title'    => esc_html__( 'Use Site Name as Logo', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Use the site title and tagline as a text logo if no image is selected', 'alliance' ) ),
					'priority' => 30,
					'std'      => 1,
					'qsetup'   => esc_html__( 'General', 'alliance' ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'switch',
				),
				'logo_zoom'                     => array(
					'title'      => esc_html__( 'Logo zoom', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Zoom the logo (set 1 to leave original size). For this parameter to affect images, their max-height should be specified in "em" instead of "px" during header creation. In this case, maximum logo size depends on the actual size of the picture.', 'alliance' ) ),
					'std'        => 1,
					'min'        => 0.2,
					'max'        => 2,
					'step'       => 0.1,
					'refresh'    => false,
					'show_value' => true,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'slider',
				),
				'logo_retina_enabled'           => array(
					'title'    => esc_html__( 'Allow retina display logo', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Show fields to select logo images for Retina display', 'alliance' ) ),
					'priority' => 40,
					'refresh'  => false,
					'std'      => 0,
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'switch',
				),
				// Parameter 'logo' was replaced with standard WordPress 'custom_logo'
				'logo_retina'                   => array(
					'title'      => esc_html__( 'Logo for Retina', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'alliance' ) ),
					'priority'   => 70,
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'image',
				),
				'logo_mobile_header'            => array(
					'title' => esc_html__( 'Logo for the mobile header', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile header (if enabled in the section "Header - Header mobile"', 'alliance' ) ),
					'std'   => '',
					'type'  => 'hidden',
				),
				'logo_mobile_header_retina'     => array(
					'title'      => esc_html__( 'Logo for the mobile header on Retina', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'alliance' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'logo_mobile'                   => array(
					'title' => esc_html__( 'Logo for the mobile menu', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile menu', 'alliance' ) ),
					'std'   => '',
					'type'  => 'hidden',
				),
				'logo_mobile_retina'            => array(
					'title'      => esc_html__( 'Logo mobile on Retina', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'alliance' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'logo_side'                     => array(
					'title' => esc_html__( 'Logo for the side menu', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu', 'alliance' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'logo_side_retina'              => array(
					'title'      => esc_html__( 'Logo for the side menu on Retina', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu on Retina displays (if empty - use default logo from the field above)', 'alliance' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'image',
				),



				// 'General settings'
				//---------------------------------------------
				'general'                       => array(
					'title'    => esc_html__( 'General', 'alliance' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 20,
					'icon'     => 'icon-settings',
					'demo'     => true,
					'type'     => 'section',
				),

				'general_layout_info'           => array(
					'title'  => esc_html__( 'Layout', 'alliance' ),
					'desc'   => '',
					'qsetup' => esc_html__( 'General', 'alliance' ),
					'demo'   => true,
					'type'   => 'info',
				),
				'body_style'                    => array(
					'title'    => esc_html__( 'Body style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'refresh'  => false,
					'std'      => 'wide',
					'options'  => alliance_get_list_body_styles( false ),
					'qsetup'   => esc_html__( 'General', 'alliance' ),
					'demo'     => true,
					'type'     => 'hidden',
				),
				'page_content'                   => array(
					'title'    => esc_html__( 'Page content', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose between the style of your page content. The classic view provides you with the primary content block and a sidebar, while the masonry view positions each block on a separate background.', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),		
					'std'      => 'blocks',
					'options'  => array(
						'classic'  => array(
									'title' => esc_html__( 'Classic', 'alliance' ),
									'icon'  => 'images/theme-options/page-content/classic.png',
								),
						'blocks'  => array(
									'title' => esc_html__( 'Blocks', 'alliance' ),
									'icon'  => 'images/theme-options/page-content/blocks.png',
								),
					),
					'type'     => 'choice',
				),
				'content_switcher'          => array(
					'title' => esc_html__( 'Page content switcher', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Add button at the bottom of the page', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'   => 0,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'switch',
				),
				'page_width'                    => array(
					'title'      => esc_html__( 'Page width', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Total width of the site content and sidebar (in pixels). If empty - use default width', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'body_style' => array( 'boxed', 'wide' ),
					),
					'std'        => alliance_theme_defaults( 'page_width' ),
					'min'        => 1000,
					'max'        => 1600,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page_width',          // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ALLIANCE_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'page_boxed_extra'             => array(
					'title'      => esc_html__( 'Boxed page extra spaces', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Width of the extra side space on boxed pages', 'alliance' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'std'        => alliance_theme_defaults( 'page_boxed_extra' ),
					'min'        => 0,
					'max'        => 150,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page_boxed_extra',   // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ALLIANCE_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'boxed_bg_image'                => array(
					'title'      => esc_html__( 'Boxed bg image', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload image for the background of the boxed content.', 'alliance' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'        => '',
					'qsetup'     => esc_html__( 'General', 'alliance' ),
					'type'       => 'hidden',
				),							
				'page_title'                	=> array(
					'title' => esc_html__( 'Page title', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Show page title over the page content', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'   => 1,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'switch',
				),									
				'page_subtitle'                	=> array(
					'title' => esc_html__( 'Page subtitle', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Show page subtitle over the page content', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'page_title' => array( 1 ),
					),
					'std'   => '',
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'text',
				),

				'general_menu_info'             => array(
					'title' => esc_html__( 'Navigation', 'alliance' ),
					'desc'  => '',
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => ! alliance_exists_trx_addons() ? 'hidden' : 'info',
				),
				'menu_side'                     => array(
					'title'    => esc_html__( 'Sidemenu position', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position of the side menu - panel', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'      => 'hide',
					'options'  => array(
						'hide'  => array(
										'title' => esc_html__( 'No menu', 'alliance' ),
										'icon'  => 'images/theme-options/menu-side/hide.png',
									),
						'left'  => array(
										'title' => esc_html__( 'Left menu', 'alliance' ),
										'icon'  => 'images/theme-options/menu-side/left.png',
									)
					),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'choice',
				),
				'menu_side_open'         		=> array(
					'title' => esc_html__( 'Open sidemenu', 'alliance' ),
					'desc'  => wp_kses_data( __( 'By default sidemenu will be open on desktop after page load. Page content will be shift to the right', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'menu_side' => array( 'left', 'right' ),
					),
					'std'   => 1,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => ! alliance_exists_trx_addons() ? 'hidden' : 'switch',
				), 					
				'menu_side_icons'               => array(
					'title'      => esc_html__( 'Iconed sidemenu', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Get icons from anchors and display them in the sidemenu, or mark sidemenu items with simple dots', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'menu_side' => array( 'left', 'right' ),
					),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'menu_side_stretch'             => array(
					'title'      => esc_html__( 'Stretch sidemenu', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Stretch sidemenu to window height (if menu items number >= 5)', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'menu_side' => array( 'left', 'right' ),
						'menu_side_icons' => array( 1 )
					),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'menu_mobile_fullscreen'        => array(
					'title' => esc_html__( 'Mobile menu fullscreen', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Display mobile and side menus on full screen (if checked) or slide narrow menu from the left or from the right side (if not checked)', 'alliance' ) ),
					'std'   => 0,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				'menu_mobile_search'           => array(
					'title' => esc_html__( 'Search field in the Mobile menu', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Add a search field at the bottom of the Mobile menu', 'alliance' ) ),
					'std'   => 0,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				'menu_mobile_socials'          => array(
					'title' => esc_html__( 'Social icons in the Mobile menu', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Add social icons at the bottom of the Mobile menu', 'alliance' ) ),
					'std'   => 0,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				'remove_margins'                => array(
					'title'    => esc_html__( 'Page margins', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Add margins above and below the content area', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),					
					'dependency' => array(
						'menu_side' => array( 'hide' ),
					),
					'refresh'  => false,
					'std'      => 0,
					'options'  => alliance_get_list_remove_margins(),
					'type'     => 'choice',
				),	

				'general_sidebar_info'          => array(
					'title' => esc_html__( 'Sidebar', 'alliance' ),
					'desc'  => '',
					'demo'  => true,
					'type'  => 'info',
				),
				'sidebar_position'              => array(
					'title'    => esc_html__( 'Sidebar position', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position to show sidebar', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'      => 'right',
					'options'  => array(),
					'qsetup'   => esc_html__( 'General', 'alliance' ),
					'demo'      => true,
					'type'     => 'choice',
				),
				'sidebar_position_ss'       => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'alliance' ),
					'desc'     => wp_kses_data( __( "Select position to move sidebar (if it's not hidden) on the small screen - above or below the content", 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_ss_single'
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'      => 'below',
					'qsetup'   => esc_html__( 'General', 'alliance' ),
					'options'  => array(),
					'type'     => 'hidden',
				),
				'sidebar_type'              => array(
					'title'    => esc_html__( 'Sidebar style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => alliance_get_list_header_footer_types(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'sidebar_style'                 => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type' => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets'               => array(
					'title'      => esc_html__( 'Sidebar widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_widgets_single'
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type'     => array( 'default')
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'alliance' ),
					'type'       => 'select',
				),
				'sidebar_width'                 => array(
					'title'      => esc_html__( 'Sidebar width', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width', 'alliance' ) ),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'        => alliance_theme_defaults( 'sidebar_width' ),
					'min'        => 150,
					'max'        => 500,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar_width', // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ALLIANCE_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'sidebar_gap'                   => array(
					'title'      => esc_html__( 'Sidebar gap', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'alliance' ) ),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'        => alliance_theme_defaults( 'sidebar_gap' ),
					'min'        => 0,
					'max'        => 100,
					'step'       => 1,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar_gap',  // SASS variable's name to preview changes 'on fly'
					'pro_only'   => ALLIANCE_THEME_FREE,
					'demo'       => true,
					'type'       => 'slider',
				),
				'expand_content'                => array(
					'title'   => esc_html__( 'Content width', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'alliance' ) ),
					'refresh' => false,
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'expand_content_single'
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'options' => alliance_get_list_expand_content(),
					'std'     => 'expand',
					'type'    => 'hidden',
				),

				'general_widgets_info'          => array(
					'title' => esc_html__( 'Additional widgets', 'alliance' ),
					'desc'  => '',
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				'widgets_above_page'            => array(
					'title'    => esc_html__( 'Widgets at the top of the page', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Widgets', 'alliance' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'widgets_above_content'         => array(
					'title'    => esc_html__( 'Widgets above the content', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Widgets', 'alliance' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'widgets_below_content'         => array(
					'title'    => esc_html__( 'Widgets below the content', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Widgets', 'alliance' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'widgets_below_page'            => array(
					'title'    => esc_html__( 'Widgets at the bottom of the page', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Widgets', 'alliance' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),

				'general_effects_info'          => array(
					'title' => esc_html__( 'Design & Effects', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'border_radius'                 => array(
					'title'      => esc_html__( 'Border radius', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Specify the border radius of the form fields and buttons in pixels', 'alliance' ) ),
					'std'        => alliance_theme_defaults( 'rad' ),
					'min'        => 0,
					'max'        => 30,
					'step'       => 1,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'rad',      // SASS name to preview changes 'on fly'
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'slider',
				),

				'general_misc_info'             => array(
					'title' => esc_html__( 'Miscellaneous', 'alliance' ),
					'desc'  => '',
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'info',
				),
				'seo_snippets'                  => array(
					'title' => esc_html__( 'SEO snippets', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Add structured data markup to the single posts and pages', 'alliance' ) ),
					'std'   => 0,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'switch',
				),
				'privacy_text' => array(
					"title" => esc_html__("Text with Privacy Policy link", 'alliance'),
					"desc"  => wp_kses_data( __("Specify text with Privacy Policy link for the checkbox 'I agree ...'", 'alliance') ),
					"std"   => wp_kses( __( 'I agree that my submitted data is being collected and stored.', 'alliance'), 'alliance_kses_content' ),
					"type"  => "textarea"
				),



				// 'Header'
				//---------------------------------------------
				'header'                        => array(
					'title'    => esc_html__( 'Header', 'alliance' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 30,
					'icon'     => 'icon-header',
					'type'     => 'section',
				),

				'header_style_info'             => array(
					'title' => esc_html__( 'Header style', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type'                   => array(
					'title'    => esc_html__( 'Header style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'std'      => 'default',
					'options'  => alliance_get_list_header_footer_types(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'header_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'dependency' => array(
						'header_type' => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				'header_position'               => array(
					'title'    => esc_html__( 'Header position', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select site header position', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'header_fullheight'             => array(
					'title'    => esc_html__( 'Header fullheight', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'std'      => 0,
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'header_wide'                   => array(
					'title'      => esc_html__( 'Header fullwidth', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'std'        => 1,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_zoom'                   => array(
					'title'   => esc_html__( 'Header title zoom', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Zoom the header title. 1 - original size', 'alliance' ) ),
					'std'     => 1,
					'min'     => 0.2,
					'max'     => 2,
					'step'    => 0.1,
					'show_value' => true,
					'refresh' => false,
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'slider',
				),		

				'header_widgets_info'           => array(
					'title' => esc_html__( 'Header widgets', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Here you can place a widget slider, advertising banners, etc.', 'alliance' ) ),
					'type'  => 'hidden',
				),
				'header_widgets'                => array(
					'title'    => esc_html__( 'Header widgets', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select set of widgets to show in the header on each page', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'alliance' ),
						'desc'    => wp_kses_data( __( 'Select set of widgets to show in the header on this page', 'alliance' ) ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => 'hidden',
				),
				'header_columns'                => array(
					'title'      => esc_html__( 'Header columns', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the Header. If 0 - autodetect by the widgets count', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'dependency' => array(
						'header_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => alliance_get_list_range( 0, 6 ),
					'type'       => 'hidden',
				),

				'header_image_info'             => array(
					'title' => esc_html__( 'Header image', 'alliance' ),
					'desc'  => '',
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				'header_image_override'         => array(
					'title'    => esc_html__( 'Header image override', 'alliance' ),
					'desc'     => wp_kses_data( __( "Allow overriding the header image with a featured image of the page, post, product, etc.", 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'std'      => 0,
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),

				'header_mobile_info'            => array(
					'title'      => esc_html__( 'Mobile header', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Configure the mobile version of the header', 'alliance' ) ),
					'priority'   => 500,
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_mobile_enabled'         => array(
					'title'      => esc_html__( 'Enable the mobile header', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Use the mobile version of the header (if checked) or relayout the current header on mobile devices', 'alliance' ) ),
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_mobile_additional_info' => array(
					'title'      => esc_html__( 'Additional info', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Additional info to show at the top of the mobile header', 'alliance' ) ),
					'std'        => '',
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'refresh'    => false,
					'teeny'      => false,
					'rows'       => 20,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_mobile_hide_info'       => array(
					'title'      => esc_html__( 'Hide additional info', 'alliance' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_mobile_hide_logo'       => array(
					'title'      => esc_html__( 'Hide logo', 'alliance' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_mobile_hide_login'      => array(
					'title'      => esc_html__( 'Hide login/logout', 'alliance' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_mobile_hide_search'     => array(
					'title'      => esc_html__( 'Hide search', 'alliance' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'header_mobile_hide_cart'       => array(
					'title'      => esc_html__( 'Hide cart', 'alliance' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden', //! function_exists( 'alliance_exists_woocommerce' ) || ! alliance_exists_woocommerce() ? 'hidden' : 'switch',
				),



				// 'Footer'
				//---------------------------------------------
				'footer'                        => array(
					'title'    => esc_html__( 'Footer', 'alliance' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 50,
					'icon'     => 'icon-footer',
					'type'     => 'section',
				),
				'footer_type'                   => array(
					'title'    => esc_html__( 'Footer style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Footer', 'alliance' ),
					),
					'std'      => 'default',
					'options'  => alliance_get_list_header_footer_types(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'footer_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Footer', 'alliance' ),
					),
					'dependency' => array(
						'footer_type' => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets'                => array(
					'title'      => esc_html__( 'Footer widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Footer', 'alliance' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns'                => array(
					'title'      => esc_html__( 'Footer columns', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Footer', 'alliance' ),
					),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'footer_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => alliance_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				'footer_wide'                   => array(
					'title'      => esc_html__( 'Footer fullwidth', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page,post,product',
						'section' => esc_html__( 'Footer', 'alliance' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'hidden',
				),
				'logo_in_footer'                => array(
					'title'      => esc_html__( 'Show logo', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Show logo in the footer', 'alliance' ) ),
					'refresh'    => false,
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'hidden',
				),
				'logo_footer'                   => array(
					'title'      => esc_html__( 'Logo for footer', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo to display it in the footer', 'alliance' ) ),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'logo_in_footer' => array( 1 ),
					),
					'std'        => '',
					'type'       => 'hidden',
				),
				'logo_footer_retina'            => array(
					'title'      => esc_html__( 'Logo for footer (Retina)', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload logo for the footer area used on Retina displays (if empty - use default logo from the field above)', 'alliance' ) ),
					'dependency' => array(
						'footer_type'         => array( 'default' ),
						'logo_in_footer'      => array( 1 ),
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'socials_in_footer'             => array(
					'title'      => esc_html__( 'Show social icons', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Show social icons in the footer (under logo or footer widgets)', 'alliance' ) ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'hidden' //! alliance_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'copyright'                     => array(
					'title'      => esc_html__( 'Copyright', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Copyright text in the footer. Use {Y} to insert current year and press "Enter" to create a new line', 'alliance' ) ),
					'translate'  => true,
					'std'        => esc_html__( 'Copyright &copy; {Y} by AncoraThemes. All rights reserved.', 'alliance' ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'refresh'    => false,
					'type'       => 'textarea',
				),



				// 'Mobile version'
				//---------------------------------------------
				'mobile'                        => array(
					'title'    => esc_html__( 'Mobile', 'alliance' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 55,
					'icon'     => 'icon-smartphone',
					'type'     => 'hidden',
				),

				'mobile_header_info'            => array(
					'title' => esc_html__( 'Header on the mobile device', 'alliance' ),
					'desc'  => '',
					'type'  => 'hidden',
				),
				'header_type_mobile'            => array(
					'title'    => esc_html__( 'Header style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose the header to be used on mobile devices: the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_header_footer_types( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden' // ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'header_style_mobile'           => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						'header_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'hidden',
				),
				'header_position_mobile'        => array(
					'title'    => esc_html__( 'Header position', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),

				'mobile_sidebar_info'           => array(
					'title' => esc_html__( 'Sidebar on the mobile device', 'alliance' ),
					'desc'  => '',
					'type'  => 'hidden',
				),
				'sidebar_position_mobile'       => array(
					'title'    => esc_html__( 'Sidebar position on mobile', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select sidebar position on mobile devices', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => 'hidden',
				),
				'sidebar_type_mobile'           => array(
					'title'    => esc_html__( 'Sidebar style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
					),
					'std'      => 'inherit',
					'options'  => alliance_get_list_header_footer_types( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'sidebar_style_mobile'          => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
						'sidebar_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'hidden',
				),
				'sidebar_widgets_mobile'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on mobile devices', 'alliance' ) ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
						'sidebar_type_mobile' => array( 'default' )
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'hidden',
				),
				'expand_content_mobile'         => array(
					'title'   => esc_html__( 'Content width', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden on mobile devices', 'alliance' ) ),
					'refresh' => false,
					'dependency' => array(
						'sidebar_position_mobile' => array( 'hide', 'inherit' ),
					),
					'std'     => 'inherit',
					'options' => alliance_get_list_expand_content( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),

				'mobile_footer_info'           => array(
					'title' => esc_html__( 'Footer on the mobile device', 'alliance' ),
					'desc'  => '',
					'type'  => 'hidden',
				),
				'footer_type_mobile'           => array(
					'title'    => esc_html__( 'Footer style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use on mobile devices: the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_header_footer_types( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'footer_style_mobile'          => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						'footer_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'hidden',
				),
				'footer_widgets_mobile'        => array(
					'title'      => esc_html__( 'Footer widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'alliance' ) ),
					'dependency' => array(
						'footer_type_mobile' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'hidden',
				),
				'footer_columns_mobile'        => array(
					'title'      => esc_html__( 'Footer columns', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'alliance' ) ),
					'dependency' => array(
						'footer_type_mobile'    => array( 'default' ),
						'footer_widgets_mobile' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => alliance_get_list_range( 0, 6 ),
					'type'       => 'hidden',
				),



				// 'Blog'
				//---------------------------------------------
				'blog'                          => array(
					'title'    => esc_html__( 'Blog', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Options of the the blog archive', 'alliance' ) ),
					'priority' => 70,
					'icon'     => 'icon-blog',
					'type'     => 'panel',
				),


				// Blog - Posts page
				//---------------------------------------------
				'blog_general'                  => array(
					'title' => esc_html__( 'Posts page', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Style and components of the blog archive', 'alliance' ) ),
					'icon'  => 'icon-posts-page',
					'type'  => 'section',
				),
				'blog_general_info'             => array(
					'title'  => esc_html__( 'Posts page settings', 'alliance' ),
					'desc'   => wp_kses_data( __( 'Customize the blog archive: post layout, header and footer style, sidebar position, etc.', 'alliance' ) ),
					'qsetup' => esc_html__( 'General', 'alliance' ),
					'type'   => 'info',
				),
				'blog_style'                    => array(
					'title'      => esc_html__( 'Blog style', 'alliance' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'std'        => 'excerpt_1',
					'qsetup'     => esc_html__( 'General', 'alliance' ),
					'options'    => array(),
					'type'       => 'choice',
				),
				'first_post_large'              => array(
					'title'      => esc_html__( 'Large first post', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Make your first post stand out by making it bigger', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'blog_style' => array( 'classic', 'masonry' ),
					),
					'std'        => 0,
					'type'       => 'hidden',
				),
				'blog_content'                  => array(
					'title'      => esc_html__( 'Posts content', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Display either post excerpts or the full post content', 'alliance' ) ),
					'std'        => 'excerpt',
					'dependency' => array(
						'blog_style' => array( 'excerpt' ),
					),
					'options'    => alliance_get_list_blog_contents(),
					'type'       => 'hidden',
				),
				'excerpt_length'                => array(
					'title'      => esc_html__( 'Excerpt length', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'blog_style'   => array( 'excerpt', 'classic', 'band' ),
						'blog_content' => array( 'excerpt' ),
					),
					'std'        => 55,
					'type'       => 'text',
				),
				'blog_columns'                  => array(
					'title'   => esc_html__( 'Blog columns', 'alliance' ),
					'desc'    => wp_kses_data( __( 'How many columns should be used in the blog archive (from 2 to 4)?', 'alliance' ) ),
					'std'     => 2,
					'options' => alliance_get_list_range( 2, 4 ),
					'type'    => 'hidden',      // This options is available and must be overriden only for some modes (for example, 'shop')
				),
				'post_type'                     => array(
					'title'      => esc_html__( 'Post type', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select post type to show in the blog archive', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'linked'     => 'parent_cat',
					'refresh'    => false,
					'hidden'     => true,
					'std'        => 'post',
					'options'    => array(),
					'type'       => 'select',
				),
				'parent_cat'                    => array(
					'title'      => esc_html__( 'Category to show', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select category to show in the blog archive', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'refresh'    => false,
					'hidden'     => true,
					'std'        => '0',
					'options'    => array(),
					'type'       => 'select',
				),
				'posts_per_page'                => array(
					'title'      => esc_html__( 'Posts per page', 'alliance' ),
					'desc'       => wp_kses_data( __( 'How many posts will be displayed on this page', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => '',
					'type'       => 'text',
				),
				'blog_pagination'               => array(
					'title'      => esc_html__( 'Pagination style', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'        => 'pages',
					'qsetup'     => esc_html__( 'General', 'alliance' ),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'options'    => alliance_get_list_blog_paginations(),
					'type'       => 'choice',
				),
				'blog_animation'                => array(
					'title'      => esc_html__( 'Post animation', 'alliance' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare'                                  => 'or',
						'#page_template'                           => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'std'        => 'none',
					'options'    => array(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'disable_animation_on_mobile'   => array(
					'title'      => esc_html__( 'Disable animation on mobile', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Disable any posts animation on mobile devices', 'alliance' ) ),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'show_filters'                  => array(
					'title'      => esc_html__( 'Show filters', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Show categories as tabs to filter posts', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare'                                  => 'or',
						'#page_template'                           => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'video_in_popup'                => array(
					'title'      => esc_html__( 'Open video in the popup on a blog archive', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Open the video from posts in the popup (if plugin "ThemeREX Addons" is installed) or play the video instead the cover image', 'alliance' ) ),
					'std'        => 1,
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare'                                  => 'or',
						'#page_template'                           => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'type'       => 'switch',
				),
				'open_full_post_in_blog'        => array(
					'title'      => esc_html__( 'Open full post in blog', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Allow to open the full version of the post directly in the blog feed. Attention! Applies only to 1 column layouts!', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'        => 0,
					'type'       => 'hidden',
				),
				'open_full_post_hide_author'    => array(
					'title'      => esc_html__( 'Hide author bio', 'alliance' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when open the full version of the post directly in the blog feed.", 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'open_full_post_hide_related'   => array(
					'title'      => esc_html__( 'Hide related posts', 'alliance' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when open the full version of the post directly in the blog feed.", 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),

				'blog_header_info'              => array(
					'title' => esc_html__( 'Header', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_blog'              => array(
					'title'    => esc_html__( 'Header style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_header_footer_types( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'radio',
				),
				'header_style_blog'             => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						'header_type_blog' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_blog'          => array(
					'title'    => esc_html__( 'Header position', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'header_fullheight_blog'        => array(
					'title'    => esc_html__( 'Header fullheight', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_checkbox_values( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'header_wide_blog'              => array(
					'title'      => esc_html__( 'Header fullwidth', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'alliance' ) ),
					'dependency' => array(
						'header_type_blog' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => alliance_get_list_checkbox_values( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),

				'blog_sidebar_info'             => array(
					'title' => esc_html__( 'Sidebar', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_blog'         => array(
					'title'   => esc_html__( 'Sidebar position', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'alliance' ) ),
					'std'     => 'inherit',
					'options' => array(),
					'qsetup'     => esc_html__( 'General', 'alliance' ),
					'type'    => 'choice',
				),
				'sidebar_position_ss_blog'  => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the small screen - above or below the content', 'alliance' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'inherit',
					'qsetup'   => esc_html__( 'General', 'alliance' ),
					'options'  => array(),
					'type'     => 'hidden',
				),
				'sidebar_type_blog'           => array(
					'title'    => esc_html__( 'Sidebar style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => alliance_get_list_header_footer_types(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'sidebar_style_blog'            => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_blog'          => array(
					'title'      => esc_html__( 'Sidebar widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'alliance' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'alliance' ),
					'type'       => 'select',
				),
				'expand_content_blog'           => array(
					'title'   => esc_html__( 'Content width', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'alliance' ) ),
					'refresh' => false,
					'std'     => 'expand',
					'options' => alliance_get_list_expand_content( true ),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),

				'blog_widgets_info'             => array(
					'title' => esc_html__( 'Additional widgets', 'alliance' ),
					'desc'  => '',
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				'widgets_above_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				'widgets_above_content_blog'    => array(
					'title'   => esc_html__( 'Widgets above the content', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				'widgets_below_content_blog'    => array(
					'title'   => esc_html__( 'Widgets below the content', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				'widgets_below_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),

				'blog_advanced_info'            => array(
					'title' => esc_html__( 'Advanced settings', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'no_image'                      => array(
					'title' => esc_html__( 'Image placeholder', 'alliance' ),
					'desc'  => wp_kses_data( __( "Select or upload a placeholder image for posts without a featured image. Placeholder is used exclusively on the blog stream page (and not on single post pages), and only in those styles, where omitting a featured image would be inappropriate.", 'alliance' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'sticky_style'                  => array(
					'title'   => esc_html__( 'Sticky posts style', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select style of the sticky posts output', 'alliance' ) ),
					'std'     => 'inherit',
					'options' => array(
						'inherit' => esc_html__( 'Decorated posts', 'alliance' ),
						'columns' => esc_html__( 'Mini-cards', 'alliance' ),
					),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				'meta_parts'                    => array(
					'title'      => esc_html__( 'Post meta', 'alliance' ),
					'desc'       => wp_kses_data( __( "If your blog page is created using the 'Blog archive' page template, set up the 'Post Meta' settings in the 'Theme Options' section of that page. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'alliance' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|modified=0|views=0|likes=1|comments=1|author=0|share=0|edit=0',
					'options'    => alliance_get_list_meta_parts(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'checklist',
				),
				'time_diff_before'              => array(
					'title' => esc_html__( 'Easy readable date format', 'alliance' ),
					'desc'  => wp_kses_data( __( "For how many days to show the easy-readable date format (e.g. '3 days ago') instead of the standard publication date", 'alliance' ) ),
					'std'   => 5,
					'type'  => 'text',
				),
				'use_blog_archive_pages'        => array(
					'title'      => esc_html__( 'Use "Blog Archive" page settings on the post list', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Apply options and content of pages created with the template "Blog Archive" for some type of posts and / or taxonomy when viewing feeds of posts of this type and taxonomy.', 'alliance' ) ),
					'std'        => 0,
					'type'       => 'switch',
				),


				// Blog - Single posts
				//---------------------------------------------
				'blog_single'                   => array(
					'title' => esc_html__( 'Single posts', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Settings of the single post', 'alliance' ) ),
					'icon'  => 'icon-single-post',
					'type'  => 'section',
				),

				'blog_single_info'       => array(
					'title' => esc_html__( 'Single posts', 'alliance' ),
					'desc'   => wp_kses_data( __( 'Customize the single post: content  layout, header and footer styles, sidebar position, meta elements, etc.', 'alliance' ) ),
					'type'  => 'info',
				),
				'blog_single_header_info'       => array(
					'title' => esc_html__( 'Header', 'alliance' ),
					'desc'   => '',
					'type'  => 'info',
				),
				'header_type_single'            => array(
					'title'    => esc_html__( 'Header style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_header_footer_types( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'radio',
				),
				'header_style_single'           => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						'header_type_single' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_single'        => array(
					'title'    => esc_html__( 'Header position', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'header_fullheight_single'      => array(
					'title'    => esc_html__( 'Header fullheight', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_checkbox_values( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				'header_wide_single'            => array(
					'title'      => esc_html__( 'Header fullwidth', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'alliance' ) ),
					'dependency' => array(
						'header_type_single' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => alliance_get_list_checkbox_values( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),

				'blog_single_sidebar_info'      => array(
					'title' => esc_html__( 'Sidebar', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_single'       => array(
					'title'   => esc_html__( 'Sidebar position', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar on the single posts', 'alliance' ) ),
					'std'     => 'inherit',
					'override'   => array(
						'mode'    => 'post,product',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'options' => array(),
					'type'    => 'choice',
				),
				'sidebar_position_ss_single'    => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the single posts on the small screen - above or below the content', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post,product',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'below',
					'options'  => array(),
					'type'     => 'hidden',
				),
				'sidebar_type_single'           => array(
					'title'    => esc_html__( 'Sidebar style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'post,product',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => alliance_get_list_header_footer_types(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'sidebar_style_single'            => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'override'   => array(
						'mode'    => 'post,product',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_single'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on the single posts', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'post,product',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_single'         => array(
					'title'   => esc_html__( 'Content width', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Content width on the single posts if the sidebar is hidden. Attention! "Narrow" width is only available for posts. For all other post types (Team, Services, etc.), it is equivalent to "Normal"', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'post,product',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'refresh' => false,
					'std'     => 'normal',
					'options' => alliance_get_list_expand_content( true, true ),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),

				'blog_single_title_info'        => array(
					'title' => esc_html__( 'Featured image and title', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'single_style'                  => array(
					'title'      => esc_html__( 'Single style', 'alliance' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'        => 'style-1',
					'qsetup'     => esc_html__( 'General', 'alliance' ),
					'options'    => array(),
					'type'       => 'hidden',
				),
				'single_parallax'               => array(
					'title'      => esc_html__( 'Parallax speed', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Speed for shifting the image while scrolling the page. If 0, the effect is not applied.', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'        => 0,
					'min'        => -50,
					'max'        => 50,
					'step'       => 1,
					'refresh'    => false,
					'show_value' => true,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'post_subtitle'                 => array(
					'title' => esc_html__( 'Post subtitle', 'alliance' ),
					'desc'  => wp_kses_data( __( "Specify post subtitle to display it under the post title.", 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'   => '',
					'hidden' => true,
					'type'  => 'text',
				),
				'show_post_meta'                => array(
					'title' => esc_html__( 'Show post meta', 'alliance' ),
					'desc'  => wp_kses_data( __( "Display block with post's meta: date, categories, counters, etc.", 'alliance' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'meta_parts_single'             => array(
					'title'      => esc_html__( 'Post meta', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Meta parts for single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'alliance' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'alliance' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|modified=0|views=0|likes=1|comments=1|author=0|share=0|edit=0',
					'options'    => alliance_get_list_meta_parts(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'checklist',
				),
				'share_position'                 => array(
					'title'      => esc_html__( 'Share links position', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select one or more positions to show Share links on single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'alliance' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'dir'        => 'vertical',
					'std'        => 'top=1|left=0|bottom=0',
					'options'    => alliance_get_list_share_links_positions(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'share_fixed'                   => array(
					'title' => esc_html__( 'Share links fixed', 'alliance' ),
					'desc'  => wp_kses_data( __( "Fix share links when a document scrolled down", 'alliance' ) ),
					'dependency' => array(
						'share_position[left]' => array( 1 ),
					),
					'std'   => 1,
					'type'  => 'hidden',
				),
				'show_author_info'              => array(
					'title' => esc_html__( 'Show author info', 'alliance' ),
					'desc'  => wp_kses_data( __( "Display block with information about post's author", 'alliance' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'show_comments_button'          => array(
					'title' => esc_html__( 'Show comments button', 'alliance' ),
					'desc'  => wp_kses_data( __( "Display button to show/hide comments block", 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'   => 0,
					'type'  => 'switch',
				),
				'show_comments'                 => array(
					'title'   => esc_html__( 'Comments block', 'alliance' ),
					'desc'    => wp_kses_data( __( "Select initial state of the comments block", 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'options' => alliance_get_list_visiblehidden(),
					'dependency' => array(
						'show_comments_button' => array( 1 ),
					),
					'std'     => 'hidden',
					'type'    => 'radio',
				),

				'blog_single_related_info'      => array(
					'title' => esc_html__( 'Related posts', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'show_related_posts'            => array(
					'title'    => esc_html__( 'Show related posts', 'alliance' ),
					'desc'     => wp_kses_data( __( "Show 'Related posts' section on single post pages", 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'std'      => 0,
					'type'     => 'switch',
				),
				'related_style'                 => array(
					'title'      => esc_html__( 'Related posts style', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select the style of the related posts output', 'alliance' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'classic',
					'options'    => array(
						'classic' => esc_html__( 'Classic', 'alliance' ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				'related_position'              => array(
					'title'      => esc_html__( 'Related posts position', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select position to display the related posts', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'below_content',
					'options'    => array (
						'inside'        => esc_html__( 'Inside the content (fullwidth)', 'alliance' ),
						'inside_left'   => esc_html__( 'At left side of the content', 'alliance' ),
						'inside_right'  => esc_html__( 'At right side of the content', 'alliance' ),
						'below_content' => esc_html__( 'After the content', 'alliance' ),
						'below_page'    => esc_html__( 'After the content & sidebar', 'alliance' ),
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				'related_position_inside'       => array(
					'title'      => esc_html__( 'Before # paragraph', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Before what paragraph should related posts appear? If 0 - randomly.', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'inside_left', 'inside_right' ),
					),
					'std'        => 2,
					'options'    => alliance_get_list_range( 0, 9 ),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				'related_posts'                 => array(
					'title'      => esc_html__( 'Related posts', 'alliance' ),
					'desc'       => wp_kses_data( __( 'How many related posts should be displayed in the single post?', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 2,
					'min'        => 1,
					'max'        => 9,
					'show_value' => true,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'slider',
				),
				'related_columns'               => array(
					'title'      => esc_html__( 'Related columns', 'alliance' ),
					'desc'       => wp_kses_data( __( 'How many columns should be used to output related posts on the single post page?', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'below_content', 'below_page' ),
					),
					'std'        => 2,
					'min'        => 1,
					'max'        => 6,
					'show_value' => true,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'slider',
				),
				'related_slider'                => array(
					'title'      => esc_html__( 'Use slider layout', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Use slider layout in case related posts count is more than columns count', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'switch',
				),
				'related_slider_controls'       => array(
					'title'      => esc_html__( 'Slider controls', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Show arrows in the slider', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'none',
					'options'    => array(
						'none'    => esc_html__('None', 'alliance'),
						'side'    => esc_html__('Side', 'alliance'),
						'top'     => esc_html__('Top', 'alliance'),
						'bottom'  => esc_html__('Bottom', 'alliance')
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				'related_slider_pagination'       => array(
					'title'      => esc_html__( 'Slider pagination', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Show bullets after the slider', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'bottom',
					'options'    => array(
						'none'    => esc_html__('None', 'alliance'),
						'bottom'  => esc_html__('Bottom', 'alliance')
					),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'radio',
				),
				'related_slider_space'          => array(
					'title'      => esc_html__( 'Space between slides', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Space between slides in the related posts slider (in pixels)', 'alliance' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 30,
					'min'        => 0,
					'max'        => 100,
					'show_value' => true,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'slider',
				),
				'posts_navigation_info'      => array(
					'title' => esc_html__( 'Post navigation', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'posts_navigation'           => array(
					'title'   => esc_html__( 'Show post navigation', 'alliance' ),
					'desc'    => wp_kses_data( __( "Display post navigation on single post pages or load the next post automatically after the content of the current article.", 'alliance' ) ),
					'std'     => 'none',
					'options' => array(
						'none'   => esc_html__('None', 'alliance'),
						'links'  => esc_html__('Prev/Next links', 'alliance'),
					//	'scroll' => esc_html__('Autoload next post', 'alliance')
					),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'radio',
				),
				'posts_navigation_fixed'     => array(
					'title'      => esc_html__( 'Fixed post navigation', 'alliance' ),
					'desc'       => wp_kses_data( __( "Fix the position of post navigation buttons on desktop. Display them on either side of post content.", 'alliance' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'links' ),
					),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'posts_navigation_scroll_same_cat'     => array(
					'title'      => esc_html__( 'Next post from same category', 'alliance' ),
					'desc'       => wp_kses_data( __( "Load next post from the same category or from any category.", 'alliance' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 1,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'posts_navigation_scroll_which_block'  => array(
					'title'   => esc_html__( 'Which block to load?', 'alliance' ),
					'desc'    => wp_kses_data( __( "Load only the content of the next article or the article and sidebar together.", 'alliance' ) )
								. '<br>'
								. wp_kses_data( __( "Attention! If you override sidebar position or content width on single posts (e.g. the sidebar is displayed on some posts and hidden on others), please don’t use the 'Full post' option to prevent improper content positioning.", 'alliance' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'     => 'article',
					'options' => array(
						'article' => array(
										'title' => esc_html__( 'Only content', 'alliance' ),
										'icon'  => 'images/theme-options/posts-navigation-scroll-which-block/article.png',
									),
						'wrapper' => array(
										'title' => esc_html__( 'Full post', 'alliance' ),
										'icon'  => 'images/theme-options/posts-navigation-scroll-which-block/wrapper.png',
									),
					),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				'posts_navigation_scroll_hide_author'  => array(
					'title'      => esc_html__( 'Hide author bio', 'alliance' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when infinite scroll is used.", 'alliance' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'posts_navigation_scroll_hide_related'  => array(
					'title'      => esc_html__( 'Hide related posts', 'alliance' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when infinite scroll is used.", 'alliance' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'hidden',
				),
				'posts_navigation_scroll_hide_comments' => array(
					'title'      => esc_html__( 'Hide comments', 'alliance' ),
					'desc'       => wp_kses_data( __( "Hide comments after post content when infinite scroll is used.", 'alliance' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 1,
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'switch',
				),
				'blog_end'                      => array(
					'type' => 'hidden',
				),



				// 'Colors'
				//---------------------------------------------
				'panel_colors'                  => array(
					'title'    => esc_html__( 'Colors', 'alliance' ),
					'desc'     => '',
					'priority' => 300,
					'icon'     => 'icon-customizer',
					'demo'     => true,
					'type'     => 'section',
				),

				'color_preset_info'             => array(
					'title' => esc_html__( 'Color preset', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Prepared sets of colors for an instant transformation of your site. After selecting a preset, you can edit any colors using the color scheme editor below.', 'alliance' ) ),
					'demo'  => true,
					'type'  => 'hidden',
				),
				'color_preset'                  => array(
					'title'   => '',
					'desc'    => '',
					'std'     => '',
					'refresh' => false,
					'demo'    => true,
					'options' => array(),
					'type'    => 'hidden',
				),

				'color_scheme_editor_info'      => array(
					'title' => esc_html__( 'Color scheme editor', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Select a color scheme to modify. Attention! Only sections of the site with the selected color scheme will be affected by the changes.', 'alliance' ) ),
					'demo'  => true,
					'type'  => 'info',
				),
				'scheme_storage'                => array(
					'title'       => '',
					'desc'        => '',
					'std'         => '$alliance_get_scheme_storage',
					'refresh'     => false,
					'colorpicker' => 'spectrum', //'tiny',
					'demo'        => true,
					'type'        => 'scheme_editor',
				),

				'color_schemes_info'            => array(
					'title'  => esc_html__( 'Color scheme assignment', 'alliance' ),
					'desc'   => wp_kses_data( __( 'Color schemes for various parts of the site. "Inherit" means that this block uses the main color scheme from the first parameter - Site Color Scheme.', 'alliance' ) ),
					'hidden' => $hide_schemes,
					'demo'   => true,
					'type'   => 'info',
				),
				'color_scheme'                  => array(
					'title'    => esc_html__( 'Site Color Scheme', 'alliance' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Colors', 'alliance' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
					'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'select',
				),
				'header_scheme'                 => array(
					'title'    => esc_html__( 'Header Color Scheme', 'alliance' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Colors', 'alliance' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'select',
				),
				'menu_scheme'                   => array(
					'title'    => esc_html__( 'Sidemenu Color Scheme', 'alliance' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Colors', 'alliance' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'pro_only' => ALLIANCE_THEME_FREE,
					'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'select',
				),
				'sidebar_scheme'                => array(
					'title'    => esc_html__( 'Sidebar Color Scheme', 'alliance' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Colors', 'alliance' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'select',
				),
				'footer_scheme'                 => array(
					'title'    => esc_html__( 'Footer Color Scheme', 'alliance' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Colors', 'alliance' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'demo'     => true,
					'type'     => $hide_schemes ? 'hidden' : 'select',
				),
				'scheme_switcher'          => array(
					'title' => esc_html__( 'Color scheme switcher', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Add button at the bottom of the page', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Colors', 'alliance' ),
					),
					'std'   => 0,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'switch',
				),
				'invert_logo'          => array(
					'title' => esc_html__( 'Invert logo', 'alliance' ),
					'desc'  => wp_kses_data( __( 'Inverse logo colors on the dark scheme', 'alliance' ) ),
					'dependency' => array(
						'scheme_switcher' => array( 1 ),
					),
					'std'   => 0,
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'switch',
				),


				// Internal options.
				// Attention! Don't change any options in the section below!
				// Huge priority is used to call render this elements after all options!
				'reset_options'                 => array(
					'title'    => '',
					'desc'     => '',
					'std'      => '0',
					'priority' => 10000,
					'type'     => 'hidden',
				),

				'last_option'                   => array(     // Need to manually call action to include Tiny MCE scripts
					'title' => '',
					'desc'  => '',
					'std'   => 1,
					'demo'  => true,
					'type'  => 'hidden',
				),

			)
		);


		// Add parameters for "Category", "Tag", "Author", "Search" to Theme Options
		alliance_storage_set_array_before( 'options', 'blog_single', alliance_options_get_list_blog_options( 'category', esc_html__( 'Category', 'alliance' ), 'icon-category' ) );
		alliance_storage_set_array_before( 'options', 'blog_single', alliance_options_get_list_blog_options( 'tag', esc_html__( 'Tag', 'alliance' ), 'icon-tag-1' ) );
		alliance_storage_set_array_before( 'options', 'blog_single', alliance_options_get_list_blog_options( 'author', esc_html__( 'Author', 'alliance' ), 'icon-resume' ) );
		alliance_storage_set_array_before( 'options', 'blog_single', alliance_options_get_list_blog_options( 'search', esc_html__( 'Search', 'alliance' ), 'icon-search-1' ) );


		// Prepare panel 'Fonts'
		// -------------------------------------------------------------
		$fonts = array(

			// 'Fonts'
			//---------------------------------------------
			'fonts'             => array(
				'title'    => esc_html__( 'Typography', 'alliance' ),
				'desc'     => '',
				'priority' => 200,
				'icon'     => 'icon-font',
				'demo'     => true,
				'type'     => 'panel',
			),

			// Fonts - presets
			'font_preset_font_section'        => array(
				'title' => esc_html__( 'Font presets', 'alliance' ),
				'desc'  => '',
				'demo'  => true,
				'type'  => 'hidden',
			),
			'font_preset_info'   => array(
				'title' => esc_html__( 'Font presets', 'alliance' ),
				'desc'  => wp_kses_data( __( 'Select a font preset to setup all typography parameters at once.', 'alliance' ) ),
				'demo'  => true,
				'type'  => 'hidden',
			),
			'font_preset'       => array(
				'title'   => esc_html__( 'Font preset', 'alliance' ),
				'desc'    => '',
				'std'     => '',
				'refresh' => false,
				'demo'    => true,
				'options' => array(),
				'type'    => 'hidden',
			),

			// Fonts - Load_fonts
			'load_fonts_font_section' => array(
				'title' => esc_html__( 'Load fonts', 'alliance' ),
				'desc'  => wp_kses_data( __( 'Specify fonts to load when theme start. You can use them in the base theme elements: headers, text, menu, links, input fields, etc.', 'alliance' ) ),
				'demo'  => true,
				'type'  => 'section',
			),
			'load_fonts_info'   => array(
				'title' => esc_html__( 'Load fonts', 'alliance' ),
				'desc'  => wp_kses_data( __( 'Press "Reload preview area" button at the top of this panel after the all font parameters are changed.', 'alliance' ) ),
				'demo'  => true,
				'type'  => 'info',
			),
			'load_fonts_subset' => array(
				'title'   => esc_html__( 'Google fonts subsets', 'alliance' ),
				'desc'    => wp_kses_data( __( 'Specify a comma separated list of subsets to be loaded from Google fonts.', 'alliance' ) )
						. wp_kses_data( __( 'Permitted subsets include: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese', 'alliance' ) ),
				'class'   => 'alliance_column-1_4 alliance_new_row',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$alliance_get_load_fonts_subset',
				'type'    => 'text',
			),
		);

		for ( $i = 1; $i <= alliance_get_theme_setting( 'max_load_fonts' ); $i++ ) {
			if ( alliance_get_value_gp( 'page' ) != 'theme_options' ) {
				$fonts[ "load_fonts-{$i}-info" ] = array(
					// Translators: Add font's number - 'Font 1', 'Font 2', etc
					'title' => esc_html( sprintf( __( 'Font %s', 'alliance' ), $i ) ),
					'desc'  => '',
					'demo'  => true,
					'type'  => 'info',
				);
			}
			$fonts[ "load_fonts-{$i}-name" ]   = array(
				'title'   => esc_html__( 'Font name', 'alliance' ),
				'desc'    => '',
				'class'   => 'alliance_column-1_4 alliance_new_row',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$alliance_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-family" ] = array(
				'title'   => esc_html__( 'Fallback fonts', 'alliance' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'A comma-separated list of fallback fonts. Used if the font specified in the previous field is not available. Last in the list, specify the name of the font family: serif, sans-serif, monospace, cursive.', 'alliance' ) )
								. '<br>'
								. wp_kses_data( __( 'For example: Arial, Helvetica, sans-serif', 'alliance' ) )
							: '',
				'class'   => 'alliance_column-1_4',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$alliance_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-link" ] = array(
				'title'   => esc_html__( 'Font URL', 'alliance' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Font URL used only for Adobe fonts. This is URL of the stylesheet for the project with a fonts collection from the site adobe.com', 'alliance' ) )
							: '',
				'class'   => 'alliance_column-1_4',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$alliance_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-styles" ] = array(
				'title'   => esc_html__( 'Font styles', 'alliance' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Font styles used only for Google fonts. This is a list of the font weight and style options for Google fonts CSS API v2.', 'alliance' ) )
								. '<br>'
								. wp_kses_data( __( 'For example, to load normal, normal italic, bold and bold italic fonts, please specify: ital,wght@0:400;0,700;1,400;1,700', 'alliance' ) )
								. '<br>'
								. wp_kses_data( __( 'Attention! Each weight and style option increases download size! Specify only those weight and style options that you plan on using.', 'alliance' ) )
							: '',
				'class'   => 'alliance_column-1_4',
				'refresh' => false,
				'demo'    => true,
				'std'     => '$alliance_get_load_fonts_option',
				'type'    => 'text',
			);
		}
		$fonts['load_fonts_end'] = array(
			'demo' => true,
			'type' => 'section_end',
		);

		// Fonts - H1..6, P, Info, Menu, etc.
		$theme_fonts = alliance_get_theme_fonts();
		foreach ( $theme_fonts as $tag => $v ) {
			$fonts[ "{$tag}_font_section" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'alliance' ), $tag ) ),
/*
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								// Translators: Add tag's name to make description
								: wp_kses_data( sprintf( __( 'Font settings for the "%s" tag.', 'alliance' ), $tag ) ),
*/
				'demo'  => true,
				'type'  => 'section',
			);
			$fonts[ "{$tag}_font_info" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'alliance' ), $tag ) ),
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								: '',
				'demo'  => true,
				'type'  => 'info',
			);
			foreach ( $v as $css_prop => $css_value ) {
				if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
					continue;
				}
				// Skip property 'text-decoration' for the main text
				if ( 'text-decoration' == $css_prop && 'p' == $tag ) {
					continue;
				}

				$options    = '';
				$type       = 'text';
				$load_order = 1;
				$title      = ucfirst( str_replace( '-', ' ', $css_prop ) );
				if ( 'font-family' == $css_prop ) {
					$type       = 'select';
					$options    = array();
					$load_order = 2;        // Load this option's value after all options are loaded (use option 'load_fonts' to build fonts list)
				} elseif ( 'font-weight' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'alliance' ),
						'100'     => esc_html__( '100 (Thin)', 'alliance' ),
						'200'     => esc_html__( '200 (Extra-Light)', 'alliance' ),
						'300'     => esc_html__( '300 (Light)', 'alliance' ),
						'400'     => esc_html__( '400 (Regular)', 'alliance' ),
						'500'     => esc_html__( '500 (Medium)', 'alliance' ),
						'600'     => esc_html__( '600 (Semi-bold)', 'alliance' ),
						'700'     => esc_html__( '700 (Bold)', 'alliance' ),
						'800'     => esc_html__( '800 (Extra-bold)', 'alliance' ),
						'900'     => esc_html__( '900 (Black)', 'alliance' ),
					);
				} elseif ( 'font-style' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'alliance' ),
						'normal'  => esc_html__( 'Normal', 'alliance' ),
						'italic'  => esc_html__( 'Italic', 'alliance' ),
					);
				} elseif ( 'text-decoration' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'      => esc_html__( 'Inherit', 'alliance' ),
						'none'         => esc_html__( 'None', 'alliance' ),
						'underline'    => esc_html__( 'Underline', 'alliance' ),
						'overline'     => esc_html__( 'Overline', 'alliance' ),
						'line-through' => esc_html__( 'Line-through', 'alliance' ),
					);
				} elseif ( 'text-transform' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'    => esc_html__( 'Inherit', 'alliance' ),
						'none'       => esc_html__( 'None', 'alliance' ),
						'uppercase'  => esc_html__( 'Uppercase', 'alliance' ),
						'lowercase'  => esc_html__( 'Lowercase', 'alliance' ),
						'capitalize' => esc_html__( 'Capitalize', 'alliance' ),
					);
				}
				$fonts[ "{$tag}_{$css_prop}" ] = array(
					'title'      => $title,
					'desc'       => '',
					'refresh'    => false,
					'demo'       => true,
					'load_order' => $load_order,
					'std'        => '$alliance_get_theme_fonts_option',
					'options'    => $options,
					'type'       => $type,
				);
			}

			$fonts[ "{$tag}_section_end" ] = array(
				'demo' => true,
				'type' => 'section_end',
			);
		}

		$fonts['fonts_end'] = array(
			'demo' => true,
			'type' => 'panel_end',
		);

		// Add fonts parameters to Theme Options
		alliance_storage_set_array_before( 'options', 'panel_colors', $fonts );

		// Add Header Video if WP version < 4.7
		// -----------------------------------------------------
		if ( ! function_exists( 'get_header_video_url' ) ) {
			alliance_storage_set_array_after(
				'options', 'header_image_override', 'header_video', array(
					'title'    => esc_html__( 'Header video', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select video to use it as background for the header', 'alliance' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'alliance' ),
					),
					'std'      => '',
					'type'     => 'video',
				)
			);
		}

		// Add option 'logo' if WP version < 4.5
		// or 'custom_logo' if current page is not 'Customize'
		// ------------------------------------------------------
		if ( ! function_exists( 'the_custom_logo' ) || ! alliance_check_url( 'customize.php' ) ) {
			alliance_storage_set_array_before(
				'options', 'logo_retina', function_exists( 'the_custom_logo' ) ? 'custom_logo' : 'logo', array(
					'title'    => esc_html__( 'Logo', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select or upload the site logo', 'alliance' ) ),
					'priority' => 60,
					'std'      => '',
					'qsetup'   => esc_html__( 'General', 'alliance' ),
					'type'     => 'image',
				)
			);
		}

	}
}


// Common parameters for some blog modes: categories, tags, archives, author posts, search, etc.
//------------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'alliance_options_get_list_blog_options' ) ) {
	function alliance_options_get_list_blog_options( $mode, $title = '', $icon = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $mode );
		}
		return apply_filters( 'alliance_filter_get_list_blog_options', array(
				"blog_general_{$mode}"           => array(
					'title' => $title,
					// Translators: Add mode name to the description
					'desc'  => wp_kses_data( sprintf( __( "Style and components of the %s posts page", 'alliance' ), $title ) ),
					'icon'  => $icon,
					'type'  => 'section',
				),
				"blog_general_info_{$mode}"      => array(
					// Translators: Add mode name to the title
					'title'  => wp_kses_data( sprintf( __( "%s posts page", 'alliance' ), $title ) ),
					// Translators: Add mode name to the description
					'desc'   => wp_kses_data( sprintf( __( 'Customize %s page: post layout, header and footer styles, sidebar position and widgets, etc.', 'alliance' ), $title ) ),
					'type'   => 'info',
				),
				"blog_style_{$mode}"             => array(
					'title'      => esc_html__( 'Blog style', 'alliance' ),
					'desc'       => '',
					'std'        => 'excerpt_1',
					'options'    => array(),
					'type'       => 'choice',
				),
				"first_post_large_{$mode}"       => array(
					'title'      => esc_html__( 'Large first post', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Make your first post stand out by making it bigger', 'alliance' ) ),
					'std'        => 0,
					'options'    => alliance_get_list_yesno( true ),
					'dependency' => array(
						'blog_style_{$mode}' => array( 'classic', 'masonry' ),
					),
					'type'       => 'hidden',
				),
				"blog_content_{$mode}"           => array(
					'title'      => esc_html__( 'Posts content', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Display either post excerpts or the full post content', 'alliance' ) ),
					'std'        => 'excerpt',
					'dependency' => array(
						"blog_style_{$mode}" => array( 'excerpt' ),
					),
					'options'    => alliance_get_list_blog_contents( true ),
					'type'       => 'hidden',
				),
				"excerpt_length_{$mode}"         => array(
					'title'      => esc_html__( 'Excerpt length', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'alliance' ) ),
					'dependency' => array(
						"blog_style_{$mode}"   => array( 'excerpt', 'classic', 'band' ),
						"blog_content_{$mode}" => array( 'excerpt' ),
					),
					'std'        => 55,
					'type'       => 'text',
				),
				"meta_parts_{$mode}"             => array(
					'title'      => esc_html__( 'Post meta', 'alliance' ),
					'desc'       => wp_kses_data( __( "Set up post meta parts to show in the blog archive. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'alliance' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'alliance' ) ),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|modified=0|views=0|likes=1|comments=1|author=0|share=0|edit=0',
					'options'    => alliance_get_list_meta_parts(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'checklist',
				),
				"blog_pagination_{$mode}"        => array(
					'title'      => esc_html__( 'Pagination style', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'alliance' ) ),
					'std'        => 'pages',
					'options'    => alliance_get_list_blog_paginations( true ),
					'type'       => 'choice',
				),
				"blog_animation_{$mode}"         => array(
					'title'      => esc_html__( 'Post animation', 'alliance' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'alliance' ) ),
					'std'        => 'none',
					'options'    => array(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				"open_full_post_in_blog_{$mode}" => array(
					'title'      => esc_html__( 'Open full post in blog', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Allow to open the full version of the post directly in the posts feed. Attention! Applies only to 1 column layouts!', 'alliance' ) ),
					'std'        => 0,
					'options'    => alliance_get_list_checkbox_values( true ),
					'type'       => 'hidden',
				),

				"blog_header_info_{$mode}"       => array(
					'title' => esc_html__( 'Header', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"header_type_{$mode}"            => array(
					'title'    => esc_html__( 'Header style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_header_footer_types( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'radio',
				),
				"header_style_{$mode}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						"header_type_{$mode}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				"header_position_{$mode}"        => array(
					'title'    => esc_html__( 'Header position', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				"header_fullheight_{$mode}"      => array(
					'title'    => esc_html__( 'Header fullheight', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_checkbox_values( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),
				"header_wide_{$mode}"            => array(
					'title'      => esc_html__( 'Header fullwidth', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'alliance' ) ),
					'dependency' => array(
						"header_type_{$mode}" => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => alliance_get_list_checkbox_values( true ),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => 'hidden',
				),

				"blog_sidebar_info_{$mode}"      => array(
					'title' => esc_html__( 'Sidebar', 'alliance' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"sidebar_position_{$mode}"       => array(
					'title'   => esc_html__( 'Sidebar position', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'alliance' ) ),
					'std'     => 'inherit',
					'options' => array(),
					'type'    => 'choice',
				),
				"sidebar_type_{$mode}"           => array(
					'title'    => esc_html__( 'Sidebar style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => alliance_get_list_header_footer_types(),
					'pro_only' => ALLIANCE_THEME_FREE,
					'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
				),
				"sidebar_style_{$mode}"          => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'custom' ),
					),
					'std'        => '',
					'options'    => array(),
					'type'       => 'select',
				),
				"sidebar_widgets_{$mode}"        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'alliance' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"expand_content_{$mode}"         => array(
					'title'   => esc_html__( 'Content width', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'alliance' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => alliance_get_list_expand_content( true ),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),

				"blog_widgets_info_{$mode}"      => array(
					'title' => esc_html__( 'Additional widgets', 'alliance' ),
					'desc'  => '',
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				"widgets_above_page_{$mode}"     => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"widgets_above_content_{$mode}"  => array(
					'title'   => esc_html__( 'Widgets above the content', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"widgets_below_content_{$mode}"  => array(
					'title'   => esc_html__( 'Widgets below the content', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"widgets_below_page_{$mode}"     => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
			), $mode, $title
		);
	}
}


// Common parameters for CPT
//------------------------------------------------------------------------------------------------------------

// Returns a list of options that can be overridden for CPT
if ( ! function_exists( 'alliance_options_get_list_cpt_options' ) ) {
	function alliance_options_get_list_cpt_options( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'alliance_filter_get_list_cpt_options',
								array_merge(
									alliance_options_get_list_cpt_options_body( $cpt, $title, $mode ),              // Body style options for both: a posts list and a single post
									alliance_options_get_list_cpt_options_header( $cpt, $title, 'list' ),    // Header options for the posts list
									//alliance_options_get_list_cpt_options_header( $cpt, $title, 'single' ),  // Header options for the single post
									alliance_options_get_list_cpt_options_sidebar( $cpt, $title, 'list' ),   // Sidebar options for the posts list
									//alliance_options_get_list_cpt_options_sidebar( $cpt, $title, 'single' ), // Sidebar options for the single post
									//alliance_options_get_list_cpt_options_footer( $cpt, $title ),            // Footer options for both: a posts list and a single post
									alliance_options_get_list_cpt_options_widgets( $cpt, $title )            // Widgets options for both: a posts list and a single post
								),
								$cpt,
								$title
							);
	}
}


// Returns a text description suffix for CPT
if ( ! function_exists( 'alliance_options_get_cpt_description_suffix' ) ) {
	function alliance_options_get_cpt_description_suffix( $title, $mode ) {
		return $mode == 'both'
					// Translators: Add CPT name to the description
					? sprintf( __( 'the %s list and single posts', 'alliance' ), $title )
					: ( $mode == 'list'
						// Translators: Add CPT name to the description
						? sprintf( __( 'the %s list', 'alliance' ), $title )
						// Translators: Add CPT name to the description
						: sprintf( __( 'Single %s posts', 'alliance' ), $title )
						);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Content'
if ( ! function_exists( 'alliance_options_get_list_cpt_options_body' ) ) {
	function alliance_options_get_list_cpt_options_body( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = alliance_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "alliance_filter_get_list_cpt_options_body{$suffix}", array(
				"content_info{$suffix}_{$cpt}"           => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Body style on %s', 'alliance' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Select body style to display %s', 'alliance' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"body_style{$suffix}_{$cpt}"             => array(
					'title'    => esc_html__( 'Body style', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'alliance' ) ),
					'std'      => 'inherit',
					'options'  => alliance_get_list_body_styles( true ),
					'type'     => 'hidden',
				),
				"page_content{$suffix}_{$cpt}"                   => array(
					'title'    => esc_html__( 'Page content', 'alliance' ),
					'desc'     => wp_kses_data( __( 'Choose between the style of your page content. The classic view provides you with the primary content block and a sidebar, while the masonry view positions each block on a separate background.', 'alliance' ) ),	
					'std'      => 'blocks',
					'options'  => array(
						'classic'  => array(
									'title' => esc_html__( 'Classic', 'alliance' ),
									'icon'  => 'images/theme-options/page-content/classic.png',
								),
						'blocks'  => array(
									'title' => esc_html__( 'Blocks', 'alliance' ),
									'icon'  => 'images/theme-options/page-content/blocks.png',
								),
					),
					'type'     => 'choice',
				),				
				"boxed_bg_image{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Boxed bg image', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select or upload image, used as background in the boxed body', 'alliance' ) ),
					'dependency' => array(
						"body_style{$suffix}_{$cpt}" => array( 'boxed' ),
					),
					'std'        => 'inherit',
					'type'       => 'hidden',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Header'
if ( ! function_exists( 'alliance_options_get_list_cpt_options_header' ) ) {
	function alliance_options_get_list_cpt_options_header( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = alliance_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "alliance_filter_get_list_cpt_options_header{$suffix}", array(
				"header_info{$suffix}_{$cpt}"            => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Header on %s', 'alliance' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up header parameters to display %s', 'alliance' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"header_type{$suffix}_{$cpt}"            => array(
					'title'   => esc_html__( 'Header style', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'std'     => 'inherit',
					'options' => alliance_get_list_header_footer_types( true ),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'radio',
				),
				"header_style{$suffix}_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					// Translators: Add CPT name to the description
					'desc'       => wp_kses_data( sprintf( __( 'Select custom layout to display the site header on the %s pages', 'alliance' ), $title ) ),
					'dependency' => array(
						"header_type{$suffix}_{$cpt}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				"header_position{$suffix}_{$cpt}"        => array(
					'title'   => esc_html__( 'Header position', 'alliance' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select position to display the site header on the %s pages', 'alliance' ), $title ) ),
					'std'     => 'inherit',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"header_image_override{$suffix}_{$cpt}"  => array(
					'title'   => esc_html__( 'Header image override', 'alliance' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( "Allow overriding the header image with a featured image of %s.", 'alliance' ), $title ) ),
					'std'     => 'inherit',
					'options' => alliance_get_list_yesno( true ),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"header_widgets{$suffix}_{$cpt}"         => array(
					'title'   => esc_html__( 'Header widgets', 'alliance' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select set of widgets to show in the header on the %s pages', 'alliance' ), $title ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => 'hidden',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Sidebar'
if ( ! function_exists( 'alliance_options_get_list_cpt_options_sidebar' ) ) {
	function alliance_options_get_list_cpt_options_sidebar( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = alliance_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "alliance_filter_get_list_cpt_options_sidebar{$suffix}", array_merge(
				array(
					"sidebar_info{$suffix}_{$cpt}"           => array(
						// Translators: Add CPT name to the description
						'title' => wp_kses_data( sprintf( __( 'Sidebar on %s', 'alliance' ), $suffix2 ) ),
						// Translators: Add CPT name to the description
						'desc'  => wp_kses_data( sprintf( __( 'Set up sidebar parameters to display %s', 'alliance' ), $suffix2 ) ),
						'type'  => 'info',
					),
					"sidebar_position{$suffix}_{$cpt}"       => array(
						'title'   => esc_html__( 'Sidebar position', 'alliance' ),
						'desc'    => wp_kses_data( __( 'Select sidebar position', 'alliance' ) ),
						'std'     => 'right',
						'options' => array(),
						'type'    => 'choice',
					),
					"sidebar_position_ss{$suffix}_{$cpt}"    => array(
						'title'    => esc_html__( 'Sidebar position on the small screen', 'alliance' ),
						'desc'     => wp_kses_data( __( 'Select a position for the sidebar on the small screen: above the content, below or on a sliding side-panel.', 'alliance' ) ),
						'std'      => 'below',
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
						),
						'options'  => array(),
						'type'     => 'hidden',
					),
					"sidebar_type{$suffix}_{$cpt}"           => array(
						'title'    => esc_html__( 'Sidebar style', 'alliance' ),
						'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
						),
						'std'      => 'default',
						'options'  => alliance_get_list_header_footer_types( true ),
						'pro_only' => ALLIANCE_THEME_FREE,
						'type'     => ! alliance_exists_trx_addons() ? 'hidden' : 'radio',
					),
					"sidebar_style{$suffix}_{$cpt}"          => array(
						'title'      => esc_html__( 'Select custom layout', 'alliance' ),
						'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'alliance' ), 'alliance_kses_content' ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
							"sidebar_type{$suffix}_{$cpt}"     => array( 'custom' ),
						),
						'std'        => '',
						'options'    => array(),
						'type'       => 'select',
					),
					"sidebar_widgets{$suffix}_{$cpt}"        => array(
						'title'      => esc_html__( 'Sidebar widgets', 'alliance' ),
						'desc'       => wp_kses_data( __( 'Select set of widgets to display in the sidebar', 'alliance' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
							"sidebar_type{$suffix}_{$cpt}"     => array( 'default' ),
						),
						'std'        => 'hide',
						'options'    => array(),
						'type'       => 'select',
					),
				),
				$mode == 'single' ? array() : array(
					"sidebar_width{$suffix}_{$cpt}"          => array(
						'title'      => esc_html__( 'Sidebar width', 'alliance' ),
						'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width', 'alliance' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
						),
						'std'        => 'inherit',
						'min'        => 0,
						'max'        => 500,
						'step'       => 10,
						'show_value' => true,
						'units'      => 'px',
						'refresh'    => false,
						'pro_only'   => ALLIANCE_THEME_FREE,
						'type'       => 'slider',
					),
					"sidebar_gap{$suffix}_{$cpt}"            => array(
						'title'      => esc_html__( 'Sidebar gap', 'alliance' ),
						'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'alliance' ) ),
						'dependency' => array(
							"sidebar_position{$suffix}_{$cpt}" => array( '^hide' ),
						),
						'std'        => 'inherit',
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'show_value' => true,
						'units'      => 'px',
						'refresh'    => false,
						'pro_only'   => ALLIANCE_THEME_FREE,
						'type'       => 'slider',
					),
				),
				array(
					"expand_content{$suffix}_{$cpt}"         => array(
						'title'   => esc_html__( 'Content width', 'alliance' ),
						'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'alliance' ) ),
						'refresh' => false,
						'std'     => 'inherit',
						'options' => alliance_get_list_expand_content( true ),
						'pro_only'=> ALLIANCE_THEME_FREE,
						'type'    => 'hidden',
					),
				)
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Footer'
if ( ! function_exists( 'alliance_options_get_list_cpt_options_footer' ) ) {
	function alliance_options_get_list_cpt_options_footer( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = alliance_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "alliance_filter_get_list_cpt_options_footer{$suffix}", array(
				"footer_info{$suffix}_{$cpt}"            => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Footer on %s', 'alliance' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up footer parameters to display %s', 'alliance' ), $suffix2 ) ),
					'type'  => 'info',
				),
				"footer_type{$suffix}_{$cpt}"            => array(
					'title'   => esc_html__( 'Footer style', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'alliance' ) ),
					'std'     => 'inherit',
					'options' => alliance_get_list_header_footer_types( true ),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'radio',
				),
				"footer_style{$suffix}_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select custom layout to display the site footer', 'alliance' ) ),
					'std'        => 'inherit',
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'custom' ),
					),
					'options'    => array(),
					'pro_only'   => ALLIANCE_THEME_FREE,
					'type'       => 'select',
				),
				"footer_widgets{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer widgets', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'alliance' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"footer_columns{$suffix}_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer columns', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'alliance' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}"    => array( 'default' ),
						"footer_widgets{$suffix}_{$cpt}" => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => alliance_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				"footer_wide{$suffix}_{$cpt}"            => array(
					'title'      => esc_html__( 'Footer fullwidth', 'alliance' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'alliance' ) ),
					'dependency' => array(
						"footer_type{$suffix}_{$cpt}" => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Additional Widget Areas'
if ( ! function_exists( 'alliance_options_get_list_cpt_options_widgets' ) ) {
	function alliance_options_get_list_cpt_options_widgets( $cpt, $title = '', $mode = 'both' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		$suffix = $mode == 'single' ? '_single' : '';
		$suffix2 = alliance_options_get_cpt_description_suffix( $title, $mode );
		return apply_filters( "alliance_filter_get_list_cpt_options_widgets{$suffix}", array(
				"widgets_info{$suffix}_{$cpt}"           => array(
					// Translators: Add CPT name to the description
					'title' => wp_kses_data( sprintf( __( 'Additional panels on %s', 'alliance' ), $suffix2 ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up additional panels to display %s', 'alliance' ), $suffix2 ) ),
					'pro_only'  => ALLIANCE_THEME_FREE,
					'type'  => 'hidden',
				),
				"widgets_above_page{$suffix}_{$cpt}"     => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"widgets_above_content{$suffix}_{$cpt}"  => array(
					'title'   => esc_html__( 'Widgets above the content', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"widgets_below_content{$suffix}_{$cpt}"  => array(
					'title'   => esc_html__( 'Widgets below the content', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
				"widgets_below_page{$suffix}_{$cpt}"     => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'alliance' ) ),
					'std'     => 'hide',
					'options' => array(),
					'pro_only'=> ALLIANCE_THEME_FREE,
					'type'    => 'hidden',
				),
			), $cpt, $title
		);
	}
}


// Return lists with choises when its need in the admin mode
if ( ! function_exists( 'alliance_options_get_list_choises' ) ) {
	add_filter( 'alliance_filter_options_get_list_choises', 'alliance_options_get_list_choises', 10, 2 );
	function alliance_options_get_list_choises( $list, $id ) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ( strpos( $id, 'header_style' ) === 0 ) {
				$list = alliance_get_list_header_styles( strpos( $id, 'header_style_' ) === 0 );
			} elseif ( strpos( $id, 'header_position' ) === 0 ) {
				$list = alliance_get_list_header_positions( strpos( $id, 'header_position_' ) === 0 );
			} elseif ( strpos( $id, 'header_widgets' ) === 0 ) {
				$list = alliance_get_list_sidebars( strpos( $id, 'header_widgets_' ) === 0, true );
			} elseif ( strpos( $id, '_scheme' ) > 0 ) {
				$list = alliance_get_list_schemes( 'color_scheme' != $id );
			} else if ( strpos( $id, 'sidebar_style' ) === 0 ) {
				$list = alliance_get_list_sidebar_styles( strpos( $id, 'sidebar_style_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_widgets' ) === 0 ) {
				$list = alliance_get_list_sidebars( 'sidebar_widgets_single' != $id && ( strpos( $id, 'sidebar_widgets_' ) === 0 || strpos( $id, 'sidebar_widgets_single_' ) === 0 ), true );
			} elseif ( strpos( $id, 'sidebar_position_ss' ) === 0 ) {
				$list = alliance_get_list_sidebars_positions_ss( strpos( $id, 'sidebar_position_ss_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_position' ) === 0 ) {
				$list = alliance_get_list_sidebars_positions( strpos( $id, 'sidebar_position_' ) === 0 );
			} elseif ( strpos( $id, 'widgets_above_page' ) === 0 ) {
				$list = alliance_get_list_sidebars( strpos( $id, 'widgets_above_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_above_content' ) === 0 ) {
				$list = alliance_get_list_sidebars( strpos( $id, 'widgets_above_content_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_page' ) === 0 ) {
				$list = alliance_get_list_sidebars( strpos( $id, 'widgets_below_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_content' ) === 0 ) {
				$list = alliance_get_list_sidebars( strpos( $id, 'widgets_below_content_' ) === 0, true );
			} elseif ( strpos( $id, 'footer_style' ) === 0 ) {
				$list = alliance_get_list_footer_styles( strpos( $id, 'footer_style_' ) === 0 );
			} elseif ( strpos( $id, 'footer_widgets' ) === 0 ) {
				$list = alliance_get_list_sidebars( strpos( $id, 'footer_widgets_' ) === 0, true );
			} elseif ( strpos( $id, 'blog_style' ) === 0 ) {
				$list = alliance_get_list_blog_styles( strpos( $id, 'blog_style_' ) === 0 );
			} elseif ( strpos( $id, 'single_style' ) === 0 ) {
				$list = alliance_get_list_single_styles( strpos( $id, 'single_style_' ) === 0 );
			} elseif ( strpos( $id, 'post_type' ) === 0 ) {
				$list = alliance_get_list_posts_types();
			} elseif ( strpos( $id, 'parent_cat' ) === 0 ) {
				$list = alliance_array_merge( array( 0 => alliance_get_not_selected_text( esc_html__( 'Select category', 'alliance' ) ) ), alliance_get_list_categories() );
			} elseif ( strpos( $id, 'blog_animation' ) === 0 ) {
				$list = alliance_get_list_animations_in( strpos( $id, 'blog_animation_' ) === 0 );
			} elseif ( 'color_scheme_editor' == $id ) {
				$list = alliance_get_list_schemes();
			} elseif ( 'color_preset' == $id ) {
				$list = alliance_get_list_color_presets();
			} elseif ( strpos( $id, '_font-family' ) > 0 ) {
				$list = alliance_get_list_load_fonts( true );
			} elseif ( 'font_preset' == $id ) {
				$list = alliance_get_list_font_presets();
			}
		}
		return $list;
	}
}


//--------------------------------------------
// THUMBS
//--------------------------------------------
if ( ! function_exists( 'alliance_skin_setup_thumbs' ) ) {
	add_action( 'after_setup_theme', 'alliance_skin_setup_thumbs', 1 );
	function alliance_skin_setup_thumbs() {
		alliance_storage_set(
			'theme_thumbs', apply_filters(
				'alliance_filter_add_thumb_sizes', array(
					// Width of the image is equal to the content area width (without sidebar)
					// Height is fixed
					'alliance-thumb-huge'        => array(
						'size'  => array( 1300, 715, true ),
						'title' => esc_html__( 'Huge image', 'alliance' ),
						'subst' => 'trx_addons-thumb-huge',
					),
					// Width of the image is equal to the content area width (with sidebar)
					// Height is fixed
					'alliance-thumb-big'         => array(
						'size'  => array( 760, 428, true ),
						'title' => esc_html__( 'Large image', 'alliance' ),
						'subst' => 'trx_addons-thumb-big',
					),

					// Height is fixed
					'alliance-thumb-large'        => array(
						'size'  => array( 400, 267, true ),
						'title' => esc_html__( 'Large image', 'alliance' ),
						'subst' => 'trx_addons-thumb-large',
					),

					// Width of the image is equal to the 1/3 of the content area width (without sidebar)
					// Height is fixed
					'alliance-thumb-med'         => array(
						'size'  => array( 370, 208, true ),
						'title' => esc_html__( 'Medium image', 'alliance' ),
						'subst' => 'trx_addons-thumb-medium',
					),

					// Small square image (for avatars in comments, etc.)
					'alliance-thumb-tiny'        => array(
						'size'  => array( 90, 90, true ),
						'title' => esc_html__( 'Small square avatar', 'alliance' ),
						'subst' => 'trx_addons-thumb-tiny',
					),

					// Width of the image is equal to the content area width (with sidebar)
					// Height is proportional (only downscale, not crop)
					'alliance-thumb-masonry-big' => array(
						'size'  => array( 760, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry Large (scaled)', 'alliance' ),
						'subst' => 'trx_addons-thumb-masonry-big',
					),

					// Width of the image is equal to the 1/3 of the full content area width (without sidebar)
					// Height is proportional (only downscale, not crop)
					'alliance-thumb-masonry'     => array(
						'size'  => array( 370, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry (scaled)', 'alliance' ),
						'subst' => 'trx_addons-thumb-masonry',
					),
				)
			)
		);
	}
}


//--------------------------------------------
// BLOG STYLES
//--------------------------------------------
if ( ! function_exists( 'alliance_skin_setup_blog_styles' ) ) {
	add_action( 'after_setup_theme', 'alliance_skin_setup_blog_styles', 1 );
	function alliance_skin_setup_blog_styles() {

		$blog_styles = array(
			'excerpt' => array(
				'title'   => esc_html__( 'Standard', 'alliance' ),
				'archive' => 'index',
				'item'    => 'templates/content-excerpt',
				'columns' => array( 1, 2 ),
				'styles'  => array( 'excerpt', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/excerpt-%d.png",
				'new_row' => true,
			),
			'band'    => array(
				'title'   => esc_html__( 'Band', 'alliance' ),
				'archive' => 'index',
				'item'    => 'templates/content-band',
				'styles'  => 'band',
				'icon'    => "images/theme-options/blog-style/band.png",
			),
			'classic' => array(
				'title'   => esc_html__( 'Classic', 'alliance' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3 ),
				'styles'  => array( 'classic', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/classic-%d.png",
				'new_row' => true,
			),
		);
		if ( ! ALLIANCE_THEME_FREE && false ) {
			$blog_styles['classic-masonry']   = array(
				'title'   => esc_html__( 'Classic Masonry', 'alliance' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3, 4 ),
				'styles'  => array( 'classic', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/classic-masonry-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio'] = array(
				'title'   => esc_html__( 'Portfolio', 'alliance' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3, 4 ),
				'styles'  => 'portfolio',
				'icon'    => "images/theme-options/blog-style/portfolio-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio-masonry'] = array(
				'title'   => esc_html__( 'Portfolio Masonry', 'alliance' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3, 4 ),
				'styles'  => array( 'portfolio', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/portfolio-masonry-%d.png",
				'new_row' => true,
			);
		}
		alliance_storage_set( 'blog_styles', apply_filters( 'alliance_filter_add_blog_styles', $blog_styles ) );
	}
}


//--------------------------------------------
// SINGLE STYLES
//--------------------------------------------
if ( ! function_exists( 'alliance_skin_setup_single_styles' ) ) {
	add_action( 'after_setup_theme', 'alliance_skin_setup_single_styles', 1 );
	function alliance_skin_setup_single_styles() {

		alliance_storage_set( 'single_styles', apply_filters( 'alliance_filter_add_single_styles', array(
			'style-1'   => array(
				'title'       => esc_html__( 'Style 1', 'alliance' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are over the image', 'alliance' ),
				'styles'      => 'style-1',
				'icon'        => "images/theme-options/single-style/style-1.png",
			),
			'style-2'   => array(
				'title'       => esc_html__( 'Style 2', 'alliance' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are inside the content area', 'alliance' ),
				'styles'      => 'style-2',
				'icon'        => "images/theme-options/single-style/style-2.png",
			),
			'style-3'   => array(
				'title'       => esc_html__( 'Style 3', 'alliance' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are below the image', 'alliance' ),
				'styles'      => 'style-3',
				'icon'        => "images/theme-options/single-style/style-3.png",
			),
			'style-4'   => array(
				'title'       => esc_html__( 'Style 4', 'alliance' ),
				'description' => esc_html__( 'Boxed image is above the content area, the title and meta are above the image', 'alliance' ),
				'styles'      => 'style-4',
				'icon'        => "images/theme-options/single-style/style-4.png",
			),
			'style-5'   => array(
				'title'       => esc_html__( 'Style 5', 'alliance' ),
				'description' => esc_html__( 'Boxed image is inside the content area, the title and meta are above the content area', 'alliance' ),
				'styles'      => 'style-5',
				'icon'        => "images/theme-options/single-style/style-5.png",
			),
			'style-6'   => array(
				'title'       => esc_html__( 'Style 6', 'alliance' ),
				'description' => esc_html__( 'Boxed image, the title and meta are inside the content area, the title and meta are above the image', 'alliance' ),
				'styles'      => 'style-6',
				'icon'        => "images/theme-options/single-style/style-6.png",
			),
			'style-7'   => array(
				'title'       => esc_html__( 'Style 7', 'alliance' ),
				'description' => esc_html__( 'Boxed image, the title and meta are above the content area like two big square areas', 'alliance' ),
				'styles'      => 'style-7',
				'icon'        => "images/theme-options/single-style/style-7.png",
			),
		) ) );
	}
}
