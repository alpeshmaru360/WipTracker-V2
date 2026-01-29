<?php
/**
 * Skin Setup
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.76.0
 */


//--------------------------------------------
// SKIN DEFAULTS
//--------------------------------------------

// Return theme's (skin's) default value for the specified parameter
if ( ! function_exists( 'alliance_theme_defaults' ) ) {
	function alliance_theme_defaults( $name='', $value='' ) {
		$defaults = array(
			'page_width'          => 1470,
			'page_boxed_extra'    => 60,
			'page_fullwide_max'   => 1920,
			'page_fullwide_extra' => 130,
			'sidebar_width'       => 345,
			'sidebar_gap'         => 30,
			'grid_gap'            => 30,
			'rad'                 => 26,
		);
		if ( empty( $name ) ) {
			return $defaults;
		} else {
			if ( empty( $value ) && isset( $defaults[ $name ] ) ) {
				$value = $defaults[ $name ];
			}
			return $value;
		}
	}
}


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)


//--------------------------------------------
// SKIN SETTINGS
//--------------------------------------------
if ( ! function_exists( 'alliance_skin_setup' ) ) {
	add_action( 'after_setup_theme', 'alliance_skin_setup', 1 );
	function alliance_skin_setup() {

		$GLOBALS['ALLIANCE_STORAGE'] = array_merge( $GLOBALS['ALLIANCE_STORAGE'], array(

			// Key validator: market[env|loc]-vendor[axiom|ancora|themerex]
			'theme_pro_key'       => 'env-themerex',

			'theme_doc_url'       => '//alliance.themerex.net/doc',

			'theme_demofiles_url' => '//demofiles.themerex.net/alliance-new/',
			
			'theme_rate_url'      => '//themeforest.net/download',

			'theme_custom_url'    => '//themerex.net/offers/?utm_source=offers&utm_medium=click&utm_campaign=themeinstall',

			'theme_support_url'   => '//themerex.net/support/',

			'theme_download_url'  => '//themeforest.net/user/themerex/portfolio',            // ThemeREX

			'theme_video_url'     => '//www.youtube.com/channel/UCnFisBimrK2aIE-hnY70kCA',   // ThemeREX

			'theme_privacy_url'   => '//themerex.net/privacy-policy/',                       // ThemeREX

			'portfolio_url'       => '//themeforest.net/user/themerex/portfolio',            // ThemeREX

			// Comma separated slugs of theme-specific categories (for get relevant news in the dashboard widget)
			// (i.e. 'children,kindergarten')
			'theme_categories'    => '',
		) );
	}
}


// Add/remove/change Theme Settings
if ( ! function_exists( 'alliance_skin_setup_settings' ) ) {
	add_action( 'after_setup_theme', 'alliance_skin_setup_settings', 1 );
	function alliance_skin_setup_settings() {
		// Example: enable (true) / disable (false) thumbs in the prev/next navigation
		alliance_storage_set_array( 'settings', 'thumbs_in_navigation', true );
		alliance_storage_set_array2( 'required_plugins', 'learnpress', 'install', false);
	}
}



