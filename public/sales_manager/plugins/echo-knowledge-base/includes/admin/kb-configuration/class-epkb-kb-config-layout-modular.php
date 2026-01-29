<?php

/**
 * Lists settings, default values and display of Modular layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Layout_Modular {

    const LAYOUT_NAME = 'Modular';
	const CATEGORY_LEVELS = 6;

	/**
	 * Defines KB configuration for this theme.
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => 'false' )
	 *
	 * @return array with both basic and theme-specific configuration
	 */
	public static function get_fields_specification() {

        $config_specification = array(

	        // Row 1
	        'ml_row_1_module'                                       => array(
		        'label'       => __( 'Module Name', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'search'
	        ),
	        'ml_row_1_desktop_width'                                => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 100
	        ),
	        'ml_row_1_desktop_width_units'                          => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => '%'
	        ),

	        // Row 2
	        'ml_row_2_module'                                       => array(
		        'label'       => __( 'Module Name', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'categories_articles'
	        ),
	        'ml_row_2_desktop_width'                                => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 1080
	        ),
	        'ml_row_2_desktop_width_units'                          => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // Row 3
	        'ml_row_3_module'                                       => array(
		        'label'       => __( 'Module Name', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'articles_list'
	        ),
	        'ml_row_3_desktop_width'                                => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 1080
	        ),
	        'ml_row_3_desktop_width_units'                          => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // Row 4
	        'ml_row_4_module'                                       => array(
		        'label'       => __( 'Module Name', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'none'
	        ),
	        'ml_row_4_desktop_width'                                => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 100
	        ),
	        'ml_row_4_desktop_width_units'                          => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => '%'
	        ),

	        // Row 5
	        'ml_row_5_module'                                       => array(
		        'label'       => __( 'Module Name', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'none'
	        ),
	        'ml_row_5_desktop_width'                                => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 100
	        ),
	        'ml_row_5_desktop_width_units'                          => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => '%'
	        ),

	        // Module: Categories & Articles
	        'ml_categories_articles_layout'                         => array(
		        'label'       => __( 'Layout', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_layout',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'classic'   => __( 'Classic Layout',   'echo-knowledge-base' ),
			        'product'   => __( 'Product Layout',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'classic'
	        ),
	        'ml_categories_columns'                                 => array(
		        'label'       => __( 'Columns', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_columns',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        '2-col'   => __( '2 Columns',   'echo-knowledge-base' ),
			        '3-col'   => __( '3 Columns',   'echo-knowledge-base' ),
			        '4-col'   => __( '4 Columns',   'echo-knowledge-base' ),
		        ),
		        'default'     => '3-col'
	        ),
	        /*	'ml_categories_articles_layout_classic_design'  => array(
					'label'       => __( 'Design', 'echo-knowledge-base' ),
					'name'        => 'ml_categories_articles_layout_classic_design',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => array(
						'1' => '1',
						'2' => '2',
					),
					'default'     => '1'
				),*/
	        /*'ml_categories_articles_layout_product_design'  => array(
				'label'       => __( 'Design', 'echo-knowledge-base' ),
				'name'        => 'ml_categories_articles_layout_product_design',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'default'     => '3'
			),*/
	        'ml_categories_articles_height_mode'                    => array(
		        'label'       => __( 'Height Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_height_mode',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'variable'  => __( 'Variable',   'echo-knowledge-base' ),
			        'fixed'     => __( 'Minimum Height',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'variable'
	        ),
	        'ml_categories_articles_fixed_height'                   => array(
		        'label'       => __( 'Height ( px )', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_fixed_height',
		        'max'         => '2000',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 514
	        ),
	        'ml_categories_articles_nof_articles_displayed'         => array(
		        'label'       => __( 'Number of Articles Listed', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_nof_articles_displayed',
		        'max'         => '200',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 8
	        ),
	        'ml_categories_articles_icon_background_color_toggle'   => array(
		        'label'       => __( 'Show Icon Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_background_color_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'on'
	        ),

	        'ml_categories_articles_icon_background_color'          => array(
		        'label'       => __( 'Icon Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_background_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#e9f6ff'
	        ),
	        'ml_categories_articles_icon_color'                     => array(
		        'label'       => __( 'Top Icon Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#7accef'
	        ),
	        'ml_categories_articles_top_category_title_color'       => array(
		        'label'       => __( 'Top Category Title Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_top_category_title_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#000000'
	        ),
	        'ml_categories_articles_sub_category_color'             => array(
		        'label'       => __( 'Sub Category Icon / Text Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sub_category_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#2ca7db'
	        ),

	        'ml_categories_articles_border_color'                   => array(
		        'label'       => __( 'Border Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_border_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#eaeaea'
	        ),
	        'ml_categories_articles_article_color'                  => array(
		        'label'       => __( 'Article Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#1e73be'
	        ),
	        'ml_categories_articles_article_bg_color'               => array(
		        'label'       => __( 'Article Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_bg_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#ffffff'
	        ),
	        'ml_categories_articles_article_show_more_color'        => array(
		        'label'       => __( 'Show More Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_show_more_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#000000'
	        ),
	        'ml_categories_articles_cat_desc_color'                 => array(
		        'label'       => __( 'Category Desc Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_cat_desc_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#000000'
	        ),
	        'ml_categories_articles_back_button_bg_color'           => array(
		        'label'       => __( 'Back Button Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_back_button_bg_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#1e73be'
	        ),

	        'ml_categories_articles_icon_size'                      => array(
		        'label'       => __( 'Top Icon Size ( px )', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_size',
		        'max'         => '250',
		        'min'         => '0',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => '80'
	        ),
	        'ml_categories_articles_title_html_tag'                 => array(
		        'label'       => __( 'Title HTML Tag', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_title_html_tag',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'default'     => 'h2',
		        'style'       => 'small',
		        'options'     => array(
			        'div' => 'div',
			        'h1' => 'h1',
			        'h2' => 'h2',
			        'h3' => 'h3',
			        'h4' => 'h4',
			        'h5' => 'h5',
			        'h6' => 'h6',
			        'span' => 'span',
			        'p' => 'p',
		        ),
	        ),
	        'ml_categories_articles_collapse_categories'                         => array(
		        'label'       => __( 'Collapse Categories', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_collapse_categories',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'all_expanded'  => __( 'All Expanded',   'echo-knowledge-base' ),
			        'all_collapsed' => __( 'All Collapsed',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'all_collapsed'
	        ),

			// Modular Sidebar
	        'ml_categories_articles_sidebar_toggle'                 => array(
		        'label'       => __( 'Sidebar', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'off'
	        ),
	        'ml_categories_articles_sidebar_desktop_width'          => array(
		        'label'       => __( 'Sidebar Width (px/%)', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 100
	        ),
	        'ml_categories_articles_sidebar_location'               => array(
		        'label'       => __( 'Sidebar Location', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_location',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'left'   => __( 'Left',   'echo-knowledge-base' ),
			        'right'   => __( 'Right',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'right'
	        ),
	        'ml_categories_articles_sidebar_position_1'             => array(
		        'label'       => __( 'Sidebar Position 1', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_position_1',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'              => '-----',
			        'newest_articles'   => __( 'Newest Articles',   'echo-knowledge-base' ),
			        'recent_articles'   => __( 'Recent Articles',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'none'
	        ),
	        'ml_categories_articles_sidebar_position_2'             => array(
		        'label'       => __( 'Sidebar Position 2', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_sidebar_position_2',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'              => '-----',
			        'newest_articles'   => __( 'Newest Articles',   'echo-knowledge-base' ),
			        'recent_articles'   => __( 'Recent Articles',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'none'
	        ),

	        // Module: Search
	        'ml_search_layout'                                      => array(
		        'label'       => __( 'Layout', 'echo-knowledge-base' ),
		        'name'        => 'ml_search_layout',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'classic'   => __( 'Classic Layout',   'echo-knowledge-base' ),
			        'modern'    => __( 'Modern Layout',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'classic'
	        ),

	        // Module: New and Recent Articles List
	        'ml_articles_list_nof_articles_displayed'               => array(
		        'label'       => __( 'Number of Articles Listed', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_nof_articles_displayed',
		        'max'         => '200',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 5
	        ),

	        // Module: FAQs
	        'ml_faqs_content_mode'                                  => array(
		        'label'       => __( 'Content Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_content_mode',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'content'    => __( 'Content', 'echo-knowledge-base' ),
			        'excerpt'    => __( 'Excerpt', 'echo-knowledge-base' )
		        ),
		        'default'     => 'content'
	        ),
	        'ml_faqs_custom_css_class'                              => array(
		        'label'       => __( 'Custom CSS class', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_custom_css_class',
		        'size'        => '200',
		        'max'         => '200',
		        'min'         => '0',
		        'mandatory'   => false,
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => ''
	        ),
        );

		return $config_specification;
	}
}
