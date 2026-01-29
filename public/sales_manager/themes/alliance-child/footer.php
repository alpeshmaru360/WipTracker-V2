<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */

                            do_action( 'alliance_action_page_content_end_text' );
                            
                            // Widgets area below the content
                            alliance_create_widgets_area( 'widgets_below_content' );
                        
                            do_action( 'alliance_action_page_content_end' );
                            ?>
                        </div>
                        <?php
                        
                        do_action( 'alliance_action_after_page_content' );

                        // Show main sidebar
                        get_sidebar();

                        do_action( 'alliance_action_content_wrap_end' );
                        ?>
                    </div>
                    <?php

                    do_action( 'alliance_action_after_content_wrap' );

                    // Widgets area below the page and related posts below the page
                    $alliance_body_style = alliance_get_theme_option( 'body_style' );
                    $alliance_widgets_name = alliance_get_theme_option( 'widgets_below_page' );
                    $alliance_show_widgets = ! alliance_is_off( $alliance_widgets_name ) && is_active_sidebar( $alliance_widgets_name );
                    $alliance_show_related = alliance_is_single() && alliance_get_theme_option( 'related_position' ) == 'below_page';
                    if ( $alliance_show_widgets || $alliance_show_related ) {
                        if ( 'fullscreen' != $alliance_body_style ) {
                            ?>
                            <div class="content_wrap">
                            <?php
                        }
                        // Show related posts before footer
                        if ( $alliance_show_related ) {
                            do_action( 'alliance_action_related_posts' );
                        }

                        // Widgets area below page content
                        if ( $alliance_show_widgets ) {
                            alliance_create_widgets_area( 'widgets_below_page' );
                        }
                        if ( 'fullscreen' != $alliance_body_style ) {
                            ?>
                            </div>
                            <?php
                        }
                    }
                    do_action( 'alliance_action_page_content_wrap_end' );
                    ?>
            </div>
            <?php
            do_action( 'alliance_action_after_page_content_wrap' );

            // Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
            if ( ( ! alliance_is_singular( 'post' ) && ! alliance_is_singular( 'attachment' ) ) || ! in_array ( alliance_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
                
                // Skip link anchor to fast access to the footer from keyboard
                ?>
                <a id="footer_skip_link_anchor" class="alliance_skip_link_anchor" href="#"></a>
                <?php

                do_action( 'alliance_action_before_footer' );

                // Footer
                $alliance_footer_type = alliance_get_theme_option( 'footer_type' );
                if ( 'custom' == $alliance_footer_type && ! alliance_is_layouts_available() ) {
                    $alliance_footer_type = 'default';
                }
                get_template_part( apply_filters( 'alliance_filter_get_template_part', "templates/footer-" . sanitize_file_name( $alliance_footer_type ) ) );

                do_action( 'alliance_action_after_footer' );

            }
            $sap_support    = get_option('sap_support');
            $email_support  = get_option('email_support');
            ?>

            <?php do_action( 'alliance_action_page_wrap_end' ); ?>

        </div>

        <?php do_action( 'alliance_action_after_page_wrap' ); ?>

    </div>
    <div class="salehiya-floating-contacts">
        <div class="salehiya-floating-contact-wrap salehiya-green" style="/*background: url(<?php //echo site_url();?>/salehiya/uploads/2023/10/BG-Contact.png);*/ background-size: 100%;background-repeat: no-repeat;padding: 30px 0px 0px 10px;">
            <div class="salehiya-floating-icon">
                <img src="<?php echo site_url('/salehiya/uploads/2023/11/contact-us.png');?>" class="contact_us_floating_image">
              <span>CONTACT US</span>
            </div>
            <div class="salehiya-floating-link salehiya-floating-phone">
              <span class="sap_support"><a class="salehiya-nav-item" href="<?php echo $sap_support;?>" target="_blank"><img src="<?php echo site_url();?>/salehiya/uploads/2023/10/SAP-SUPPORT.svg" alt="SAP-SUPPORT" width="100px"> <label class="lable">SAP SUPPORT</label></a></span>
              
              <span class="email_support"><a class="salehiya-nav-item" href="mailto:<?php echo $email_support;?>" target="_blank"><img src="<?php echo site_url();?>/salehiya/uploads/2023/10/EMAIL-SUPPORT.svg" alt="EMAIL-SUPPORT" width="100px"><label class="lable">EMAIL SUPPORT</label></a></span>
            </div>
        </div>
    </div>
    <?php do_action( 'alliance_action_after_body' ); ?>

    <?php wp_footer(); ?>
    <?php
    wp_enqueue_script('jquery');
   
    ?>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
    <script>
        jQuery(document).ready(function(){
            jQuery('.menu_mobile.menu_mobile_narrow').removeClass('is_opened');
            // jQuery('.menu_mobile.menu_mobile_narrow .menu_mobile_nav_area ul li a span').text('');
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'sso_dashboard_count',
                    // Add any data you want to send to the server
                },
                success: function(response) {
                    response = jQuery.parseJSON(response);
                    var inbox_count= 0;
                    var pending_request_count=0;
                    var po_approval_count = 0;
                    if(response.success==1)
                    {
                        po_approval_count= response.data.po_approval_count;
                        inbox_count= response.data.inbox_count;
                        pending_request_count= response.data.pending_request_count;
                    }
                    jQuery(".po-approval").html( po_approval_count);
                    jQuery(".my_inbox_count").html(inbox_count);
                    jQuery(".my_pending_count").html(pending_request_count);
                }
            });
            // Floating Contact Widget Trigger
            jQuery(".salehiya-floating-icon").on("mouseenter", function(){
                jQuery(this).closest(".salehiya-floating-contact-wrap").addClass("hover")
            });
            jQuery(".salehiya-floating-contact-wrap").on("mouseleave", function(){
                jQuery(this).removeClass("hover");
            });
            var $swiperContainer = jQuery(".swiper-js-container");
            function init($this) {
                var $el = $this.find('.swiper-container'),
                pagination = $this.find('.swiper-pagination'),
                navNext = $this.find('.swiper-button-next'),
                navPrev = $this.find('.swiper-button-prev'),
                paginationType = $el.data('swiper-pagination-type') ? $el.data('swiper-pagination-type') : 'bullets';
                var $swiper = new Swiper($el, {
                    slidesPerView: 1,
                    spaceBetween: 10,
                    loop: true,
                    autoplay:{
                        delay: 4000,
                    },
                    breakpoints: {
                    767: {
                        slidesPerView: 1
                    },
                    1024: {
                        slidesPerView: 1
                    },
                    1270: {
                        slidesPerView: 1
                    }
                    },
                    pagination: {
                        el: pagination,
                        clickable: true,
                        type: paginationType,   
                    },
                    navigation: {
                        nextEl: navNext,
                        prevEl: navPrev,
                    }
                });     
                    //     var swiper = new Swiper(".swiper-container", {
                    //     pagination: {
                    //         el: ".swiper-pagination",
                    //         dynamicBullets: true,
                    //     },
                    // });
                
                }
                $swiperContainer.each(function(i, Slider) {
                    init(jQuery(Slider));
            });
                new Splide( '#first-slider', {
                    type   : 'loop',
                    perPage: 1,
                    autoHeight:true,
                    arrows:false,
                    autoplay:true,
                    interval     : 3000, // How long to display each slide
                    pauseOnHover : false, // must be false
                    pauseOnFocus : false, // must be false
                    resetProgress: false
                } ).mount();
        
            // Open the modal when the button is clicked
            jQuery('.open_popup').on('click', function () {
                jQuery.magnificPopup.open({
                    items: {
                        src: '#modalContent'
                    }
                    // type: 'inline'
                });
            });

            // Close the modal when the close button is clicked
            jQuery(document).on('click', '.mfp-close', function () {
                jQuery.magnificPopup.close();
            });

        });
    </script>
</body>
</html>