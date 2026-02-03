@extends('layouts.main')

@section('content')

<link href="{{ asset('css/setting.css') }}" rel="stylesheet" />

<div class="sidebar-menu">
    <ul>
        <li class="menu-item">
            <a href="{{ route('kpi-reports') }}" class="menu-link">
                <span>KPI REPORTS</span>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('kpi-reports') }}" class="menu-item-link {{ request()->routeIs('kpi-reports') ? 'active' : '' }}">Allocated month </a></li>
                <li><a href="{{ route('kpi-reports.manpower-efficiency') }}" class="menu-item-link">Manpower Efficiency</a></li>
                <li><a href="{{ route('kpi-reports.throughput-time') }}" class="menu-item-link">Throughput Time</a></li>
                <li><a href="{{ route('kpi-reports.delivery-on-time') }}" class="menu-item-link">Delivery on time</a></li>
                <li><a href="{{ route('kpi-reports.finished-goods') }}" class="menu-item-link">Finished good per employee hour</a></li>
                <li><a href="{{ route('kpi-reports.coverage-rate') }}" class="menu-item-link">Coverage Rate</a></li>
                <li><a href="{{ route('kpi-reports.vsi') }}" class="menu-item-link">VSI</a></li>
                <li><a href="{{ route('kpi-reports.monthly-kpis') }}" class="menu-item-link">MONTHLY KPIs TRACKING</a></li>
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


@endsection
