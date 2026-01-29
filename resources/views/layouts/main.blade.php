<!DOCTYPE html>
<html lang="en-US" class="no-js scheme_default">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=no">
    <link rel="profile" href="//gmpg.org/xfn/11">
    <title>WIP Tracker</title>
    <link rel="icon" type="image/png" href="{{asset('sales_manager/uploads/logo/wilo_logo.png')}}">
    <meta name='robots' content='max-image-preview:large' />
    <link rel='dns-prefetch' href='//fonts.googleapis.com' />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="WIP Tracker" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="{{asset('sales_manager/uploads/logo/wilo_logo.png')}}" />

    <link property="stylesheet" rel='stylesheet' id='trx_addons-icons-css' href="{{asset('sales_manager/plugins/trx_addons/css/font-icons/css/trx_addons_icons.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='alliance-fontello-css' href="{{asset('sales_manager/themes/alliance/skins/default/css/font-icons/css/fontello.css')}}" media='all' />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link property="stylesheet" rel='stylesheet' id='wp-block-library-css' href="{{asset('sales_manager/includes/style.min.css')}}" media='all' />

    
    <link property="stylesheet" rel='stylesheet' id='elementor-post-3160-css' href="{{asset('sales_manager/uploads/elementor/css/post-3160.css')}}" media='all' />
    <!-- <link property="stylesheet" rel='stylesheet' id='elementor-post-3160-css' href="{{asset('sales_manager/uploads/elementor/css/post-3160.css')}}" media='(max-width:767px)' /> -->
   
    <link property="stylesheet" rel='stylesheet' id='elementor-post-5327-css' href="{{asset('sales_manager/uploads/elementor/css/post-5327.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='elementor-icons-shared-0-css' href="{{asset('sales_manager/plugins/elementor/assets/lib/font-awesome/css/fontawesome.min.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='alliance-style-css' href="{{asset('sales_manager/themes/alliance/style.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='elementor-frontend-css' href="{{asset('sales_manager/uploads/elementor/css/custom-frontend.min.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='elementor-post-8649-css' href="{{asset('sales_manager/uploads/elementor/css/post-8649.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='trx_addons-css' href="{{asset('sales_manager/plugins/trx_addons/css/__styles.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='trx_addons-sc_content-responsive-css' href="{{asset('sales_manager/plugins/trx_addons/components/shortcodes/content/content.responsive.css')}}" media='(max-width:1439px)' />

    <link property="stylesheet" rel='stylesheet' id='trx_addons-animations-css' href="{{asset('sales_manager/plugins/trx_addons/css/trx_addons.animations.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='alliance-plugins-css' href="{{asset('sales_manager/themes/alliance/skins/default/css/__plugins.css')}}" media='all' />

    <link property="stylesheet" rel='stylesheet' id='alliance-custom-css' href="{{asset('sales_manager/themes/alliance/skins/default/css/__custom.css')}}" media='all' />
  
    <link property="stylesheet" rel='stylesheet' id='alliance-child-css' href="{{asset('sales_manager/themes/alliance-child/style.css')}}" media='all' />


    <link property="stylesheet" rel='stylesheet' id='trx_addons-responsive-css' href="{{asset('sales_manager/plugins/trx_addons/css/__responsive.css')}}" 
    media='(max-width:1439px)' />

    <!-- <link property="stylesheet" rel='stylesheet' id='alliance-responsive-css' href="{{asset('sales_manager/themes/alliance/skins/default/css/__responsive.css')}}" 
    media='(max-width:1679px)' /> -->

    <link property="stylesheet" rel='stylesheet' id='alliance-responsive-css' href="{{asset('sales_manager/themes/alliance/skins/default/css/__responsive.css')}}" 
    media='(max-width:767px)' />

    <link property="stylesheet" rel='stylesheet' id='alliance-skin-custom-css-default-css' href="{{asset('sales_manager/themes/alliance/skins/default/css/extra-style.css')}}" media='all' />

    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>

    <link rel="stylesheet" href="{{asset('css/admin_css1.css')}}">
    <!-- Font Awesome -->
    <link href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet" />
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('css/ionicons.min.css')}}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <!-- JQVMap -->
    <link href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet" />
    <!-- Theme style -->
    <link href="{{ asset('dist/css/adminlte.min.css') }}" rel="stylesheet" />
    <!-- overlayScrollbars -->
    <link href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}" rel="stylesheet" />
    <!-- Daterange picker -->
    <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" />
    <!-- summernote -->
    <link href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}" rel="stylesheet" />
    <!-- Custom admin styling -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet" />
    {{--<link href="{{ asset('css/operator.css') }}" rel="stylesheet" />--}}
    <link href="{{ asset('css/manager.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/role.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <script src="https://js.pusher.com/8.3.0/pusher.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  
</head>

@php
    $role = '';
    $role_lable = '';
@endphp
@if(Auth::check())
    @if(Auth::user()->role == "Sales Manager")
        @php $role = "Sales Manager"; @endphp 
    @else 
        @php $role = "Other"; @endphp
    @endif
@endif
@php
    if(Auth::user()){
        $role_lable = Auth::user()->role;
    }else{
        $role_lable = "No Role";
    }
@endphp
<!-- <body class="home-page bp-legacy home page-template-default page page-id-8649 logged-in wp-custom-logo pmpro-body-has-access preloader frontpage allow_lazy_load 
skin_default page_content_blocks show_page_title debug_off scheme_default blog_mode_front body_style_wide  is_stream blog_style_excerpt_2 
sidebar_hide expand_content trx_addons_present header_type_custom header_style_header-custom-3160 header_position_default menu_side_present no_layout 
fixed_blocks_sticky elementor-default elementor-kit-141 elementor-page elementor-page-8649 no-js {{$role == 'Other' ? 'menu_side_left' : ''}}"> -->

<body class="home allow_lazy_load debug_off scheme_default sidebar_hide    menu_side_present    no_layout no-js {{$role == 'Other' ? 'menu_side_left' : ''}}">

    <div id="page_preloader">
        <div class="preloader_wrap preloader_square">
            <div class="preloader_square1"></div>
            <div class="preloader_square2"></div>
        </div>
    </div>
    <div class="body_wrap">
        <div class="page_wrap">
            <!-- <a class="alliance_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="1">Skip to content</a>
            <a class="alliance_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="1">Skip to footer</a> -->
            <header class="top_panel top_panel_custom top_panel_custom_3160 top_panel_custom_header without_bg_image d-flex">            
                <div data-elementor-type="cpt_layouts" data-elementor-id="3160" class="elementor elementor-3160">
                    <section class="elementor-section elementor-top-section elementor-element elementor-section-content-middle sc_layouts_row sc_layouts_row_type_narrow sc_layouts_row_fixed sc_layouts_row_fixed_always  elementor-section-height-min-height elementor-section-full_width header_section elementor-section-height-default elementor-section-items-middle sc_fly_static {{$role == 'Other' ? 'elementor-element-559c821' : 'elementor-element-559c822'}}"
                    data-id="559c821" data-element_type="section" data-settings="{&quot;stretch_section&quot;:&quot;section-stretched&quot;}">
                        <div class="elementor-container elementor-column-gap-no">
                            <div class="elementor-column elementor-col-33 elementor-top-column elementor-element elementor-element-595190e sc_layouts_hide_on_mobile sc-tablet_layouts_column_align_left sc_layouts_column sc_inner_width_none sc_content_align_inherit sc_layouts_column_icons_position_left sc_fly_static"
                            data-id="595190e" data-element_type="column">
                                <div class="elementor-widget-wrap elementor-element-populated">
                                    
                                    <div class="sc_layouts_item elementor-element elementor-element-9e5daa3 header_logo_img sc_fly_static elementor-widget elementor-widget-image" 
                                         data-id="9e5daa3" data-element_type="widget" data-widget_type="image.default">

                                        @php
                                            $roleToRoute = [
                                                "Admin"                 => "AdminDashboard",
                                                "Assembly Manager"      => "AssemblyManagerDashboard",
                                                "Quality Engineer"      => "QualityManagerDashboard",
                                                "Procurement Specialist"=> "ProcurementManagerDashboard",
                                                "Sale Manager"          => "ExpectedOrdersDashboard",
                                                "Production Engineer"   => "ProductionManagerDashboard",
                                                "Designer Engineer"     => "DesignerEngineerDashboard",
                                                "Production Superwisor" => "ProductionSuperwisorDashboard",
                                                "Wilo Operator"              => "OperatorDashboard",
                                                "User"                  => "UserDashboard",
                                            ];
                                            $dashboardRoute = $roleToRoute[$role_lable] ?? "AdminDashboard";
                                        @endphp

                                        <div class="elementor-widget-container">
                                            <a class="wilo_logo" href="{{ route($dashboardRoute) }}">
                                                <img 
                                                    width="418" 
                                                    height="224" 
                                                    src="{{ asset('sales_manager/uploads/logo/wilo_logo.png') }}"
                                                    class="lazyload_inited attachment-full size-full wp-image-10301 wiptracker_logo"
                                                    alt="Wilo Logo"
                                                    sizes="(max-width: 600px) 100vw, 418px"
                                                />
                                            </a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-column elementor-col-66 elementor-element 
                            elementor-element-6175f6b sc_layouts_column_align_right sc_layouts_column sc-mobile_content_align_inherit sc-tablet_layouts_column_align_right sc_layouts_column sc_inner_width_none sc_content_align_inherit sc_layouts_column_icons_position_left sc_fly_static"
                            data-id="6175f6b" data-element_type="column">
                                <div class="elementor-widget-wrap elementor-element-populated">

                                    <div class="sc_layouts_item elementor-element elementor-element-85d476b elementor-view-default sc_fly_static elementor-widget 
                                    elementor-widget-icon mobile_profile" data-id="85d476b" data-element_type="widget" data-widget_type="icon.default">
                                        <div class="elementor-widget-container">
                                            <div class="elementor-icon-wrapper">
                                                <div class="elementor-icon">
                                                    <i aria-hidden="true" class="far fa-bell"></i> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    

                                    <div class="sc_layouts_login sc_layouts_menu desktop_profile">
                                        <ul class="sc_layouts_dropdown sc_layouts_menu_nav">
                                            <li class="menu-item menu-item-has-children">
                                                <a href="#" class="trx_addons_login_link">
                                                    <span class="sc_layouts_item_avatar">                                                  
                                                     
                                                        <!-- A Code: 15-12-2025 Start -->
                                                        @if(file_exists(public_path('production_team/all_user_profile_pic/' . Auth::user()->profile_pic)) && !empty(Auth::user()->profile_pic))
                                                        <img src="{{ asset('production_team/all_user_profile_pic/' . Auth::user()->profile_pic) }}" alt="Profile Pic" class="profile-pic">
                                                        @else
                                                        <img id="profilePicPreview" src="{{ asset('images/blank_user.jpg') }}" alt="Profile Picture" class="profile-pic mb-2" height="100" width="100">
                                                        @endif
                                                        <!-- A Code: 15-12-2025 End -->
                                                        
                                                    </span>
                                                    <span class="sc_layouts_item_details">
                                                        <span class="sc_layouts_item_details_line1">Hi,</span>
                                                        <span class="sc_layouts_item_details_line2">
                                                            @if(Auth::check())
                                                                {{Auth::user()->name}} 
                                                                <!-- A Code: 22-12-2025 Start -->
                                                                @if(Auth::user()->is_admin_login && Auth::user()->role != "Admin")
                                                                    ({{Auth::user()->role}}) 
                                                                @endif
                                                                <!-- A Code: 22-12-2025 Start -->
                                                            @else
                                                                User
                                                            @endif
                                                        </span>
                                                    </span>
                                                </a>
                                                <ul>
                                                    <li class="trx_addons_icon-user-times">
                                                        <a href="{{route('Logout')}}"><span>Logout</span></a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </header>
            @if($role == 'Other')
                @include('partials.menu')
            @endif 
            @yield('content')
        </div>
    </div>
    
<a href="#" class="trx_addons_scroll_to_top trx_addons_icon-up" title="Scroll to top"><span class="trx_addons_scroll_progress trx_addons_scroll_progress_type_round"></span></a>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables Responsive CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

<script defer="defer" src="{{asset('sales_manager/plugins/m-chart-highcharts-library/components/external/highcharts/highcharts.js')}}" id="highcharts-js"></script>

<script defer="defer" src="{{asset('sales_manager/plugins/trx_addons/js/__scripts.js')}}" id="trx_addons-js"></script>

<script defer="defer" src="{{asset('sales_manager/plugins/trx_addons/components/cpt/layouts/shortcodes/menu/superfish.min.js')}}" id="superfish-js"></script>

<script src="{{asset('sales_manager/plugins/trx_addons/js/tweenmax/tweenmax.min.js')}}" id="tweenmax-js"></script>


<script defer="defer" src="{{asset('sales_manager/plugins/buddypress-media/lib/media-element/mediaelement-and-player.min.js')}}" id="rt-mediaelement-js"></script>

<script defer="defer" src="{{asset('sales_manager/plugins/buddypress-media/lib/touchswipe/jquery.touchSwipe.min.js')}}" id="rtmedia-touchswipe-js"></script>

<script defer="defer" src="{{asset('sales_manager/themes/alliance/js/__scripts.js')}}" id="alliance-init-js"></script>


<script defer="defer" src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js" id="splide-slider-js"></script>
<script src="{{asset('sales_manager/plugins/elementor/assets/js/webpack.runtime.min.js')}}" id="elementor-webpack-runtime-js"></script>

<div id="sitewide-notice" class="admin-bar-off"></div>
<!-- Datatable -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.1/css/responsive.bootstrap4.min.css">

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.1/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@yield('scripts')
</body>
</html>