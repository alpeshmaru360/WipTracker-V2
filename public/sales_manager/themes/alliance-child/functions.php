<?php
/**
 * Child-Theme functions and definitions
 */

function alliance_child_scripts() {
    wp_enqueue_style( 'alliance-style', get_template_directory_uri(). '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'alliance_child_scripts' );

/**start - home page redirect to admin login page if user is not login*****/
function redirect_home_to_login() {

    /************Start - get login user detail*********/
    $user_id = get_current_user_id();

    $access_token = get_user_meta($user_id, 'wpo_access_tokens', true);
    
    
    $access_token = json_decode($access_token,true);
    // echo "<pre>";
    // print_r($access_token);
    // if(!empty($access_token[0]['access_token']))
    // {
    //     // Make a request to the Microsoft Graph API to fetch user data
    //     $graph_api_url = 'https://graph.microsoft.com/v1.0/me';
    //     $response = wp_remote_get($graph_api_url, array(
    //         'headers' => array(
    //             'Authorization' => 'Bearer ' . $access_token[0]['access_token'],
    //         ),
    //     ));

    //     if (!is_wp_error($response)) {
    //         $user_data = json_decode(wp_remote_retrieve_body($response));
    //         echo "<pre>";
    //         print_r($user_data);
    //     }   
    //     die();
    // }
    /************End - get login user detail*********/

    if (!is_user_logged_in() && is_front_page()) {
        //wp_redirect(home_url('/login' ));
        wp_redirect(admin_url('/' ));
        exit;
    }
    else if(current_user_can( 'subscriber' ) && !is_front_page()) {
        wp_redirect(site_url());
        exit();
    }
    /*else if(current_user_can( 'administrator' ) && is_front_page()) {
        wp_redirect(admin_url('/' ));
        exit();
    }*/
}
function admin_default_page() {
    if(current_user_can( 'administrator' )){
        return admin_url('/' );
    }
}

//add_filter('login_redirect', 'admin_default_page');
add_action('template_redirect', 'redirect_home_to_login');
/**end - home page redirect to admin login page if user is not login*****/

add_action( 'admin_init', 'restrict_admin_with_redirect', 1 );
function restrict_admin_with_redirect() {
    if ( current_user_can( 'subscriber' ) && ( ! wp_doing_ajax() ) ) {
        wp_redirect(site_url());
        exit;
    }
    if(current_user_can( 'administrator' )){
        return admin_url('/' );
    }
}


function my_svg_image_mime_types( $mimes ) {
	// New allowed mime types.
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'my_svg_image_mime_types' );
 
/***********Start - add custom setting in admin side**********/
add_action('admin_init', 'my_general_global_section');  
function my_general_global_section() {  
    add_settings_section(  
        'my_settings_section', // Section ID 
        'API Settings', // Section Title
        'my_api_section_options_callback', // Callback
        'general' // What Page?  This makes the section show up on the General Settings Page
    );

    /***********start - base URL***********/
    add_settings_field( 
        'api_base_url', // Option ID
        'API Base URL', // Label
        'my_api_base_url_callback', // !important - This is where the args go!
        'general', // Page it will be displayed (General Settings)
        'my_settings_section', // Name of our section
        array( // The $args
            'api_base_url' // Should match Option ID
        )  
    ); 
    register_setting('general','api_base_url', 'esc_attr');
    /***********end - base URL***********/

    /***********start - username***********/
    add_settings_field( 
        'api_user_name', // Option ID
        'API Username', // Label
        'my_api_username_callback', // !important - This is where the args go!
        'general', // Page it will be displayed (General Settings)
        'my_settings_section', // Name of our section
        array( // The $args
            'api_user_name' // Should match Option ID
        )  
    ); 
    register_setting('general','api_user_name', 'esc_attr');
    /***********end - username***********/

    /***********start - password***********/
    add_settings_field( 
        'api_password', // Option ID
        'API Password', // Label
        'my_api_password_callback', // !important - This is where the args go!
        'general', // Page it will be displayed (General Settings)
        'my_settings_section', // Name of our section
        array( // The $args
            'api_password' // Should match Option ID
        )  
    ); 
    register_setting('general','api_password', 'esc_attr');
    /***********end - password***********/
    /***********start - sap_support***********/
    add_settings_field( 
        'sap_support', // Option ID
        'SAP Support URL', // Label
        'my_api_sap_support_callback', // !important - This is where the args go!
        'general', // Page it will be displayed (General Settings)
        'my_settings_section', // Name of our section
        array( // The $args
            'sap_support' // Should match Option ID
        )  
    ); 
    register_setting('general','sap_support', 'esc_attr');
    /***********end - sap_support***********/
    /***********start - email_support***********/
    add_settings_field( 
        'email_support', // Option ID
        'EMAIL Support URL', // Label
        'my_api_email_support_callback', // !important - This is where the args go!
        'general', // Page it will be displayed (General Settings)
        'my_settings_section', // Name of our section
        array( // The $args
            'email_support' // Should match Option ID
        )  
    ); 
    register_setting('general','email_support', 'esc_attr');
    /***********end - email_support***********/

    /***********start - sso_default_image***********/
    add_settings_field( 
        'sso_default_image', // Option ID
        'SSO Default Image', // Label
        'my_sso_default_image_callback', // !important - This is where the args go!
        'general', // Page it will be displayed (General Settings)
        'my_settings_section', // Name of our section
        array( // The $args
            'sso_default_image' // Should match Option ID
        )  
    ); 
    register_setting('general','sso_default_image', 'esc_attr');
    /***********end - sso_default_image***********/
}
/***********Start - sso default image*********/
function my_sso_default_image_callback($args) {  // number Callback
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" placeholder="File Upload"/> <br/><img src="'.$option.'" width="100">';
}
add_shortcode( 'get_sso_default_image', 'get_sso_default_image' );
function get_sso_default_image(){
    return get_option('sso_default_image');
}
/***********end - sso default image***********/

/***********start - base URL***********/
function my_api_section_options_callback() { // Section Callback
    
}

function my_api_base_url_callback($args) {  // number Callback
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" placeholder="Baseurl"/>';
}
add_shortcode( 'get_api_base_url', 'get_api_base_url' );
function get_api_base_url(){
    return get_option('api_base_url');
}
/***********end - base URL***********/

/***********start - username***********/
function my_api_username_callback($args) {  // number Callback
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" placeholder="Username"/>';
}
add_shortcode( 'get_api_username', 'get_api_username' );
function get_api_username(){
    return get_option('api_user_name');
}
/***********end - username***********/

/***********start - password***********/
function my_api_password_callback($args) {  // number Callback
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" placeholder="Password"/>';
}
add_shortcode( 'get_api_password', 'get_api_password' );
function get_api_password(){
    return get_option('api_password');
}
/***********end - password***********/
/***********start - sap_support***********/
function my_api_sap_support_callback($args) {  // number Callback
    $option = get_option($args[0]);
    echo '<input type="url" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" placeholder="SAP Support URL"/>';
}
/***********end - sap_support***********/
/***********start - email_support***********/
function my_api_email_support_callback($args) {  // number Callback
    $option = get_option($args[0]);
    echo '<input type="email" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" placeholder="EMAIL Support"/>';
}
/***********end - sap_support***********/
/***********End - add custom setting in admin side**********/


/**********Start - api call for dashboard data***********/

function sso_dashboard_count() {
    /********Start - inbox count************/
    $inbox_count = 0;
    $pending_request_count = 0;
    $po_approval_count = 0;  

    $api_base_url = get_api_base_url();
    $username = get_api_username();
    $password = get_api_password();

    $current_user = wp_get_current_user();

    $loginUserID=0;
    if(!empty($current_user))
    {
        $current_user = $current_user->user_login;
        $current_user = explode('@', $current_user);

        if(!empty($current_user[0]))
        {
            $loginUserID=$current_user[0];
        }
    }
    // $loginUserID = "16958";
    $api_url = $api_base_url."/sap/ZAPI_USER_INBOX_COUNT_SRV/zcountSet('".$loginUserID."')"; 


    $query_params = array(
    );
    $api_url = add_query_arg($query_params, $api_url);
    
    $auth_header = 'Basic ' . base64_encode($username . ':' . $password);
    $headers = array(
        'Authorization' => $auth_header,
        'Accept' => 'application/xml',
    );
    $args = array(
        'sslverify' => false,
        'headers' => $headers,
    );
    $response = wp_remote_get($api_url,$args);

    if (is_wp_error($response)) {
    
    } else {
        $data = wp_remote_retrieve_body($response);
        $response = simplexml_load_string($data);

        if(!empty($response->content->children('m', true)->properties->children('d', true)->PoCount))
        {
            $po_approval_count = (int) $response->content->children('m', true)->properties->children('d', true)->PoCount;
        }
        if(!empty($response->content->children('m', true)->properties->children('d', true)->InboxCount))
        {
            $inbox_count = (int) $response->content->children('m', true)->properties->children('d', true)->InboxCount;
        }
        if(!empty($response->content->children('m', true)->properties->children('d', true)->PendReqCount))
        {
            $pending_request_count = (int) $response->content->children('m', true)->properties->children('d', true)->PendReqCount;
        }
        
    }  
   
    $response=array(
        'success'=>1,
        'data'=>array(
            'inbox_count' => $inbox_count,
            'pending_request_count' => $pending_request_count,
            'po_approval_count' => $po_approval_count
        )
    );
    echo json_encode($response);
    exit;

}

add_action('wp_ajax_sso_dashboard_count', 'sso_dashboard_count'); // For authenticated users
add_action('wp_ajax_nopriv_sso_dashboard_count', 'sso_dashboard_count'); // For non-authenticated users

/**********End -  api call for dashboard data************/

/**********Start - slider shortcode***********/
function sso_dashboard_slider() {

    /** Start - get event list dyanmic from api**********/
    $api_base_url = get_api_base_url();
    $username = get_api_username();
    $password = get_api_password();
    $new_sliders = array();
    
    $loginUserID=0;
    $current_user = wp_get_current_user();
    if(!empty($current_user))
    {
        $current_user = $current_user->user_login;
        $current_user = explode('@', $current_user);

        if(!empty($current_user[0]))
        {
            $loginUserID=$current_user[0];
        }
    }
    // $loginUserID = "16958";
    $today = date('Y-m-d');
    $last_day = date("Y-m-d", strtotime("+1 week"));

    // $today = '2023-01-01';
    // $last_day = '2023-01-31';
    $api_url = $api_base_url.'sap/ZAPI_TEAM_EVENTS_SRV/ZteamEventsSet?%24filter=Uname%20eq%20%27'.$loginUserID.'%27&%24format=json'; 
    $query_params = array(
    );
    $api_url = add_query_arg($query_params, $api_url);
    
    $auth_header = 'Basic ' . base64_encode($username . ':' . $password);
    $headers = array(
        'Authorization' => $auth_header,
        'Accept' => 'application/json',
    );
    $args = array(
        'sslverify' => false,
        'headers' => $headers,
    );
    $response = wp_remote_get($api_url,$args);

    if (is_wp_error($response)) {
    
    } else {
        $data = wp_remote_retrieve_body($response);
        if(!empty($data))
        {
            $data = json_decode($data,true);
            if(!empty($data['d']['results']))
            {
                $sso_default_image = get_sso_default_image();
                $sliders = $data['d']['results'];
                foreach($sliders as $slide)
                {
                    $display_date_text = '';
                    $display_day = '';
                    $display_month = '';
                    $StartDate = $slide['StartDate'];
                    $display_date = '';
                    $can_display_date = 0;
                    $pattern = '/\d+/'; // Match one or more digits
                    preg_match($pattern, $StartDate, $matches);
                    if (!empty($matches)) 
                    {
                        $timestamp = $matches[0] / 1000;
                        $date = new DateTime();
                        $display_date = $date->setTimestamp($timestamp);
                        $display_date_text = $date->format('F d,Y');
                        $display_day = $date->format('d');
                        $display_month = $date->format('M');
                        $can_display_date = 1;
                    }
                    
                    
                    $new_sliders[] = array(
                        'title'=>$slide['EventDesc'],
                        'image'=>!empty($slide['EventImg'])?$slide['EventImg']:$sso_default_image,
                        'event_type'=>$slide['EventSubcategory'],
                        'display_date'=>$display_date_text,
                        'display_day'=>$display_day,
                        'display_month'=>$display_month
                    );
                }
            }
        }
        
    }  
    if(empty($new_sliders))
    {
        $sso_default_image = site_url('/salehiya/uploads/2023/11/Salehiya-def-img.jpg');
        $new_sliders[] = array(
            'title'=>'No Data', 
            'image'=>$sso_default_image,
            'event_type'=>"No Event",
            'display_date'=>date('F d,Y'),
            'display_day'=>date('d'),
            'display_month'=>date('M')
        );
    }
    if(!empty($new_sliders))
    {?>
        <div class="container">
            <div class="row">
                <div class="col col-sm-12 py-5 homepage_Slider">
                    <div class="swiper-js-container slider-a here">
                        <div class="slider_container slider_swiper swiper-slider-container slider_direction_horizontal slider_pagination slider_pagination_bullets slider_pagination_pos_bottom slider_one slider_type_bg slider_nocontrols slider_centered slider_overflow_hidden slider_titles_bottom slider_resize slider_swipe slider_height_auto swiper_027835130149666176 inited swiper-container-initialized swiper-container-horizontal" style="border-radius: 14px; display: block; opacity: 1; cursor: grab; height: 262px;" data-busy="0">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <?php
                                    foreach ($new_sliders as $index => $slide) {
                                    ?>
                                        <div class="lazyload_inited slider-slide swiper-slide with_titles<?php echo $index === 0 ? ' swiper-slide-prev' : ''; ?>" data-image="<?php echo $slide['image']; ?>" data-cats="VIEW EVENT" data-title="<?php echo $slide['title']; ?>" data-date="Event" style="background-image: url('<?php echo $slide['image']; ?>'); width: 699px;" data-slide-number="<?php echo $index; ?>" data-swiper-slide-index="<?php echo $index; ?>">
                                            <div class="slide_overlay slide_overlay_small"></div>
                                            <div class="slide_info slide_info_small">
                                                <h3 class="slide_title"><a href="#"><?php echo $slide['title']; ?></a></h3>
                                                <div class="slide_cats open_popup"><a href="javascript:void(0)" id="openModalButton" class="btn btn-primary">VIEW ALL</a></div>
                                            </div>
                                            <a href="#" class="slide_link"></a>
                                        </div>
                                        <!-- Modal Start -->
                                        <div id="modalContent" class="white-popup mfp-hide homepage_popup">
                                            <h2>Top Events</h2>
                                            <hr>
                                            <?php

                                            foreach($new_sliders as $model_slider)
                                            {
                                            ?>
                                            
                                            <div class="sc_events_item sc_item_container post_container is_block">
                                               <div class="post_featured with_thumb hover_none sc_events_item_thumb">
                                                  <div class="mask"></div>
                                                  <a href="#" class="sc_events_item_date"><span class="sc_events_item_date_day"><?=!empty($model_slider['display_day'])?$model_slider['display_day']:0;?></span><span class="sc_events_item_date_month"><?=!empty($model_slider['display_month'])?$model_slider['display_month']:''?></span></a>              
                                                  <a href="#" aria-hidden="true" class="icons"></a>
                                               </div>
                                               <div class="sc_events_item_info">
                                                  <div class="sc_events_item_header">
                                                     <h4 class="sc_events_item_title"><a href="#"><?=$model_slider['title']?></a></h4>

                                                     <?php
                                                     if(!empty($can_display_date) && $can_display_date==1)
                                                     {
                                                     ?>
                                                     <div class="sc_events_item_text"><?=$model_slider['display_date']?></div>
                                                     <?php
                                                    }
                                                     ?>
                                                     <div class="sc_events_item_additional_info"><?=$model_slider['event_type']?></div>
                                                  </div>
                                               </div>
                                            </div>
                                            <hr>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <!-- Modal End -->
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
    /** End - get event list dyanmic from api**********/

    ob_start(); // Start output buffering
    wp_enqueue_script('swiper-slider', 'https://salehiya.360websitedemo.com/salehiya/plugins/trx_addons/js/swiper/swiper.min.js');
   
    return ob_get_clean();
    $api_url = get_api_base_url().'/sap/ZAPI_TEAM_EVENTS_SRV/ZteamEventsSet';

        $query_params = array(
            '$filter' => "Uname eq '16958' and StartDate eq datetime'2023-01-01T00:00:00' and EndDate eq datetime'2023-12-31T00:00:00'",
            '$format' => 'json',
        );

        // Add the query parameters to the URL
        $api_url = add_query_arg($query_params, $api_url);

        // Define your API credentials (username and password)
        $username = get_api_username();
        $password = get_api_password();

        // Create the Authorization header with Basic Authentication
        $auth_header = 'Basic ' . base64_encode($username . ':' . $password);

        // Set headers with the Authorization header
        $headers = array(
            'Authorization' => $auth_header,
        );

        // Prepare the request arguments
        $args = array(
            'sslverify' => false,
            'headers' => $headers,
        );
        // Make the API request using wp_remote_get()
        $response = wp_remote_get($api_url,$args);

        // Check for errors
        if (is_wp_error($response)) {
            
        } else {
            // Parse and use the response data
            $data = wp_remote_retrieve_body($response);
            $data = json_decode($data,true);
            if(!empty($data['d']['results']))
            {
                $sliders = $data['d']['results'];
                foreach($sliders as $slider)
                {

                }
            }
        }     
       
   
}
add_shortcode('custom_slider', 'sso_dashboard_slider');
/**********End -  slider shortcode************/
function sso_dashboard_slider_new() {

    /** Start - get event list dyanmic from api**********/
    $api_base_url = get_api_base_url();
    $username = get_api_username();
    $password = get_api_password();
    $new_sliders = array();
    
    $loginUserID=0;
    $current_user = wp_get_current_user();
    if(!empty($current_user))
    {
        $current_user = $current_user->user_login;
        $current_user = explode('@', $current_user);

        if(!empty($current_user[0]))
        {
            $loginUserID=$current_user[0];
        }
    }
    // $loginUserID = "16958";
    $today = date('Y-m-d');
    $last_day = date("Y-m-d", strtotime("+1 week"));

    // $today = '2023-01-01';
    // $last_day = '2023-01-31';
    $api_url = $api_base_url.'sap/ZAPI_TEAM_EVENTS_SRV/ZteamEventsSet?%24filter=Uname%20eq%20%27'.$loginUserID.'%27&%24format=json'; 
    $query_params = array(
    );
    $api_url = add_query_arg($query_params, $api_url);
    
    $auth_header = 'Basic ' . base64_encode($username . ':' . $password);
    $headers = array(
        'Authorization' => $auth_header,
        'Accept' => 'application/json',
    );
    $args = array(
        'sslverify' => false,
        'headers' => $headers,
    );
    $response = wp_remote_get($api_url,$args);

    if (is_wp_error($response)) {
    
    } else {
        $data = wp_remote_retrieve_body($response);
        if(!empty($data))
        {
            $data = json_decode($data,true);
           
            if(!empty($data['d']['results']))
            {
                $sso_default_image = get_sso_default_image();
                $sliders = $data['d']['results'];
                
                foreach($sliders as $slide)
                {
                    $display_date_text = '';
                    $display_day = '';
                    $display_month = '';
                    $StartDate = $slide['StartDate'];
                    $display_date = '';
                    $can_display_date = 0;
                    $pattern = '/\d+/'; // Match one or more digits
                    $timestamp = "";
                    preg_match($pattern, $StartDate, $matches);
                    if (!empty($matches)) 
                    {
                        $timestamp = $matches[0] / 1000;
                        $date = new DateTime();
                        $display_date = $date->setTimestamp($timestamp);
                        $display_date_text = $date->format('F d,Y');
                        $display_day = $date->format('d');
                        $display_month = $date->format('M');
                        $can_display_date = 1;
                    }
                    
                    
                    $new_sliders[$timestamp] = array(
                        'title'=>$slide['EventDesc'],
                        'image'=>!empty($slide['EventImg'])?$slide['EventImg']:$sso_default_image,
                        'event_type'=>$slide['EventSubcategory'],
                        'display_date'=>$display_date_text,
                        'display_day'=>$display_day,
                        'display_month'=>$display_month
                    );
                }
            }
        }
        
    }  
    
    if(empty($new_sliders))
    {
        $sso_default_image = site_url('/salehiya/uploads/2023/11/Salehiya-def-img.jpg');
        $new_sliders[] = array(
            'title'=>'No Data', 
            'image'=>$sso_default_image,
            'event_type'=>"No Event",
            'display_date'=>date('F d,Y'),
            'display_day'=>date('d'),
            'display_month'=>date('M')
        );
    }
    if(!empty($new_sliders))
    {
        

        uksort($new_sliders, function ($a, $b) {
            $timestampA = $a;
            $timestampB = $b;

            return $timestampA - $timestampB;
        });
       
        ?>
        <div class="container splide_slider_section">
            <div class="row">
                <div class="col col-sm-12 py-5 homepage_Slider">
                    <section class="splide splide_slider_container" id="first-slider">
                      <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                                foreach ($new_sliders as $index => $slide) {
                            ?>
                                <li class="splide__slide">
                                    <div class="splide__slide_info lazyload_inited" data-image="<?php echo $slide['image']; ?>" data-cats="VIEW EVENT" data-title="<?php echo $slide['title']; ?>" data-date="Event" style="background-image: url('<?php echo $slide['image']; ?>');" data-slide-number="<?php echo $index; ?>" data-swiper-slide-index="<?php echo $index; ?>">
                                            <div class="slide_overlay slide_overlay_small"></div>
                                            <div class="slide_info slide_info_small">
                                                <h3 class="slide_title"><a href="#"><?php echo $slide['title']; ?></a></h3>
                                                <div class="slide_cats open_popup"><a href="javascript:void(0)" id="openModalButton" class="btn btn-primary">VIEW ALL</a></div>
                                                <a href="#" class="slide_link"></a>
                                            </div>
                                        </div>
                                </li>
                                <!-- Modal Start -->
                                <div id="modalContent" class="white-popup mfp-hide homepage_popup">
                                    <h2>Top Events</h2>
                                    <hr>
                                    <?php

                                    foreach($new_sliders as $model_slider)
                                    {
                                    ?>
                                    
                                    <div class="sc_events_item sc_item_container post_container is_block">
                                       <div class="post_featured with_thumb hover_none sc_events_item_thumb">
                                          <div class="mask"></div>
                                          <a href="#" class="sc_events_item_date"><span class="sc_events_item_date_day"><?=!empty($model_slider['display_day'])?$model_slider['display_day']:0;?></span><span class="sc_events_item_date_month"><?=!empty($model_slider['display_month'])?$model_slider['display_month']:''?></span></a>              
                                          <a href="#" aria-hidden="true" class="icons"></a>
                                       </div>
                                       <div class="sc_events_item_info">
                                          <div class="sc_events_item_header">
                                             <h4 class="sc_events_item_title"><a href="#"><?=$model_slider['title']?></a></h4>

                                             <?php
                                             if(!empty($can_display_date) && $can_display_date==1)
                                             {
                                             ?>
                                             <div class="sc_events_item_text"><?=$model_slider['display_date']?></div>
                                             <?php
                                            }
                                             ?>
                                             <div class="sc_events_item_additional_info"><?=$model_slider['event_type']?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <hr>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <!-- Modal End -->
                            <?php
                                }
                            ?>
                        </ul>
                      </div>
                    </section>

                </div>
            </div>
        </div>
    <?php
    }
    /** End - get event list dyanmic from api**********/

    ob_start(); // Start output buffering
    //wp_enqueue_script('swiper-slider', 'https://salehiya.360websitedemo.com/salehiya/plugins/trx_addons/js/swiper/swiper.min.js');
    wp_enqueue_script('splide-slider', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js');
    wp_enqueue_style( 'splide-style', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/css/splide.min.css', false, '1.0', 'all' ); // Inside a parent theme

   
    return ob_get_clean();
    $api_url = get_api_base_url().'/sap/ZAPI_TEAM_EVENTS_SRV/ZteamEventsSet';

        $query_params = array(
            '$filter' => "Uname eq '16958' and StartDate eq datetime'2023-01-01T00:00:00' and EndDate eq datetime'2023-12-31T00:00:00'",
            '$format' => 'json',
        );

        // Add the query parameters to the URL
        $api_url = add_query_arg($query_params, $api_url);

        // Define your API credentials (username and password)
        $username = get_api_username();
        $password = get_api_password();

        // Create the Authorization header with Basic Authentication
        $auth_header = 'Basic ' . base64_encode($username . ':' . $password);

        // Set headers with the Authorization header
        $headers = array(
            'Authorization' => $auth_header,
        );

        // Prepare the request arguments
        $args = array(
            'sslverify' => false,
            'headers' => $headers,
        );
        // Make the API request using wp_remote_get()
        $response = wp_remote_get($api_url,$args);

        // Check for errors
        if (is_wp_error($response)) {
            
        } else {
            // Parse and use the response data
            $data = wp_remote_retrieve_body($response);
            $data = json_decode($data,true);
            if(!empty($data['d']['results']))
            {
                $sliders = $data['d']['results'];
                foreach($sliders as $slider)
                {

                }
            }
        }
}
add_shortcode('custom_slider_new', 'sso_dashboard_slider_new');

add_action( 'admin_menu', 'dashboard_setting_admin_menu', 12 );

function dashboard_setting_admin_menu() {
    add_submenu_page( 'options-general.php', __( 'General Settings', 'royaltickets' ), __( 'Dashboard Options', 'alliance' ), 'manage_options', 'dashboard_setting','dashboard_setting_admin_menu_output' );
}
function dashboard_setting_admin_menu_output() {
    if(isset($_POST['save_dashboard_setting'])){
        if( isset($_POST['dashboard_banner_title']) && !empty( $_POST['dashboard_banner_title'] ) ) {
            update_option('dashboard_banner_title',$_POST['dashboard_banner_title']);
        }
        if( isset($_POST['dashboard_banner_description']) && !empty( $_POST['dashboard_banner_description'] ) ) {
            update_option('dashboard_banner_description',$_POST['dashboard_banner_description']);
        }
    }
    $dashboard_banner_title = get_option('dashboard_banner_title');
    $dashboard_banner_description = get_option('dashboard_banner_description');
    ?>
    <div class="wrap dashboard_setting_wrap">
        <h1>Dashboard Options</h1>
        <div class="row mb-6">
            <div class="col-12">
                <form method="post" action="options-general.php?page=dashboard_setting">
                    <table class="form-table">
                        <tbody>
                            <tr valign="top" class="">
                                <th scope="row">
                                    <label for="setting-royaltickets_posts_per_page">Banner Title</label>
                                </th>
                                <td class="banner_title_section">
                                    <input type="text" name="dashboard_banner_title" placeholder="Add Dashboard Banner Title Here" value="<?php echo $dashboard_banner_title; ?>">
                                </td>
                            </tr>
                            <tr valign="top" class="">
                                <th scope="row">
                                    <label for="setting-royaltickets_posts_per_page">Banner Description</label>
                                </th>
                                <td class="dashboard_banner_description_section">
                                    <input type="text" name="dashboard_banner_description" placeholder="Add Dashboard Banner Description Here" value="<?php echo $dashboard_banner_description; ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="Save" name="save_dashboard_setting">
                    </p>
                </form>
            </div>
        </div>
    </div>
    <?php
}
add_shortcode( 'dashboard_banner_title', 'get_dashboard_banner_title_fun' );
function get_dashboard_banner_title_fun(){
    return get_option('dashboard_banner_title');
}
add_shortcode( 'dashboard_banner_description', 'get_dashboard_banner_description_fun' );
function get_dashboard_banner_description_fun(){
    return get_option('dashboard_banner_description');
}
add_shortcode( 'get_current_user_name', 'get_current_user_name_fun' );
function get_current_user_name_fun(){
    $current_user = wp_get_current_user();
    
    $user_firstname = $current_user->user_firstname;
    $user_lastname  = $current_user->user_lastname;
    if(!empty($user_firstname)){
        $display_name = $user_firstname.' '.$user_lastname;
    }else{
        $display_name = $current_user->display_name;
    }
    return 'Mr. '.$display_name;
}
?>