@php
  $role = Auth::user()->role;
@endphp
@if($role != "Operator" && $role != "User")
<div class="menu_mobile menu_mobile_narrow is_opened">
    <div class="menu_mobile_inner">
        <div class="menu_mobile_top_panel">
            <a class="menu_mobile_close theme_button_close" tabindex="0"><span class="theme_button_close_icon"></span></a>
                @if($role == "Admin")
                    <a class = "sc_layouts_logo" href="{{route('AdminDashboard')}}">
                @elseif($role == "Assembly Manager")
                    <a class = "sc_layouts_logo" href="{{route('AssemblyManagerDashboard')}}">
                @elseif($role == "Quality Engineer")
                    <a class = "sc_layouts_logo" href="{{route('QualityManagerDashboard')}}">
                @elseif($role == "Procurement Specialist")
                    <a class = "sc_layouts_logo" href="{{route('ProcurementManagerDashboard')}}">
                @elseif($role == "Sale Manager")
                    <a class = "sc_layouts_logo" href="{{route('SalesManagerControllerDashboard')}}">
                @elseif($role == "Production Engineer")
                    <a class = "sc_layouts_logo" href="{{route('ProductionManagerDashboard')}}">
                @elseif($role == "Designer Engineer")
                    <a class = "sc_layouts_logo" href="{{route('DesignerEngineerDashboard')}}">
                @elseif($role == "Production Superwisor")
                    <a class = "sc_layouts_logo" href="{{route('ProductionSuperwisorDashboard')}}">
                @elseif($role == "Wilo Operator")
                    <a class = "sc_layouts_logo" href="{{route('OperatorDashboard')}}">
                @elseif($role == "3rd Party Operator")
                    <a class = "sc_layouts_logo" href="{{route('OperatorDashboard')}}">        
                @elseif($role == "User")
                    <a class = "sc_layouts_logo" href="{{route('UserDashboard')}}">
                @elseif($role == "Estimation Manager")
                    <a class = "sc_layouts_logo" href="{{route('EstimationManagerDashboard')}}">
                @else
                    <a href="{{route('AdminDashboard')}}" rel="noopener" aria-current="page">
                @endif
                <span>Home</span>
            </a>
        </div>

        <nav class="menu_mobile_nav_area" itemscope="itemscope" itemtype="">
            <ul id="mobile-menu_mobile" class="pt-3 menu_mobile_nav">
                
                <li id="mobile-menu-item-9071" class="icon-home-3 menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-9071">
                    @if($role == "Admin")
                        <a title="Dashboard" rel="noopener" href="{{route('AdminDashboard')}}">
                    @elseif($role == "Assembly Manager")
                        <a title="Dashboard" rel="noopener" href="{{route('AssemblyManagerDashboard')}}">
                    @elseif($role == "Quality Engineer")
                        <a title="Dashboard" rel="noopener" href="{{route('QualityManagerDashboard')}}">
                    @elseif($role == "Procurement Specialist")
                        <a title="Dashboard" rel="noopener" href="{{route('ProcurementManagerDashboard')}}">
                    @elseif($role == "Sale Manager")
                        <a title="Dashboard" rel="noopener" href="{{route('SalesManagerControllerDashboard')}}">
                    @elseif($role == "Production Engineer")
                        <a title="Dashboard" rel="noopener" href="{{route('ProductionManagerDashboard')}}">
                    @elseif($role == "Designer Engineer")
                        <a title="Dashboard" rel="noopener" href="{{route('DesignerEngineerDashboard')}}">
                    @elseif($role == "Production Superwisor")
                        <a title="Dashboard" rel="noopener" href="{{route('ProductionSuperwisorDashboard')}}">
                    @elseif($role == "Operator")
                        <a title="Dashboard" rel="noopener" href="{{route('OperatorDashboard')}}">
                    @elseif($role == "Wilo Operator")
                        <a title="Dashboard" rel="noopener" href="{{route('OperatorDashboard')}}">
                    @elseif($role == "3rd Party Operator")
                        <a title="Dashboard" rel="noopener" href="{{route('OperatorDashboard')}}">       
                    @elseif($role == "User")
                        <a title="Dashboard" rel="noopener" href="{{route('UserDashboard')}}">
                    @elseif($role == "Estimation Manager")
                        <a title="Dashboard" rel="noopener" href="{{route('EstimationManagerDashboard')}}">
                    @else
                        <a href="{{route('AdminDashboard')}}" rel="noopener" aria-current="page">
                    @endif
                        <span>Home</span>
                    </a>
                </li>
                
                @if($role == "Production Superwisor")
                    <li id="mobile-menu-item-9460" class="icon-email-3 menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                        <a title="Inbox"  rel="noopener" href="{{route('ProductionSuperwisorInbox')}}">
                            <span>Inbox</span>
                        </a>
                    </li>
                @endif
                
                @if($role == "Estimation Manager")
                <li id="mobile-menu-item-9460" class="icon-email-3 menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                        <a title="Inbox"  rel="noopener" href="{{route('EstimationManagerInbox')}}">
                            <span>Inbox</span>
                        </a>
                    </li>
                @endif
                
                @if($role == "Production Engineer")
                <li id="mobile-menu-item-9581" class="icon-file-powerpoint menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                    <a title="Projects"  rel="noopener" href="{{route('ProductionManagerProjectIndex')}}">
                        <span>Projects</span>
                    </a>
                </li>
                @else
                 <li id="mobile-menu-item-9581" class="icon-file-powerpoint menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                    <a title="Projects"  rel="noopener" href="">
                        <span>Projects</span>
                    </a>
                </li>
               @endif

                <li id="mobile-menu-item-9581" class="icon-document menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                    <a title="PURCHASE ORDERS"  rel="noopener">
                        <span>PURCHASE ORDERS</span>
                    </a>
                </li>

                <!-- janasi 17/12 - STOCK page only visible for Production Engineer -->
                <li id="mobile-menu-item-9581" class="icon-cart-2 menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                    <a title="STOCK" rel="noopener" href="{{ route('StockPage') }}">
                        <span>STOCK</span>
                    </a>
                </li>


                 <li id="mobile-menu-item-9581" class="icon-star-filled menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                    <a title="QUALITY"  rel="noopener">
                        <span>QUALITY</span>
                    </a>
                </li>

                <li id="mobile-menu-item-9581" class="icon-window-maximize menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                    <a title="KPI REPORTS"  rel="noopener">
                        <span>KPI REPORTS</span>
                    </a>
                </li>
                    
                <li id="mobile-menu-item-9581" class="icon-chat-empty menu-item menu-item-type-custom menu-item-object-custom menu-item-9581">
                    <a title="CHAT"  rel="noopener">
                        <span>CHAT</span>
                    </a>
                </li>
             
                <li id="mobile-menu-item-9902" class="icon-settings menu-item menu-item-type-custom menu-item-object-custom menu-item-9902">
                    @if($role == "Admin")
                        <a title="Settings" rel="noopener" href="{{route('AdminSettingsForm')}}">
                    @else
                        <a title="Settings" rel="noopener" href=""> 
                    @endif
                        <span>Settings</span>
                        </a>
                </li>
                <!-- janasi - 16/12 -Settings page only visible for Admi -->    
                @if(auth()->user()->role == "Admin")
                    <li id="mobile-menu-item-9902" class="icon-cogs menu-item menu-item-type-custom menu-item-object-custom">
                        <a title="Settings" rel="noopener" href="{{ route('SettingsPage') }}">
                            <span>Settings</span>
                        </a>
                    </li>
                @else
                    <li id="mobile-menu-item-9902" class="icon-cogs menu-item menu-item-type-custom menu-item-object-custom">
                        <a title="Settings" rel="noopener" href="">
                            <span>Settings</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
@endif