//--------------------------------------------
// SKIN FONTS
//--------------------------------------------
if ( ! function_exists( 'alliance_skin_setup_fonts' ) ) {
	add_action( 'after_setup_theme', 'alliance_skin_setup_fonts', 1 );
	function alliance_skin_setup_fonts() {
		// Fonts to load when theme start
		// It can be:
		// - Google fonts (specify name, family and styles)
		// - Adobe fonts (specify name, family and link URL)
		// - uploaded fonts (specify name, family), placed in the folder css/font-face/font-name inside the skin folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		// example: font name 'TeX Gyre Termes', folder 'TeX-Gyre-Termes'
		$load_fonts = array(
			// Google font
			array(
				'name'   => 'DM Sans',
				'family' => 'sans-serif',
				'link'   => '',                                                 // Parameter 'link' used only for the Adobe fonts
				'styles' => 'wght@400;500;600;700',    							// Parameter 'styles' used only for the Google fonts
																				// Google Fonts CSS API v.1: 300,400,700,300italic,400italic,700italic
																				// Google Fonts CSS API v.2: ital,wght@0,300;0,400;0,700;1,300;1,400;1,700
																				// Attention! For v.2 fonts list must be sorted by tuples (first digit in each pair)
			),
			array(
				'name'   => 'Outfit',
				'family' => 'sans-serif',
				'link'   => '',                                                 // Parameter 'link' used only for the Adobe fonts
				'styles' => 'wght@400;500;600;700',    // Parameter 'styles' used only for the Google fonts
																				// Google Fonts CSS API v.1: 300,400,700,300italic,400italic,700italic
																				// Google Fonts CSS API v.2: ital,wght@0,300;0,400;0,700;1,300;1,400;1,700
																				//                           Attention! For v.2 fonts list must be sorted by tuples (first digit in each pair)
			),
		);
		alliance_storage_set( 'load_fonts', $load_fonts );

		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		alliance_storage_set( 'load_fonts_subset', 'latin,latin-ext' );

		// Settings of the main tags.
		// Default value of 'font-family' may be specified as reference to the array $load_fonts (see above)
		// or as comma-separated string.
		// In the second case (if 'font-family' is specified manually as comma-separated string):
		//    1) Font name with spaces in the parameter 'font-family' will be enclosed in quotes and no spaces after comma!
		//    2) If font-family inherit a value from the 'Main text' - specify 'inherit' as a value
		// example:
		// Correct:   'font-family' => alliance_get_load_fonts_family_string( $load_fonts[0] )
		// Correct:   'font-family' => 'inherit'
		// Correct:   'font-family' => 'Roboto,sans-serif'
		// Correct:   'font-family' => '"PT Serif",sans-serif'
		// Incorrect: 'font-family' => 'Roboto, sans-serif'      // A space after a comma is prohibited
		// Incorrect: 'font-family' => 'PT Serif,sans-serif'     // A font family with spaces must be enclosed with quotes

		$font_description = esc_html__( 'Press "Reload preview area" button at the top of this panel after the all font parameters are changed. Please use only the following units: "rem" or "em"', 'alliance' );

		alliance_storage_set(
			'theme_fonts', array(
				'p'       => array(
					'title'           => esc_html__( 'Main text', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'main text', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'       => '15px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.6em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '',
					'margin-top'      => '0em',
					'margin-bottom'   => '24px',
				),
				'post'    => array(
					'title'           => esc_html__( 'Article text', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'article text', 'alliance' ) ),
					'font-family'     => '',			// Example: alliance_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'       => '',			// Example: '1.286rem',
					'font-weight'     => '',			// Example: '400',
					'font-style'      => '',			// Example: 'normal',
					'line-height'     => '',			// Example: '1.75em',
					'text-decoration' => '',			// Example: 'none',
					'text-transform'  => '',			// Example: 'none',
					'letter-spacing'  => '',			// Example: '',
					'margin-top'      => '',			// Example: '0em',
					'margin-bottom'   => '',			// Example: '1.4em',
				),
				'h1'      => array(
					'title'           => esc_html__( 'Heading 1', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H1', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '45px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.111em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '-0.02em',
					'margin-top'      => '1.444em',
					'margin-bottom'   => '0.555em',
				),
				'h2'      => array(
					'title'           => esc_html__( 'Heading 2', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H2', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '28px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.142em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.3214em',
					'margin-bottom'   => '0.7em',
				),
				'h3'      => array(
					'title'           => esc_html__( 'Heading 3', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H3', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '23px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.217em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.7em',
					'margin-bottom'   => '0.739em',
				),
				'h4'      => array(
					'title'           => esc_html__( 'Heading 4', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H4', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '20px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.3em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.9em',
					'margin-bottom'   => '0.85em',
				),
				'h5'      => array(
					'title'           => esc_html__( 'Heading 5', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H5', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '18px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.335em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '2.166em',
					'margin-bottom'   => '0.7em',
				),
				'h6'      => array(
					'title'           => esc_html__( 'Heading 6', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H6', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '15px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.333em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '2.7em',
					'margin-bottom'   => '1em',
				),
				'logo'    => array(
					'title'           => esc_html__( 'Logo text', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'text of the logo', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '32px',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.25em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'button'  => array(
					'title'           => esc_html__( 'Buttons', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'buttons', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '12px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '21px',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0.06em',
				),
				'input'   => array(
					'title'           => esc_html__( 'Input fields', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'input fields, dropdowns and textareas', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'       => '13px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '19px',     // Attention! Firefox don't allow line-height less then 1.5em in the select
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'info'    => array(
					'title'           => esc_html__( 'Post meta', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'post meta (author, categories, publish date, counters, share, etc.)', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'       => '12px',  // Old value '13px' don't allow using 'font zoom' in the custom blog items
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '18px',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'menu'    => array(
					'title'           => esc_html__( 'Main menu', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'main menu items', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[1] ),
					'font-size'       => '14px',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'submenu' => array(
					'title'           => esc_html__( 'Dropdown menu', 'alliance' ),
					'description'     => sprintf( $font_description, esc_html__( 'dropdown menu items', 'alliance' ) ),
					'font-family'     => alliance_get_load_fonts_family_string( $load_fonts[0] ),
					'font-size'       => '14px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
			)
		);

		// Font presets
		alliance_storage_set(
			'font_presets', array(
				'default' => array(
								'title'  => esc_html__( 'Default', 'alliance' ),
								'load_fonts' => $load_fonts,
								'theme_fonts' => alliance_storage_get('theme_fonts'),
							),
				'karla' => array(
								'title'  => esc_html__( 'Karla', 'alliance' ),
								'load_fonts' => array(
													// Google font
													array(
														'name'   => 'Dancing Script',
														'family' => 'fantasy',
														'link'   => '',
														'styles' => 'wght@300;400;700',	// 300,400,700
													),
													// Google font
													array(
														'name'   => 'Sansita Swashed',
														'family' => 'fantasy',
														'link'   => '',
														'styles' => 'wght@300;400;700',	// 300,400,700
													),
												),
								'theme_fonts' => array(
													'p'       => array(
														'font-family'     => '"Dancing Script",fantasy',
														'font-size'       => '1.25rem',
													),
													'post'    => array(
														'font-family'     => '',
													),
													'h1'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
														'font-size'       => '4em',
													),
													'h2'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h3'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h4'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h5'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'h6'      => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'logo'    => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'button'  => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'input'   => array(
														'font-family'     => 'inherit',
													),
													'info'    => array(
														'font-family'     => 'inherit',
													),
													'menu'    => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
													'submenu' => array(
														'font-family'     => '"Sansita Swashed",fantasy',
													),
												),
							),
				'roboto' => array(
								'title'  => esc_html__( 'Roboto', 'alliance' ),
								'load_fonts' => array(
													// Google font
													array(
														'name'   => 'Noto Sans JP',
														'family' => 'serif',
														'link'   => '',
														'styles' => 'ital,wght@0,300;0,400;0,700;1,300;1,400;1,700',   // 300,300italic,400,400italic,700,700italic
													),
													// Google font
													array(
														'name'   => 'Merriweather',
														'family' => 'sans-serif',
														'link'   => '',
														'styles' => 'ital,wght@0,300;0,400;0,700;1,300;1,400;1,700',   // 300,300italic,400,400italic,700,700italic
													),
												),
								'theme_fonts' => array(
													'p'       => array(
														'font-family'     => '"Noto Sans JP",serif',
													),
													'post'    => array(
														'font-family'     => '',
													),
													'h1'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h2'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h3'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h4'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h5'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'h6'      => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'logo'    => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'button'  => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'input'   => array(
														'font-family'     => 'inherit',
													),
													'info'    => array(
														'font-family'     => 'inherit',
													),
													'menu'    => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
													'submenu' => array(
														'font-family'     => 'Merriweather,sans-serif',
													),
												),
							),
				'garamond' => array(
								'title'  => esc_html__( 'Garamond', 'alliance' ),
								'load_fonts' => array(
													// Adobe font
													array(
														'name'   => 'Europe',
														'family' => 'sans-serif',
														'link'   => 'https://use.typekit.net/qmj1tmx.css',
														'styles' => '',
													),
													// Adobe font
													array(
														'name'   => 'Sofia Pro',
														'family' => 'sans-serif',
														'link'   => 'https://use.typekit.net/qmj1tmx.css',
														'styles' => '',
													),
												),
								'theme_fonts' => array(
													'p'       => array(
														'font-family'     => '"Sofia Pro",sans-serif',
													),
													'post'    => array(
														'font-family'     => '',
													),
													'h1'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h2'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h3'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h4'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h5'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'h6'      => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'logo'    => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'button'  => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'input'   => array(
														'font-family'     => 'inherit',
													),
													'info'    => array(
														'font-family'     => 'inherit',
													),
													'menu'    => array(
														'font-family'     => 'Europe,sans-serif',
													),
													'submenu' => array(
														'font-family'     => 'Europe,sans-serif',
													),
												),
							),
			)
		);
	}
}


//--------------------------------------------
// COLOR SCHEMES
//--------------------------------------------
if ( ! function_exists( 'alliance_skin_setup_schemes' ) ) {
	add_action( 'after_setup_theme', 'alliance_skin_setup_schemes', 1 );
	function alliance_skin_setup_schemes() {

		// Theme colors for customizer
		// Attention! Inner scheme must be last in the array below
		alliance_storage_set(
			'scheme_color_groups', array(
				'main'    => array(
					'title'       => esc_html__( 'Main', 'alliance' ),
					'description' => esc_html__( 'General colors', 'alliance' ),
				),
				'extra'   => array(
					'title'       => esc_html__( 'Extra', 'alliance' ),
					'description' => esc_html__( 'Extra block colors (audio, video, image posts, etc.)', 'alliance' ),
				),
				'input'   => array(
					'title'       => esc_html__( 'Input', 'alliance' ),
					'description' => esc_html__( 'Form field colors (text field, textarea, select, etc.)', 'alliance' ),
				),
				'accent'   => array(
					'title'       => esc_html__( 'Accent', 'alliance' ),
					'description' => esc_html__( 'Accent block colors (blockquotes, countdown, alert box, etc.)', 'alliance' ),
				),
			)
		);

		alliance_storage_set(
			'scheme_color_names', array(
				'content_bg'    => array(
					'title'       => esc_html__( 'Content background', 'alliance' ),
					'description' => esc_html__( 'Background color of the layout panels for displaying content. Also used as a background color for posts content, sidebars, headers, etc.', 'alliance' ),
				),
				'navigate_bg'    => array(
					'title'       => esc_html__( 'Navigation background', 'alliance' ),
					'description' => esc_html__( 'Background color of navigation elements (e.g. pagination), table headers, accordion tabs, etc.', 'alliance' ),
				),
				'menu_bg'    => array(
					'title'       => esc_html__( 'Menu background', 'alliance' ),
					'description' => esc_html__( 'Background color of the menu panel.', 'alliance' ),
				),
				'bg_color'    => array(
					'title'       => esc_html__( 'Background color', 'alliance' ),
					'description' => esc_html__( 'Background color of the pages', 'alliance' ),
				),
				'bg_hover'    => array(
					'title'       => esc_html__( 'Background hover', 'alliance' ),
					'description' => esc_html__( 'Background hover color of the pages', 'alliance' ),
				),
				'bd_color'    => array(
					'title'       => esc_html__( 'Border color', 'alliance' ),
					'description' => esc_html__( 'The border color of the blocks', 'alliance' ),
				),
				'text'        => array(
					'title'       => esc_html__( 'Text', 'alliance' ),
					'description' => esc_html__( 'The color of the text', 'alliance' ),
				),
				'text_dark'   => array(
					'title'       => esc_html__( 'Text dark', 'alliance' ),
					'description' => esc_html__( 'The color of dark text (bold, header, menu etc.)', 'alliance' ),
				),
				'text_light'  => array(
					'title'       => esc_html__( 'Text light', 'alliance' ),
					'description' => esc_html__( 'The color of light text (post date, counters, categories, tags, submenu etc.)', 'alliance' ),
				),

				'text_link'   => array(
					'title'       => esc_html__( 'Accent', 'alliance' ),
					'description' => esc_html__( 'The color of the accented layouts elements', 'alliance' ),
				),
				'text_hover'   => array(
					'title'       => esc_html__( 'Accent hover', 'alliance' ),
					'description' => esc_html__( 'The hover of the accented layouts elements', 'alliance' ),
				),
				'text_link2'  => array(
					'title'       => esc_html__( 'Accent 2', 'alliance' ),
					'description' => esc_html__( 'The color of other accented layouts elements', 'alliance' ),
				),
				'text_hover2'   => array(
					'title'       => esc_html__( 'Accent 2 hover', 'alliance' ),
					'description' => esc_html__( 'The hover of the accented layouts elements', 'alliance' ),
				),
				'text_link3'  => array(
					'title'       => esc_html__( 'Accent 3', 'alliance' ),
					'description' => esc_html__( 'The color of other accented layouts elements', 'alliance' ),
				),
				'text_hover3'   => array(
					'title'       => esc_html__( 'Accent 3 hover', 'alliance' ),
					'description' => esc_html__( 'The hover of the accented layouts elements', 'alliance' ),
				),
				'text_link4'  => array(
					'title'       => esc_html__( 'Accent 4', 'alliance' ),
					'description' => esc_html__( 'The color of other accented layouts elements', 'alliance' ),
				),
				'text_hover4'   => array(
					'title'       => esc_html__( 'Accent 4 hover', 'alliance' ),
					'description' => esc_html__( 'The hover of the accented layouts elements', 'alliance' ),
				),
				'text_link5'  => array(
					'title'       => esc_html__( 'Accent 5', 'alliance' ),
					'description' => esc_html__( 'The color of other accented layouts elements', 'alliance' ),
				),
				'text_hover5'   => array(
					'title'       => esc_html__( 'Accent 5 hover', 'alliance' ),
					'description' => esc_html__( 'The hover of the accented layouts elements', 'alliance' ),
				),
			)
		);

		// Default values for each color scheme
		$schemes = array(

			// Color scheme: 'default'
			'default' => array(
				'title'    => esc_html__( 'Default', 'alliance' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'content_bg'   	   	=> '#ffffff',//ok
					'navigate_bg'    	=> '#F8FBFC',//ok
					'menu_bg'       	=> '#FAFDFD',//ok

					'bg_color'         	=> '#EEF6F7',//ok
					'bd_color'         	=> '#D9E0E3',//ok

					// Text and links colors
					'text'             	=> '#707276',//ok
					'text_light'       	=> '#7A7E83',//ok
					'text_dark'        	=> '#000724',//ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#12151e',//ok
					'extra_bd_color'   => '#434759',//ok
					'extra_text'       => '#D9D9D9',//ok
					'extra_light'      => '#D9D9D9',//ok
					'extra_dark'       => '#FCFCFC',//ok

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#FFFFFF',//ok
					'input_bg_hover'   => '#F8FBFC',//ok
					'input_bd_color'   => '#D9E0E3',//ok
					'input_text'       => '#707276',//ok
					'input_light'      => '#7A7E83',//ok
					'input_dark'       => '#000724',//ok

					// Accent blocks		
					'accent_text'       => '#FCFCFC',//ok
					'accent_light'      => '#D9D9D9',//ok

					'accent_link'       => '#22BBCE',
					'accent_hover'      => '#0AA1B4',
					'accent_link2'      => '#285AE2',
					'accent_hover2'     => '#1946C1',
					'accent_link3'      => '#10C393',
					'accent_hover3'     => '#0BA87E',
					'accent_link4'      => '#8EB3C6',
					'accent_hover4'     => '#759AAD',
					'accent_link5'      => '#82BA08',
					'accent_hover5'     => '#699804',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'dark'
			'dark'    => array(
				'title'    => esc_html__( 'Dark', 'alliance' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'content_bg'   	   	=> '#1A1E2B',//ok
					'navigate_bg'    	=> '#242834',//ok
					'menu_bg'       	=> '#242834',//ok

					'bg_color'         	=> '#12151e',//ok
					'bd_color'         	=> '#434759',//ok

					// Text and links colors
					'text'             	=> '#D9D9D9',//ok
					'text_light'       	=> '#D9D9D9',//ok
					'text_dark'        	=> '#FCFCFC',//ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#EEF6F7',//ok
					'extra_bd_color'   => '#D9E0E3',//ok
					'extra_text'       => '#707276',//ok
					'extra_light'      => '#7A7E83',//ok
					'extra_dark'       => '#000724',//ok

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#242834',//ok
					'input_bg_hover'   => '#1A1E2B',//ok
					'input_bd_color'   => '#434759',//ok
					'input_text'       => '#D9D9D9',//ok
					'input_light'      => '#D9D9D9',//ok
					'input_dark'       => '#FCFCFC',//ok

					// Accent blocks
					'accent_text'       => '#FCFCFC',//ok
					'accent_light'      => '#D9D9D9',//ok

					'accent_link'       => '#22BBCE',
					'accent_hover'      => '#0AA1B4',
					'accent_link2'      => '#285AE2',
					'accent_hover2'     => '#1946C1',
					'accent_link3'      => '#10C393',
					'accent_hover3'     => '#0BA87E',
					'accent_link4'      => '#8EB3C6',
					'accent_hover4'     => '#759AAD',
					'accent_link5'      => '#82BA08',
					'accent_hover5'     => '#699804',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),
		);
		alliance_storage_set( 'schemes', $schemes );
		alliance_storage_set( 'schemes_original', $schemes );

		// Add names of additional colors
		//---> For example:
		//---> alliance_storage_set_array( 'scheme_color_names', 'new_color1', array(
		//---> 	'title'       => __( 'New color 1', 'alliance' ),
		//---> 	'description' => __( 'Description of the new color 1', 'alliance' ),
		//---> ) );


		// Additional colors for each scheme
		// Parameters:	'color' - name of the color from the scheme that should be used as source for the transformation
		//				'alpha' - to make color transparent (0.0 - 1.0)
		//				'hue', 'saturation', 'brightness' - inc/dec value for each color's component
		alliance_storage_set(
			'scheme_colors_add', array(
				'bg_color_0'        => array(
					'color' => 'bg_color',
					'alpha' => 0,
				),
				'bg_color_02'       => array(
					'color' => 'bg_color',
					'alpha' => 0.2,
				),
				'bg_color_07'       => array(
					'color' => 'bg_color',
					'alpha' => 0.7,
				),
				'bg_color_08'       => array(
					'color' => 'bg_color',
					'alpha' => 0.8,
				),
				'bg_color_09'       => array(
					'color' => 'bg_color',
					'alpha' => 0.9,
				),	
				'content_bg_03'       => array(
					'color' => 'content_bg',
					'alpha' => 0.3,
				),			
				'text_light_04'      => array(
					'color' => 'text_light',
					'alpha' => 0.4,
				),				
				'text_light_06'      => array(
					'color' => 'text_light',
					'alpha' => 0.6,
				),				
				'text_light_08'      => array(
					'color' => 'text_light',
					'alpha' => 0.8,
				),	
				'text_dark_005'      => array(
					'color' => 'text_dark',
					'alpha' => 0.05,
				),
				'text_dark_016'      => array(
					'color' => 'text_dark',
					'alpha' => 0.16,
				),
				'text_dark_02'      => array(
					'color' => 'text_dark',
					'alpha' => 0.2,
				),	
				'text_dark_025'      => array(
					'color' => 'text_dark',
					'alpha' => 0.25,
				),	
				'text_dark_03'      => array(
					'color' => 'text_dark',
					'alpha' => 0.3,
				),		
				'text_dark_07'      => array(
					'color' => 'text_dark',
					'alpha' => 0.7,
				),	
				'text_dark_08'      => array(
					'color' => 'text_dark',
					'alpha' => 0.8,
				),
				'extra_bg_color_08'      => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.8,
				),
				'extra_dark_06'      => array(
					'color' => 'extra_dark',
					'alpha' => 0.6,
				),
				'accent_text_012'      => array(
					'color' => 'accent_text',
					'alpha' => 0.12,
				),
				'accent_text_03'      => array(
					'color' => 'accent_text',
					'alpha' => 0.3,
				),
				'accent_text_06'      => array(
					'color' => 'accent_text',
					'alpha' => 0.6,
				),
				'accent_text_07'      => array(
					'color' => 'accent_text',
					'alpha' => 0.7,
				),
				'accent_link_005'      => array(
					'color' => 'accent_link',
					'alpha' => 0.05,
				),
				'accent_link_007'      => array(
					'color' => 'accent_link',
					'alpha' => 0.07,
				),
				'accent_link_02'      => array(
					'color' => 'accent_link',
					'alpha' => 0.2,
				),
				'accent_link_05'      => array(
					'color' => 'accent_link',
					'alpha' => 0.5,
				),
				'accent_link_07'      => array(
					'color' => 'accent_link',
					'alpha' => 0.7,
				),
				'accent_hover_02'      => array(
					'color' => 'accent_hover',
					'alpha' => 0.2,
				),
				'accent_link2_005'      => array(
					'color' => 'accent_link2',
					'alpha' => 0.05,
				),
				'accent_link2_007'      => array(
					'color' => 'accent_link2',
					'alpha' => 0.07,
				),
				'accent_link2_01'      => array(
					'color' => 'accent_link2',
					'alpha' => 0.1,
				),
				'accent_link2_02'      => array(
					'color' => 'accent_link2',
					'alpha' => 0.2,
				),
				'accent_link2_05'      => array(
					'color' => 'accent_link2',
					'alpha' => 0.5,
				),
				'accent_hover2_02'      => array(
					'color' => 'accent_hover2',
					'alpha' => 0.2,
				),
				'accent_link3_005'      => array(
					'color' => 'accent_link3',
					'alpha' => 0.05,
				),
				'accent_link3_007'      => array(
					'color' => 'accent_link3',
					'alpha' => 0.07,
				),
				'accent_link3_02'      => array(
					'color' => 'accent_link3',
					'alpha' => 0.2,
				),
				'accent_link3_05'      => array(
					'color' => 'accent_link3',
					'alpha' => 0.5,
				),
				'accent_hover3_02'      => array(
					'color' => 'accent_hover3',
					'alpha' => 0.2,
				),
				'accent_link4_02'      => array(
					'color' => 'accent_link4',
					'alpha' => 0.2,
				),
				'accent_hover4_02'      => array(
					'color' => 'accent_hover4',
					'alpha' => 0.2,
				),
				'accent_link5_005'      => array(
					'color' => 'accent_link5',
					'alpha' => 0.05,
				),
				'accent_link5_007'      => array(
					'color' => 'accent_link5',
					'alpha' => 0.07,
				),
				'accent_link5_02'      => array(
					'color' => 'accent_link5',
					'alpha' => 0.2,
				),
				'accent_link5_05'      => array(
					'color' => 'accent_link5',
					'alpha' => 0.5,
				),
				'accent_hover5_02'      => array(
					'color' => 'accent_hover5',
					'alpha' => 0.2,
				),
				'accent_link_blend'   => array(
					'color'      => 'accent_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
			)
		);

		// Simple scheme editor: lists the colors to edit in the "Simple" mode.
		// For each color you can set the array of 'slave' colors and brightness factors that are used to generate new values,
		// when 'main' color is changed
		// Leave 'slave' arrays empty if your scheme does not have a color dependency
		alliance_storage_set(
			'schemes_simple', array(
				'accent_link'        => array(
				),
				'accent_link2'        => array(
				),
				'accent_link3'        => array(
				),
				'accent_link4'        => array(
				),
				'accent_link5'        => array(
				),
			)
		);

		// Parameters to set order of schemes in the css
		alliance_storage_set(
			'schemes_sorted', array(
				'color_scheme',
				'header_scheme',
				'menu_scheme',
				'sidebar_scheme',
				'footer_scheme',
			)
		);

		// Color presets
		alliance_storage_set(
			'color_presets', array(
				'default' => array(
								'title'  => esc_html__( 'Default', 'alliance' ),
								'colors' => array(
												'default' => $schemes['default']['colors'],
												'dark'    => $schemes['dark']['colors'],
												)
							),
				'autumn'  => array(
								'title'  => esc_html__( 'Autumn', 'alliance' ),
								'colors' => array(
												'default' => array(
																	'accent_link'  => '#d83938',
																	'accent_hover' => '#f2b232',
																	),
												'dark' => array(
																	'accent_link'  => '#d83938',
																	'accent_hover' => '#f2b232',
																	)
												)
							),
				'green'   => array(
								'title'  => esc_html__( 'Natural Green', 'alliance' ),
								'colors' => array(
												'default' => array(
																	'accent_link'  => '#75ac78',
																	'accent_hover' => '#378e6d',
																	),
												'dark' => array(
																	'accent_link'  => '#75ac78',
																	'accent_hover' => '#378e6d',
																	)
												)
							),
			)
		);
	}
}

// Enqueue theme-specific style
if ( ! function_exists( 'alliance_theme_custom_style' ) ) {
	add_action( 'wp_enqueue_scripts', 'alliance_theme_custom_style', 4000 );
	function alliance_theme_custom_style() {
		$alliance_url = alliance_get_file_url( alliance_skins_get_current_skin_dir() . 'css/extra-style.css' );
		if ( '' != $alliance_url ) {
			wp_enqueue_style( 'alliance-skin-custom-css-' . esc_attr( alliance_skins_get_current_skin_name() ), $alliance_url, array(), null );
		}
	}
}

// Activation methods
if ( ! function_exists( 'alliance_skin_filter_activation_methods2' ) ) {
    add_filter( 'trx_addons_filter_activation_methods', 'alliance_skin_filter_activation_methods2', 11, 1 );
    function alliance_skin_filter_activation_methods2( $args ) {
        $args['elements_key'] = true;
        return $args;
    }
}


// M Chart - This filter hook is triggered after all of the Highcharts/Chart.js chart args for a given chart have been generated.
if ( ! function_exists( 'alliance_skin_m_chart_chart_args' ) ) {
	add_filter( 'm_chart_chart_args', 'alliance_skin_m_chart_chart_args', 11, 4 );
	function alliance_skin_m_chart_chart_args( $chart_args, $post, $post_meta, $args ) {
		// Type: Area
		if ( $chart_args['chart']['type'] == 'area' ) {			
			$chart_args['colors'] = array(
				alliance_get_scheme_color( 'accent_link5' ),
				alliance_get_scheme_color( 'accent_link2' ),
				alliance_get_scheme_color( 'accent_link' ),
				alliance_get_scheme_color( 'text_dark' ),
				alliance_get_scheme_color( 'accent_link4' ),
				alliance_get_scheme_color( 'accent_link3' ),
			);
		}

		// Type: Spline
		if ( $chart_args['chart']['type'] == 'spline' ) {			
			$chart_args['colors'] = array(
				alliance_get_scheme_color( 'accent_link3' ),
				alliance_get_scheme_color( 'accent_link5' ),
				alliance_get_scheme_color( 'accent_link' ),
				alliance_get_scheme_color( 'accent_link4' ),
				alliance_get_scheme_color( 'accent_link2' ),
				alliance_get_scheme_color( 'text_dark' ),
			);
		}
		
		return $chart_args;
	}
}

