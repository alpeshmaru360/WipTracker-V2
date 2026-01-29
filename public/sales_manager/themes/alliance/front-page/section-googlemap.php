<div class="front_page_section front_page_section_googlemap<?php
	$alliance_scheme = alliance_get_theme_option( 'front_page_googlemap_scheme' );
	if ( ! empty( $alliance_scheme ) && ! alliance_is_inherit( $alliance_scheme ) ) {
		echo ' scheme_' . esc_attr( $alliance_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( alliance_get_theme_option( 'front_page_googlemap_paddings' ) );
	if ( alliance_get_theme_option( 'front_page_googlemap_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$alliance_css      = '';
		$alliance_bg_image = alliance_get_theme_option( 'front_page_googlemap_bg_image' );
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
	$alliance_anchor_icon = alliance_get_theme_option( 'front_page_googlemap_anchor_icon' );
	$alliance_anchor_text = alliance_get_theme_option( 'front_page_googlemap_anchor_text' );
if ( ( ! empty( $alliance_anchor_icon ) || ! empty( $alliance_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_googlemap"'
									. ( ! empty( $alliance_anchor_icon ) ? ' icon="' . esc_attr( $alliance_anchor_icon ) . '"' : '' )
									. ( ! empty( $alliance_anchor_text ) ? ' title="' . esc_attr( $alliance_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_googlemap_inner
		<?php
		$alliance_layout = alliance_get_theme_option( 'front_page_googlemap_layout' );
		echo ' front_page_section_layout_' . esc_attr( $alliance_layout );
		if ( alliance_get_theme_option( 'front_page_googlemap_fullheight' ) ) {
			echo ' alliance-full-height sc_layouts_flex sc_layouts_columns_middle';
		}
		?>
		"
			<?php
			$alliance_css      = '';
			$alliance_bg_mask  = alliance_get_theme_option( 'front_page_googlemap_bg_mask' );
			$alliance_bg_color_type = alliance_get_theme_option( 'front_page_googlemap_bg_color_type' );
			if ( 'custom' == $alliance_bg_color_type ) {
				$alliance_bg_color = alliance_get_theme_option( 'front_page_googlemap_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_googlemap_content_wrap
		<?php
		if ( 'fullwidth' != $alliance_layout ) {
			echo ' content_wrap';
		}
		?>
		">
			<?php
			// Content wrap with title and description
			$alliance_caption     = alliance_get_theme_option( 'front_page_googlemap_caption' );
			$alliance_description = alliance_get_theme_option( 'front_page_googlemap_description' );
			if ( ! empty( $alliance_caption ) || ! empty( $alliance_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				if ( 'fullwidth' == $alliance_layout ) {
					?>
					<div class="content_wrap">
					<?php
				}
					// Caption
				if ( ! empty( $alliance_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_googlemap_caption front_page_block_<?php echo ! empty( $alliance_caption ) ? 'filled' : 'empty'; ?>">
					<?php
					echo wp_kses( $alliance_caption, 'alliance_kses_content' );
					?>
					</h2>
					<?php
				}

					// Description (text)
				if ( ! empty( $alliance_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_googlemap_description front_page_block_<?php echo ! empty( $alliance_description ) ? 'filled' : 'empty'; ?>">
					<?php
					echo wp_kses( wpautop( $alliance_description ), 'alliance_kses_content' );
					?>
					</div>
					<?php
				}
				if ( 'fullwidth' == $alliance_layout ) {
					?>
					</div>
					<?php
				}
			}

			// Content (text)
			$alliance_content = alliance_get_theme_option( 'front_page_googlemap_content' );
			if ( ! empty( $alliance_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				if ( 'columns' == $alliance_layout ) {
					?>
					<div class="front_page_section_columns front_page_section_googlemap_columns columns_wrap">
						<div class="column-1_3">
					<?php
				} elseif ( 'fullwidth' == $alliance_layout ) {
					?>
					<div class="content_wrap">
					<?php
				}

				?>
				<div class="front_page_section_content front_page_section_googlemap_content front_page_block_<?php echo ! empty( $alliance_content ) ? 'filled' : 'empty'; ?>">
				<?php
					echo wp_kses( $alliance_content, 'alliance_kses_content' );
				?>
				</div>
				<?php

				if ( 'columns' == $alliance_layout ) {
					?>
					</div><div class="column-2_3">
					<?php
				} elseif ( 'fullwidth' == $alliance_layout ) {
					?>
					</div>
					<?php
				}
			}

			// Widgets output
			?>
			<div class="front_page_section_output front_page_section_googlemap_output">
				<?php
				if ( is_active_sidebar( 'front_page_googlemap_widgets' ) ) {
					dynamic_sidebar( 'front_page_googlemap_widgets' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					if ( ! alliance_exists_trx_addons() ) {
						alliance_customizer_need_trx_addons_message();
					} else {
						alliance_customizer_need_widgets_message( 'front_page_googlemap_caption', 'ThemeREX Addons - Google map' );
					}
				}
				?>
			</div>
			<?php

			if ( 'columns' == $alliance_layout && ( ! empty( $alliance_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div></div>
				<?php
			}
			?>
		</div>
	</div>
</div>
