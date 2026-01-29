<?php
/**
 * The template to display Admin notices
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.1
 */

$alliance_theme_slug = get_template();
$alliance_theme_obj  = wp_get_theme( $alliance_theme_slug );
?>
<div class="alliance_admin_notice alliance_welcome_notice notice notice-info is-dismissible" data-notice="admin">
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
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'alliance' ),
				$alliance_theme_obj->get( 'Name' ) . ( ALLIANCE_THEME_FREE ? ' ' . __( 'Free', 'alliance' ) : '' ),
				$alliance_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="alliance_notice_text">
		<p class="alliance_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $alliance_theme_obj->description ) );
			?>
		</p>
		<p class="alliance_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'alliance' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="alliance_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=alliance_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'alliance' );
			?>
		</a>
	</div>
</div>
