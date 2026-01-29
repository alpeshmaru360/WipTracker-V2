
<link href="{{ asset('css/setting.css') }}" rel="stylesheet" />

<div class="sidebar-menu">
    <ul>
        <li class="menu-item">
            <a href="{{ route('setting') }}" class="menu-link">
                <span>Setting</span>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('currency') }}" class="menu-item-link">Currency Converter</a></li>
                <li><a href="{{ route('production.team.details') }}" class="menu-item-link">Production Team</a></li>
                <li><a href="{{ route('product-types') }}" class="menu-item-link">Product Types</a></li>
                <li><a href="{{ route('initial.inspection') }}" class="menu-item-link">Initial Inspection Name</a></li>
                <li><a href="{{ route('final.inspection') }}" class="menu-item-link">Final Inspection Name</a></li>
                <li><a href="{{ route('suppliers.list') }}" class="menu-item-link">Suppliers List</a></li>
                <li><a href="{{ route('procurement.std.time') }}" class="menu-item-link">Procurement Std Time</a></li>
                <li><a href="{{ route('deleted.projects.list') }}" class="menu-item-link">Deleted Projects</a></li> <!-- A Code: 31-12-2025 -->
            </ul>
        </li>
    </ul>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentUrl = window.location.href;
        let menuItems = document.querySelectorAll(".menu-item-link");
        let activeSet = false;

        menuItems.forEach(item => {
            if (item.href === currentUrl) {
                item.classList.add("active");
                activeSet = true;
            }
        });

        if (!activeSet && menuItems.length > 0) {
            menuItems[0].classList.add("active");
        }
    });
</script>



