<?php
/**
 * Generate custom CSS for theme hovers
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'alliance_hovers_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'alliance_hovers_theme_setup3', 3 );
	function alliance_hovers_theme_setup3() {

		// Add 'Buttons hover' option
		alliance_storage_set_array_after(
			'options', 'border_radius', array(
				'button_hover' => array(
					'title'   => esc_html__( "Button hover", 'alliance' ),
					'desc'    => wp_kses_data( __( 'Select a hover effect for theme buttons', 'alliance' ) ),
					'std'     => 'slide_left',
					'options' => array(
						'default'      => esc_html__( 'Fade', 'alliance' ),
						'slide_left'   => esc_html__( 'Slide from Left', 'alliance' ),
						'slide_right'  => esc_html__( 'Slide from Right', 'alliance' ),
						'slide_top'    => esc_html__( 'Slide from Top', 'alliance' ),
						'slide_bottom' => esc_html__( 'Slide from Bottom', 'alliance' ),
					),
					'type'    => 'select',
				),
				'image_hover'  => array(
					'title'    => esc_html__( "Image hover", 'alliance' ),
					'desc'     => wp_kses_data( __( 'Select a hover effect for theme images', 'alliance' ) ),
					'std'      => 'none',
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'alliance' ),
					),
					'options'  => alliance_get_list_hovers(),
					'type'     => 'select',
				),
			)
		);
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'alliance_hovers_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_hovers_theme_setup9', 9 );
	function alliance_hovers_theme_setup9() {
		add_action( 'wp_enqueue_scripts', 'alliance_hovers_frontend_scripts', 1100 );      // Priority 1100 -  after theme scripts (1000)
		add_action( 'wp_enqueue_scripts', 'alliance_hovers_frontend_styles', 1100 );       // Priority 1100 -  after theme/skin styles (1050)
		add_action( 'wp_enqueue_scripts', 'alliance_hovers_responsive_styles', 2100 );     // Priority 2100 -  after theme/skin responsive (2000)
		add_filter( 'alliance_filter_localize_script', 'alliance_hovers_localize_script' );
		add_filter( 'alliance_filter_merge_scripts', 'alliance_hovers_merge_scripts' );
		add_filter( 'alliance_filter_merge_styles', 'alliance_hovers_merge_styles' );
		add_filter( 'alliance_filter_merge_styles_responsive', 'alliance_hovers_merge_styles_responsive' );
		add_action( 'alliance_action_add_hover_icons','alliance_hovers_add_icons', 10, 2 );
	}
}

// Enqueue hover styles and scripts
if ( ! function_exists( 'alliance_hovers_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_hovers_frontend_scripts', 1100 );
	function alliance_hovers_frontend_scripts() {
		if ( alliance_is_on( alliance_get_theme_option( 'debug_mode' ) ) ) {
			$alliance_url = alliance_get_file_url( 'theme-specific/theme-hovers/theme-hovers.js' );
			if ( '' != $alliance_url ) {
				wp_enqueue_script( 'alliance-hovers', $alliance_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'alliance_hovers_frontend_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_hovers_frontend_styles', 1100 );
	function alliance_hovers_frontend_styles() {
		if ( alliance_is_on( alliance_get_theme_option( 'debug_mode' ) ) ) {
			$alliance_url = alliance_get_file_url( 'theme-specific/theme-hovers/theme-hovers.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-hovers', $alliance_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'alliance_hovers_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'alliance_hovers_responsive_styles', 2100 );
	function alliance_hovers_responsive_styles() {
		if ( alliance_is_on( alliance_get_theme_option( 'debug_mode' ) ) ) {
			$alliance_url = alliance_get_file_url( 'theme-specific/theme-hovers/theme-hovers-responsive.css' );
			if ( '' != $alliance_url ) {
				wp_enqueue_style( 'alliance-hovers-responsive', $alliance_url, array(), null, alliance_media_for_load_css_responsive( 'theme-hovers' ) );
			}
		}
	}
}

// Merge hover effects into single css
if ( ! function_exists( 'alliance_hovers_merge_styles' ) ) {
	//Handler of the add_filter( 'alliance_filter_merge_styles', 'alliance_hovers_merge_styles' );
	function alliance_hovers_merge_styles( $list ) {
		$list[ 'theme-specific/theme-hovers/theme-hovers.css' ] = true;
		return $list;
	}
}

// Merge hover effects to the single css (responsive)
if ( ! function_exists( 'alliance_hovers_merge_styles_responsive' ) ) {
	//Handler of the add_filter( 'alliance_filter_merge_styles_responsive', 'alliance_hovers_merge_styles_responsive' );
	function alliance_hovers_merge_styles_responsive( $list ) {
		$list[ 'theme-specific/theme-hovers/theme-hovers-responsive.css' ] = true;
		return $list;
	}
}

// Add hover effect's vars to the localize array
if ( ! function_exists( 'alliance_hovers_localize_script' ) ) {
	//Handler of the add_filter( 'alliance_filter_localize_script','alliance_hovers_localize_script' );
	function alliance_hovers_localize_script( $arr ) {
		$arr['button_hover'] = alliance_get_theme_option( 'button_hover' );
		return $arr;
	}
}

// Merge hover effects to the single js
if ( ! function_exists( 'alliance_hovers_merge_scripts' ) ) {
	//Handler of the add_filter( 'alliance_filter_merge_scripts', 'alliance_hovers_merge_scripts' );
	function alliance_hovers_merge_scripts( $list ) {
		$list[ 'theme-specific/theme-hovers/theme-hovers.js' ] = true;
		return $list;
	}
}

// Add hover icons on the featured image
if ( ! function_exists( 'alliance_hovers_add_icons' ) ) {
	//Handler of the add_action( 'alliance_action_add_hover_icons','alliance_hovers_add_icons', 10, 2 );
	function alliance_hovers_add_icons( $hover, $args = array() ) {

		// Additional parameters
		$args = array_merge(
			array(
				'cat'        => '',
				'image'      => null,
				'no_links'   => false,
				'link'       => '',
				'post_info'  => '',
				'meta_parts' => ''
			), $args
		);

		$post_link = empty( $args['no_links'] )
						? ( ! empty( $args['link'] )
							? $args['link']
							: apply_filters( 'alliance_filter_get_post_link', get_permalink() )
							)
						: '';
		$no_link   = 'javascript:void(0)';
		$target    = ! empty( $post_link ) && false === strpos( $post_link, get_site_url() )   // With activated WPML home_url() contain ?lang=xx and equal to external link
						? ' target="_blank" rel="nofollow"'
						: '';

		if ( in_array( $hover, array( 'icons', 'zoom' ) ) ) {
			// Hover style 'Icons and 'Zoom'
			if ( $args['image'] ) {
				$large_image = $args['image'];
			} else {
				$attachment = wp_get_attachment_image_src( get_post_thumbnail_id(), 'masonry-big' );
				if ( ! empty( $attachment[0] ) ) {
					$large_image = $attachment[0];
				}
			}
			if ( ! empty( $args['post_info'] ) ) {
				alliance_show_layout( $args['post_info'] );
			}
			?>
			<div class="icons">
				<a href="<?php echo ! empty( $post_link ) ? esc_url( $post_link ) : $no_link; ?>" <?php alliance_show_layout($target); ?> aria-hidden="true" class="icon-link
									<?php
									if ( empty( $large_image ) ) {
										echo ' single_icon';
									}
									?>
				"></a>
				<?php if ( ! empty( $large_image ) ) { ?>
				<a href="<?php echo esc_url( $large_image ); ?>" aria-hidden="true" class="icon-search" title="<?php the_title_attribute( '' ); ?>"></a>
				<?php } ?>
			</div>
			<?php

		} elseif ( 'shop' == $hover || 'shop_buttons' == $hover ) {
			// Hover style 'Shop'
			global $product;
			if ( ! empty( $args['post_info'] ) ) {
				alliance_show_layout( $args['post_info'] );
			}
			?>
			<div class="icons">
				<?php
				if ( ! is_object( $args['cat'] ) ) {
					alliance_show_layout(
						apply_filters(
							'woocommerce_loop_add_to_cart_link',
							'<a rel="nofollow" href="' . esc_url( $product->add_to_cart_url() ) . '" 
														aria-hidden="true" 
														data-quantity="1" 
														data-product_id="' . esc_attr( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() ) . '"
														data-product_sku="' . esc_attr( $product->get_sku() ) . '"
														class="shop_cart icon-cart-2 button add_to_cart_button'
																. ' product_type_' . $product->get_type()
																. ' product_' . ( $product->is_purchasable() && $product->is_in_stock() ? 'in' : 'out' ) . '_stock'
																. ( $product->supports( 'ajax_add_to_cart' ) ? ' ajax_add_to_cart' : '' )
																. '">'
											. ( 'shop_buttons' == $hover ? ( $product->is_type( 'variable' ) ? esc_html__( 'Options', 'alliance' ) : esc_html__( 'Buy now', 'alliance' ) ) : '' )
										. '</a>',
							$product
						)
					);
				}
				?>
				<a href="<?php echo esc_url( is_object( $args['cat'] )
													? get_term_link( $args['cat']->slug, 'product_cat' )
													: get_permalink()
											); ?>"
					aria-hidden="true" class="shop_link button icon-link">
				<?php
				if ( 'shop_buttons' == $hover ) {
					if ( is_object( $args['cat'] ) ) {
						esc_html_e( 'View products', 'alliance' );
					} else {
						esc_html_e( 'Details', 'alliance' );
					}
				}
				?>
				</a>
			</div>
			<?php

		} elseif ( 'icon' == $hover ) {
			// Hover style 'Icon'
			if ( ! empty( $args['post_info'] ) ) {
				alliance_show_layout( $args['post_info'] );
			}
			?>
			<div class="icons"><a href="<?php echo ! empty( $post_link ) ? esc_url( $post_link ) : $no_link; ?>" <?php alliance_show_layout($target); ?> aria-hidden="true" class="icon-search-alt"></a></div>
			<?php

		} elseif ( 'dots' == $hover ) {
			// Hover style 'Dots'
			if ( ! empty( $args['post_info'] ) ) {
				alliance_show_layout( $args['post_info'] );
			}
			?>
			<a href="<?php echo ! empty( $post_link ) ? esc_url( $post_link ) : $no_link; ?>" <?php alliance_show_layout($target); ?> aria-hidden="true" class="icons"><span></span><span></span><span></span></a>
			<?php

		} elseif ( 'info' == $hover ) {
			// Hover style 'Info'
			if ( ! empty( $args['post_info'] ) ) {
				alliance_show_layout( $args['post_info'] );
			} else {
				$alliance_components = empty( $args['meta_parts'] )
										? alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) )
										: ( is_array( $args['meta_parts'] )
											? $args['meta_parts']
											: explode( ',', $args['meta_parts'] )
											);
				?>
				<div class="post_info">
					<?php
					if ( in_array( 'categories', $alliance_components ) ) {
						if ( apply_filters( 'alliance_filter_show_blog_categories', true, array( 'categories' ) ) ) {
							?>
							<div class="post_category">
								<?php
								$categories = alliance_show_post_meta( apply_filters(
																	'alliance_filter_post_meta_args',
																	array(
																		'components' => 'categories',
																		'seo'        => false,
																		'echo'       => false,
																		),
																	'hover_' . $hover, 1
																	)
													);
								alliance_show_layout( str_replace( ', ', '', $categories ) );
								?>
							</div>
							<?php
						}
						$alliance_components = alliance_array_delete_by_value( $alliance_components, 'categories' );
					}
					if ( apply_filters( 'alliance_filter_show_blog_title', true ) ) {
						?>
						<h4 class="post_title">
							<?php
							if ( ! empty( $post_link ) ) {
								?>
								<a href="<?php echo esc_url( $post_link ); ?>" <?php alliance_show_layout($target); ?>>
								<?php
							}
							the_title();
							if ( ! empty( $post_link ) ) {
								?>
								</a>
								<?php
							}
							?>
						</h4>
						<?php
					}
					?>
					<div class="post_descr">
						<?php
						if ( ! empty( $alliance_components ) && count( $alliance_components ) > 0 ) {
							if ( apply_filters( 'alliance_filter_show_blog_meta', true, $alliance_components ) ) {
								alliance_show_post_meta(
									apply_filters(
										'alliance_filter_post_meta_args', array(
											'components' => join( ',', $alliance_components ),
											'seo'        => false,
											'echo'       => true,
										), 'hover_' . $hover, 1
									)
								);
							}
						}
						?>
					</div>
					<?php
					if ( ! empty( $post_link ) ) {
						?>
						<a class="post_link" href="<?php echo esc_url( $post_link ); ?>" <?php alliance_show_layout($target); ?>></a>
						<?php
					}
					?>
				</div>
				<?php
			}

		} elseif ( in_array( $hover, array( 'fade', 'pull', 'slide', 'border', 'excerpt' ) ) ) {
			// Hover style 'Fade', 'Slide', 'Pull', 'Border', 'Excerpt'
			if ( ! empty( $args['post_info'] ) ) {
				alliance_show_layout( $args['post_info'] );
			} else {
				?>
				<div class="post_info">
					<div class="post_info_back">
						<?php
						if ( apply_filters( 'alliance_filter_show_blog_title', true ) ) {
							?>
							<h4 class="post_title">
								<?php
								if ( ! empty( $post_link ) ) {
									?>
									<a href="<?php echo esc_url( $post_link ); ?>" <?php alliance_show_layout($target); ?>>
									<?php
								}
								the_title();
								if ( ! empty( $post_link ) ) {
									?>
									</a>
									<?php
								}
								?>
							</h4>
							<?php
						}
						?>
						<div class="post_descr">
							<?php
							if ( 'excerpt' != $hover ) {
								$alliance_components = empty( $args['meta_parts'] )
														? alliance_array_get_keys_by_value( alliance_get_theme_option( 'meta_parts' ) )
														: ( is_array( $args['meta_parts'] )
															? $args['meta_parts']
															: explode( ',', $args['meta_parts'] )
															);
								if ( ! empty( $alliance_components ) ) {
									if ( apply_filters( 'alliance_filter_show_blog_meta', true, $alliance_components ) ) {
										alliance_show_post_meta(
											apply_filters(
												'alliance_filter_post_meta_args', array(
													'components' => $alliance_components,
													'seo'        => false,
													'echo'       => true,
												), 'hover_' . $hover, 1
											)
										);
									}
								}
							}
							// Remove the condition below if you want display excerpt
							if ( 'excerpt' == $hover ) {
								if ( apply_filters( 'alliance_filter_show_blog_excerpt', true ) ) {
									?>
									<div class="post_excerpt"><?php
										alliance_show_layout( get_the_excerpt() );
									?></div>
									<?php
								}
							}
							?>
						</div>
						<?php
						if ( ! empty( $post_link ) ) {
							?>
							<a class="post_link" href="<?php echo esc_url( $post_link ); ?>" <?php alliance_show_layout($target); ?>></a>
							<?php
						}
						?>
					</div>
					<?php
					if ( ! empty( $post_link ) ) {
						?>
						<a class="post_link" href="<?php echo esc_url( $post_link ); ?>" <?php alliance_show_layout($target); ?>></a>
						<?php
					}
					?>
				</div>
				<?php
			}

		} else {

			do_action( 'alliance_action_custom_hover_icons', $args, $hover );

			if ( ! empty( $args['post_info'] ) ) {
				alliance_show_layout( $args['post_info'] );
			}
			if ( ! empty( $post_link ) ) {
				?>
				<a href="<?php echo esc_url( $post_link ); ?>" <?php alliance_show_layout($target); ?> aria-hidden="true" class="icons"></a>
				<?php
			}
		}
	}
}
