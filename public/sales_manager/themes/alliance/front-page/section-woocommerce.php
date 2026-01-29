<?php
$alliance_woocommerce_sc = alliance_get_theme_option( 'front_page_woocommerce_products' );
if ( ! empty( $alliance_woocommerce_sc ) ) {
	?><div class="front_page_section front_page_section_woocommerce<?php
		$alliance_scheme = alliance_get_theme_option( 'front_page_woocommerce_scheme' );
		if ( ! empty( $alliance_scheme ) && ! alliance_is_inherit( $alliance_scheme ) ) {
			echo ' scheme_' . esc_attr( $alliance_scheme );
		}
		echo ' front_page_section_paddings_' . esc_attr( alliance_get_theme_option( 'front_page_woocommerce_paddings' ) );
		if ( alliance_get_theme_option( 'front_page_woocommerce_stack' ) ) {
			echo ' sc_stack_section_on';
		}
	?>"
			<?php
			$alliance_css      = '';
			$alliance_bg_image = alliance_get_theme_option( 'front_page_woocommerce_bg_image' );
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
		$alliance_anchor_icon = alliance_get_theme_option( 'front_page_woocommerce_anchor_icon' );
		$alliance_anchor_text = alliance_get_theme_option( 'front_page_woocommerce_anchor_text' );
		if ( ( ! empty( $alliance_anchor_icon ) || ! empty( $alliance_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
			echo do_shortcode(
				'[trx_sc_anchor id="front_page_section_woocommerce"'
											. ( ! empty( $alliance_anchor_icon ) ? ' icon="' . esc_attr( $alliance_anchor_icon ) . '"' : '' )
											. ( ! empty( $alliance_anchor_text ) ? ' title="' . esc_attr( $alliance_anchor_text ) . '"' : '' )
											. ']'
			);
		}
	?>
		<div class="front_page_section_inner front_page_section_woocommerce_inner
			<?php
			if ( alliance_get_theme_option( 'front_page_woocommerce_fullheight' ) ) {
				echo ' alliance-full-height sc_layouts_flex sc_layouts_columns_middle';
			}
			?>
				"
				<?php
				$alliance_css      = '';
				$alliance_bg_mask  = alliance_get_theme_option( 'front_page_woocommerce_bg_mask' );
				$alliance_bg_color_type = alliance_get_theme_option( 'front_page_woocommerce_bg_color_type' );
				if ( 'custom' == $alliance_bg_color_type ) {
					$alliance_bg_color = alliance_get_theme_option( 'front_page_woocommerce_bg_color' );
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
			<div class="front_page_section_content_wrap front_page_section_woocommerce_content_wrap content_wrap woocommerce">
				<?php
				// Content wrap with title and description
				$alliance_caption     = alliance_get_theme_option( 'front_page_woocommerce_caption' );
				$alliance_description = alliance_get_theme_option( 'front_page_woocommerce_description' );
				if ( ! empty( $alliance_caption ) || ! empty( $alliance_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					// Caption
					if ( ! empty( $alliance_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<h2 class="front_page_section_caption front_page_section_woocommerce_caption front_page_block_<?php echo ! empty( $alliance_caption ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( $alliance_caption, 'alliance_kses_content' );
						?>
						</h2>
						<?php
					}

					// Description (text)
					if ( ! empty( $alliance_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<div class="front_page_section_description front_page_section_woocommerce_description front_page_block_<?php echo ! empty( $alliance_description ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( wpautop( $alliance_description ), 'alliance_kses_content' );
						?>
						</div>
						<?php
					}
				}

				// Content (widgets)
				?>
				<div class="front_page_section_output front_page_section_woocommerce_output list_products shop_mode_thumbs">
					<?php
					if ( 'products' == $alliance_woocommerce_sc ) {
						$alliance_woocommerce_sc_ids      = alliance_get_theme_option( 'front_page_woocommerce_products_per_page' );
						$alliance_woocommerce_sc_per_page = count( explode( ',', $alliance_woocommerce_sc_ids ) );
					} else {
						$alliance_woocommerce_sc_per_page = max( 1, (int) alliance_get_theme_option( 'front_page_woocommerce_products_per_page' ) );
					}
					$alliance_woocommerce_sc_columns = max( 1, min( $alliance_woocommerce_sc_per_page, (int) alliance_get_theme_option( 'front_page_woocommerce_products_columns' ) ) );
					echo do_shortcode(
						"[{$alliance_woocommerce_sc}"
										. ( 'products' == $alliance_woocommerce_sc
												? ' ids="' . esc_attr( $alliance_woocommerce_sc_ids ) . '"'
												: '' )
										. ( 'product_category' == $alliance_woocommerce_sc
												? ' category="' . esc_attr( alliance_get_theme_option( 'front_page_woocommerce_products_categories' ) ) . '"'
												: '' )
										. ( 'best_selling_products' != $alliance_woocommerce_sc
												? ' orderby="' . esc_attr( alliance_get_theme_option( 'front_page_woocommerce_products_orderby' ) ) . '"'
													. ' order="' . esc_attr( alliance_get_theme_option( 'front_page_woocommerce_products_order' ) ) . '"'
												: '' )
										. ' per_page="' . esc_attr( $alliance_woocommerce_sc_per_page ) . '"'
										. ' columns="' . esc_attr( $alliance_woocommerce_sc_columns ) . '"'
						. ']'
					);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
