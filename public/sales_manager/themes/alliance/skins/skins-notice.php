<?php
/**
 * The template to display Admin notices
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.64
 */

$alliance_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$alliance_skins_args = get_query_var( 'alliance_skins_notice_args' );
?>
<div class="alliance_admin_notice alliance_skins_notice notice notice-info is-dismissible" data-notice="skins">
	<?php
	// Theme image
	$alliance_theme_img = alliance_get_file_url( 'screenshot.jpg' );
	if ( '' != $alliance_theme_img ) {
		?>
		<div class="alliance_notice_image"><img src="<?php echo esc_url( $alliance_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'alliance' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="alliance_notice_title">
		<?php esc_html_e( 'New skins available', 'alliance' ); ?>
	</h3>
	<?php

	// Description
	$alliance_total      = $alliance_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$alliance_skins_msg  = $alliance_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $alliance_total, 'alliance' ), $alliance_total ) . '</strong>'
							: '';
	$alliance_total      = $alliance_skins_args['free'];
	$alliance_skins_msg .= $alliance_total > 0
							? ( ! empty( $alliance_skins_msg ) ? ' ' . esc_html__( 'and', 'alliance' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $alliance_total, 'alliance' ), $alliance_total ) . '</strong>'
							: '';
	$alliance_total      = $alliance_skins_args['pay'];
	$alliance_skins_msg .= $alliance_skins_args['pay'] > 0
							? ( ! empty( $alliance_skins_msg ) ? ' ' . esc_html__( 'and', 'alliance' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $alliance_total, 'alliance' ), $alliance_total ) . '</strong>'
							: '';
	?>
	<div class="alliance_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'alliance' ), $alliance_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="alliance_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $alliance_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Skins manager', 'alliance' );
			?>
		</a>
	</div>
</div>
