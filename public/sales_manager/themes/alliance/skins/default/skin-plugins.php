<?php
/**
 * Required plugins
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$alliance_theme_required_plugins_groups = array(
	'core'          => esc_html__( 'Core', 'alliance' ),
	'page_builders' => esc_html__( 'Page Builders', 'alliance' ),
	'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'alliance' ),
	'socials'       => esc_html__( 'Socials and Communities', 'alliance' ),
	'events'        => esc_html__( 'Events and Appointments', 'alliance' ),
	'content'       => esc_html__( 'Content', 'alliance' ),
	'other'         => esc_html__( 'Other', 'alliance' ),
);
$alliance_theme_required_plugins        = array(
	'bbpress'                    => array(
		'title'       => esc_html__( 'bbPress and BuddyPress', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'bbpress.png',
		'group'       => $alliance_theme_required_plugins_groups['socials'],
	),
	'bp-better-messages'      => array(
		'title'       => esc_html__( 'Better Messages', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/bp-better-messages/bp-better-messages.png' ),
		'group'       => $alliance_theme_required_plugins_groups['socials'],
	),
	'bp-activity-shortcode'      => array(
		'title'       => esc_html__( 'BuddyPress Activity ShortCode', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/bbpress/bbpress-sc.png' ),
		'group'       => $alliance_theme_required_plugins_groups['socials'],
	),
	'buddypress-docs'      => array(
		'title'       => esc_html__( 'BuddyPress Docs', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/buddypress-docs/buddypress-docs.png' ),
		'group'       => $alliance_theme_required_plugins_groups['socials'],
	),
	'buddypress-learndash'      => array(
		'title'       => esc_html__( 'BuddyPress for LearnDash', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/buddypress/buddypress-learndash.png' ),
		'group'       => $alliance_theme_required_plugins_groups['socials'],
	),
	'contact-form-7'             => array(
		'title'       => esc_html__( 'Contact Form 7', 'alliance' ),
		'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'alliance' ),
		'required'    => false,
		'logo'        => 'contact-form-7.png',
		'group'       => $alliance_theme_required_plugins_groups['content'],
	),
	'democracy-poll'      => array(
		'title'       => esc_html__( 'Democracy Poll', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/democracy-poll/democracy-poll.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'elegro-payment'             => array(
		'title'       => esc_html__( 'Elegro Crypto Payment', 'alliance' ),
		'description' => esc_html__( "Extends WooCommerce Payment Gateways with an elegro Crypto Payment", 'alliance' ),
		'required'    => false,
		'logo'        => 'elegro-payment.png',
		'group'       => $alliance_theme_required_plugins_groups['ecommerce'],
	),
	'elementor'                  => array(
		'title'       => esc_html__( 'Elementor', 'alliance' ),
		'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'alliance' ),
		'required'    => false,          // Leave this plugin unchecked on load Theme Dashboard
		'logo'        => 'elementor.png',
		'group'       => $alliance_theme_required_plugins_groups['page_builders'],
	),
	'echo-knowledge-base'      => array(
		'title'       => esc_html__( 'Knowledge Base for Documents and FAQs', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/echo-knowledge-base/echo-knowledge-base.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'sfwd-lms'      => array(
		'title'       => esc_html__( 'LearnDash LMS', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/sfwd-lms/sfwd-lms.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'learndash-course-grid'      => array(
		'title'       => esc_html__( 'LearnDash LMS - Course Grid', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/sfwd-lms/sfwd-lms.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'learnpress'                 => array(
        'title'       => esc_html__( 'LearnPress', 'alliance' ),
        'description' => '',
        'required'    => false,
        'logo'        => alliance_get_file_url( 'plugins/learnpress/learnpress.png' ),
        'group'       => $alliance_theme_required_plugins_groups['events'],
    ),
	'm-chart'              => array(
		'title'       => esc_html__( 'M Chart', 'alliance' ),
		'description' => '',
		'required'    => false,
		'install'     => true,
		'logo'        => alliance_get_file_url( 'plugins/m-chart/m-chart.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'm-chart-highcharts-library'              => array(
		'title'       => esc_html__( 'M Chart Highcharts Library', 'alliance' ),
		'description' => '',
		'required'    => false,
		'install'     => true,
		'logo'        => alliance_get_file_url( 'plugins/m-chart/m-chart.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'mailchimp-for-wp'           => array(
		'title'       => esc_html__( 'MailChimp for WP', 'alliance' ),
		'description' => esc_html__( "Allows visitors to subscribe to newsletters", 'alliance' ),
		'required'    => false,
		'logo'        => 'mailchimp-for-wp.png',
		'group'       => $alliance_theme_required_plugins_groups['socials'],
	),
	'paid-memberships-pro'      => array(
		'title'       => esc_html__( 'Paid Memberships Pro', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/paid-memberships-pro/paid-memberships-pro.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'buddypress-media'      => array(
		'title'       => esc_html__( 'rtMedia for WordPress, BuddyPress and bbPress', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/bbpress/buddypress-media.png' ),
		'group'       => $alliance_theme_required_plugins_groups['socials'],
	),
	'the-events-calendar'        => array(
		'title'       => esc_html__( 'The Events Calendar', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'the-events-calendar.png',
		'group'       => $alliance_theme_required_plugins_groups['events'],
	),
	'trx_addons'                 => array(
		'title'       => esc_html__( 'ThemeREX Addons', 'alliance' ),
		'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'alliance' ),
		'required'    => false,           // Check this plugin in the list on load Theme Dashboard
		'logo'        => 'trx_addons.png',
		'group'       => $alliance_theme_required_plugins_groups['core'],
	),
	'trx_updater'                => array(
		'title'       => esc_html__( 'ThemeREX Updater', 'alliance' ),
		'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'alliance' ),
		'required'    => false,
		'logo'        => 'trx_updater.png',
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'woocommerce'                => array(
		'title'       => esc_html__( 'WooCommerce', 'alliance' ),
		'description' => esc_html__( "Connect the store to your website and start selling now", 'alliance' ),
		'required'    => false,
		'logo'        => 'woocommerce.png',
		'group'       => $alliance_theme_required_plugins_groups['ecommerce'],
	),
	'wp-job-manager'      => array(
		'title'       => esc_html__( 'WP Job Manager', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/wp-job-manager/wp-job-manager.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'wp-job-manager-resumes'      => array(
		'title'       => esc_html__( 'WP Job Manager - Resume Manager', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => alliance_get_file_url( 'plugins/wp-job-manager-resumes/wp-job-manager-resumes.png' ),
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'gutenberg'                  => array(
		'title'       => esc_html__( 'Gutenberg', 'alliance' ),
		'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'alliance' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'gutenberg.png',
		'group'       => $alliance_theme_required_plugins_groups['page_builders'],
	),
	'sitepress-multilingual-cms' => array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'alliance' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'alliance' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'sitepress-multilingual-cms.png',
		'group'       => $alliance_theme_required_plugins_groups['content'],
	),
	'wp-gdpr-compliance'         => array(
		'title'       => esc_html__( 'WP GDPR Compliance', 'alliance' ),
		'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'alliance' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'wp-gdpr-compliance.png',
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'trx_popup'                  => array(
		'title'       => esc_html__( 'ThemeREX Popup', 'alliance' ),
		'description' => esc_html__( "Add popup to your site.", 'alliance' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'trx_popup.png',
		'group'       => $alliance_theme_required_plugins_groups['other'],
	),
	'envato-market'              => array(
		'title'       => esc_html__( 'Envato Market', 'alliance' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'envato-market.png',
		'group'       => $alliance_theme_required_plugins_groups['other'],
	)
);

if ( ALLIANCE_THEME_FREE ) {
	unset( $alliance_theme_required_plugins['js_composer'] );
	unset( $alliance_theme_required_plugins['vc-extensions-bundle'] );
	unset( $alliance_theme_required_plugins['easy-digital-downloads'] );
	unset( $alliance_theme_required_plugins['give'] );
	unset( $alliance_theme_required_plugins['bbpress'] );
	unset( $alliance_theme_required_plugins['booked'] );
	unset( $alliance_theme_required_plugins['content_timeline'] );
	unset( $alliance_theme_required_plugins['mp-timetable'] );
	unset( $alliance_theme_required_plugins['learnpress'] );
	unset( $alliance_theme_required_plugins['the-events-calendar'] );
	unset( $alliance_theme_required_plugins['calculated-fields-form'] );
	unset( $alliance_theme_required_plugins['essential-grid'] );
	unset( $alliance_theme_required_plugins['revslider'] );
	unset( $alliance_theme_required_plugins['ubermenu'] );
	unset( $alliance_theme_required_plugins['sitepress-multilingual-cms'] );
	unset( $alliance_theme_required_plugins['envato-market'] );
	unset( $alliance_theme_required_plugins['trx_updater'] );
	unset( $alliance_theme_required_plugins['trx_popup'] );
}

// Add plugins list to the global storage
alliance_storage_set( 'required_plugins', $alliance_theme_required_plugins );
