@php
$role = Auth::user()->role;
@endphp

<style type="text/css">
@if($role =="User" || preg_match('/^Operator[0-9]*$/', $role)) 
    .menu_mobile {box-shadow: none !important;}
@endif
</style>

<div class="menu_mobile menu_mobile_narrow is_opened">
    @if($role != "User" && !preg_match('/^Operator[0-9]*$/', $role))
    @php
        $dashboardRoutes = [
            'Admin' => 'AdminDashboard',
            'Assembly Manager' => 'AssemblyManagerDashboard',
            'Quality Engineer' => 'QualityManagerDashboard',
            'Procurement Specialist' => 'ProcurementManagerDashboard',
            'Production Engineer' => 'ProductionManagerDashboard',
            'Designer Engineer' => 'DesignerEngineerDashboard',
            'Production Superwisor' => 'ProductionSuperwisorDashboard',
            'Operator' => 'OperatorDashboard',
            'Estimation Manager' => 'EstimationManagerDashboard',
        ];

        // Determine route based on role (trimmed + fallback)
        $roleKey = trim($role);
        $dashboardRoute = $dashboardRoutes[$roleKey] ?? 'AdminDashboard';

        // List of all dashboard routes for active state detection
        $allDashboardRoutes = array_values($dashboardRoutes); 
    @endphp

    <div class="menu_mobile_inner">
        <div class="menu_mobile_top_panel">
            <a class="menu_mobile_close theme_button_close" tabindex="0">
                <span class="theme_button_close_icon"></span>
            </a>

            <a class="sc_layouts_logo" href="{{ route($dashboardRoute) }}">               
                <img width="418" 
                    height="224" 
                    src="{{ asset('sales_manager/uploads/logo/wilo_logo.png') }}"
                    class="lazyload_inited attachment-full size-full wp-image-10301 wiptracker_logo"
                    alt="Wilo Logo"
                    sizes="(max-width: 600px) 100vw, 418px" />
            </a>    
        </div>

        <nav class="menu_mobile_nav_area" itemscope="itemscope" itemtype="">
            <ul id="mobile-menu_mobile" class="pt-3 menu_mobile_nav"> 

                <li class="icon-home-3 menu-item menu-item-type-custom menu-item-object-custom menu-item-home {{ Request::routeIs($allDashboardRoutes) || Request::is('/') ? 'current-menu-item' : '' }}">
                    
                    <a class="sc_layouts_logo home_icon"
                       href="{{ route($dashboardRoute) }}"
                       title="Dashboard"
                       data-toggle="tooltip"
                       rel="noopener"
                       aria-current="page">
                        <span>Home</span>
                    </a>
                </li>  

                <!-- Add Inbox Menu Just After Dashboard -->
                @php
                    $inboxRoutes = [
                        "Production Superwisor"   => "ProductionSuperwisorInbox",
                        "Procurement Specialist"  => "ProcurementManagerInbox",
                        "Estimation Manager"      => "EstimationManagerInbox",
                        "Assembly Manager"        => "AssemblyManagerInbox",
                        "Production Engineer"     => "ProductionManagerInbox",
                        "Quality Engineer"        => "QualityManagerInbox"
                    ];

                    $inboxRoute = $inboxRoutes[$role] ?? null;
                    $isActive = Request::routeIs($inboxRoute);
                    $hasUnread = $inboxUnreadCount > 0;
                @endphp

                @if($inboxRoute)
                    <li id="mobile-menu-item-inbox"
                        class="icon-email-3 menu-item menu-item-type-custom menu-item-object-custom inbox_menu
                               {{ $isActive ? 'current-menu-item' : '' }}
                               {{ $hasUnread ? 'active_inbox' : '' }}">
                        <a title="Inbox" rel="noopener" href="{{ route($inboxRoute) }}" data-toggle="tooltip">
                            <span>Inbox</span>
                        </a>
                    </li>
                @endif

                <li class="icon-file-powerpoint menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('ProductionManagerProjectIndex')) current-menu-item @endif">
                    <a title="Projects" rel="noopener" href="{{route('ProductionManagerProjectIndex')}}" data-toggle="tooltip">
                        <span>Projects</span>
                    </a>
                </li>
                
                <li class="icon-parking menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('product_tracking')) current-menu-item @endif">
                    <a title="Product Tracking" rel="noopener" href="{{route('product_tracking')}}" data-toggle="tooltip">
                        <span>Product Tracking</span>
                    </a>
                </li>          

                @if($role == "Production Superwisor" || $role == "Admin" || Auth::user()->is_admin_login) {{-- A Code: 22-12-2025 --}}
                    <li class="icon-th-large menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('OperatorTracking')) current-menu-item @endif">
                        <a title="Operator Tracking" rel="noopener" href="{{route('OperatorTracking')}}" data-toggle="tooltip">
                            <span>Operator Tracking</span>
                        </a>
                    </li>
                @endif
                
                <li class="icon-document menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('PurchaseOrder')) current-menu-item @endif">
                    <a title="PURCHASE ORDERS" rel="noopener" href="{{route('PurchaseOrder')}}" data-toggle="tooltip">
                        <span>PURCHASE ORDERS</span>
                    </a>
                </li>

                @if($role == "Sale Manager")
                    <li id="mobile-menu-item-9460" class="icon-cart-2 menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('ExpectedOrdersDashboard')) current-menu-item @endif">
                        <a title="Expected Orders" rel="noopener" href="{{route('ExpectedOrdersDashboard')}}" data-toggle="tooltip">
                            <span>Expected Orders</span>
                        </a>
                    </li>
                @endif             

                <li class="trx_addons_icon-table menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('Stock')) current-menu-item @endif">
                    <a title="Stock" rel="noopener" href="{{route('Stock')}}" data-toggle="tooltip">
                        <span>Stock</span>
                    </a>
                </li> 
                <li class="icon-star-filled menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('QUALITY')) current-menu-item @endif">
                    <a title="QUALITY" rel="noopener" href="{{route('QUALITY')}}" data-toggle="tooltip">
                        <span>QUALITY</span>
                    </a>
                </li>

                {{-- A Code: 23-01-2026 Start --}}
                @if($role == "Quality Engineer")
                    <li class="icon-flag menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('qualityAction')) current-menu-item @endif">
                        <a title="Action" rel="noopener" href="{{route('qualityAction')}}" data-toggle="tooltip">
                            <span>Action</span>
                        </a>
                    </li>
                @endif
                {{-- A Code: 23-01-2026 End --}}

                @if($role == "Admin" || Auth::user()->is_admin_login) {{-- A Code: 17-12-2025 --}}
                    <li class="icon-user menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('AdminUsersIndex')) current-menu-item @endif">
                        <a title="Users" rel="noopener" href="{{ route('AdminUsersIndex') }}" data-toggle="tooltip">
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="icon-users menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('AdminRoles')) current-menu-item @endif">
                        <a title="Roles" rel="noopener" href="{{ route('AdminRoles') }}" data-toggle="tooltip">
                            <span>Roles</span>
                        </a>
                    </li>
                    <li class="icon-clock menu-item menu-item-type-custom menu-item-object-custom @if(Request::routeIs('AdminHoursSettings')) current-menu-item @endif">
                        <a title="Process Hours" rel="noopener" href="{{route('AdminHoursSettings')}}" data-toggle="tooltip">
                            <span>Process Hours</span>
                        </a>
                    </li>
                    @php
                        $settingsRoutes = [
                            'currency',
                            'production.team.details',
                            'product-types',
                            'initial.inspection',
                            'final.inspection',
                            'suppliers.list',
                            'procurement.std.time',
                        ];
                    @endphp
                    <li class="icon-settings menu-item menu-item-type-custom menu-item-object-custom {{ Request::routeIs($settingsRoutes) ? 'current-menu-item' : '' }}">                        
                        <a title="Setting" rel="noopener" href="{{ route('currency') }}" data-toggle="tooltip">
                            <span>Setting</span>
                        </a>
                    </li>
                @endif               

            </ul>
        </nav>
    </div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("[data-toggle='tooltip']").tooltip();
    });
</script>