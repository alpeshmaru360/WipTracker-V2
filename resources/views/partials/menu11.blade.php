<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{route('ManagerDashboard')}}" class="nav-link">
               <i class="nav-icon fas fa-tachometer-alt"></i>
                DASHBOARD
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
               <i class="nav-icon fas fa-tachometer-alt"></i>
                INBOX
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
               <i class="nav-icon fas fa-tachometer-alt"></i>
                PROJECTS
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
               <i class="nav-icon fas fa-tachometer-alt"></i>
                PURCHASE ORERS
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
               <i class="nav-icon fas fa-tachometer-alt"></i>
                STOCK
            </a>
        </li>
        <li class="nav-item">
            <a href="" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                QUALITY
            </a>
        </li>
		
         <li class="nav-item">
            <a href="" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
               KPI REPORTS
            </a>
        </li>
        <li class="nav-item">
            <a href="" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
               CHAT
            </a>
        </li>
        <li class="nav-item">
            <a href="" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
               USER SETTINGS
            </a>
        </li>	
        <li class="nav-item">
            <a href="{{route('Logout')}}" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                LOGOUT
            </a>
        </li>   	
    </ul>
</nav>




















