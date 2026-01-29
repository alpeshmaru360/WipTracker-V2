<div class="front_page_section front_page_section_subscribe<?php
	$alliance_scheme = alliance_get_theme_option( 'front_page_subscribe_scheme' );
	if ( ! empty( $alliance_scheme ) && ! alliance_is_inherit( $alliance_scheme ) ) {
		echo ' scheme_' . esc_attr( $alliance_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( alliance_get_theme_option( 'front_page_subscribe_paddings' ) );
	if ( alliance_get_theme_option( 'front_page_subscribe_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$alliance_css      = '';
		$alliance_bg_image = alliance_get_theme_option( 'front_page_subscribe_bg_image' );
		if ( ! empty( $alliance_bg_image ) ) {
			$alliance_css .= 'background-image: url(' . esc_url( alliance_get_attachment_url( $alliance_bg_image ) ) . ');';
		}
		if ( ! empty( $alliance_css ) ) {
			echo ' style="' . esc_attr( $alliance_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$alliance_anchor_icon = alliance_get_theme_option( 'front_page_subscribe_anchor_icon' );
	$alliance_anchor_text = alliance_get_theme_option( 'front_page_subscribe_anchor_text' );
if ( ( ! empty( $alliance_anchor_icon ) || ! empty( $alliance_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_subscribe"'
									. ( ! empty( $alliance_anchor_icon ) ? ' icon="' . esc_attr( $alliance_anchor_icon ) . '"' : '' )
									. ( ! empty( $alliance_anchor_text ) ? ' title="' . esc_attr( $alliance_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_subscribe_inner
	<?php
	if ( alliance_get_theme_option( 'front_page_subscribe_fullheight' ) ) {
		echo ' alliance-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$alliance_css      = '';
			$alliance_bg_mask  = alliance_get_theme_option( 'front_page_subscribe_bg_mask' );
			$alliance_bg_color_type = alliance_get_theme_option( 'front_page_subscribe_bg_color_type' );
			if ( 'custom' == $alliance_bg_color_type ) {
				$alliance_bg_color = alliance_get_theme_option( 'front_page_subscribe_bg_color' );
			} elseif ( 'scheme_bg_color' == $alliance_bg_color_type ) {
				$alliance_bg_color = alliance_get_scheme_color( 'bg_color', $alliance_scheme );
			} else {
				$alliance_bg_color = '';
			}
			if ( ! empty( $alliance_bg_color ) && $alliance_bg_mask > 0 ) {
				$alliance_css .= 'background-color: ' . esc_attr(
					1 == $alliance_bg_mask ? $alliance_bg_color : alliance_hex2rgba( $alliance_bg_color, $alliance_bg_mask )
				) . ';';
			}
			if ( ! empty( $alliance_css ) ) {
				echo ' style="' . esc_attr( $alliance_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_subscribe_content_wrap content_wrap">
			<?php
			// Caption
			$alliance_caption = alliance_get_theme_option( 'front_page_subscribe_caption' );
			if ( ! empty( $alliance_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_subscribe_caption front_page_block_<?php echo ! empty( $alliance_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $alliance_caption, 'alliance_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$alliance_description = alliance_get_theme_option( 'front_page_subscribe_description' );
			if ( ! empty( $alliance_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_subscribe_description front_page_block_<?php echo ! empty( $alliance_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $alliance_description ), 'alliance_kses_content' ); ?></div>
				<?php
			}

			// Content
			$alliance_sc = alliance_get_theme_option( 'front_page_subscribe_shortcode' );
			if ( ! empty( $alliance_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_subscribe_output front_page_block_<?php echo ! empty( $alliance_sc ) ? 'filled' : 'empty'; ?>">
				<?php
					alliance_show_layout( do_shortcode( $alliance_sc ) );
				?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
