@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/procurement_manager.css') }}" />

<div class="procurement_dashboard_page main_section bg-white m-4">
    @include('manager.dashboard')
    <h3 class="px-5 pt-4 text-bold text-left text-uppercase">{{$page_title}}</h3>
    <hr class="mx-5" />
    <div class="mx-5">
        <div class="row">
            <div class="col-xl-12 px-0 mx-3">    

                <div class="d-sm-flex flex-wrap">
                    <h4 class="card-title mb-4 chart_title"></h4>
                    <div class="ms-auto">
                        <ul class="nav nav-pills">
                            <li data-value="week" class="nav-item loadkpiChart1">
                                <a class="nav-link" href="javascript: void(0);">{{__('Week')}}</a>
                            </li>
                            <li data-value="month" class="nav-item loadkpiChart1">
                                <a class="nav-link" href="javascript: void(0);">{{__('Month')}}</a>
                            </li>
                            <li data-value="quarter" class="nav-item loadkpiChart1">
                                <a class="nav-link" href="javascript: void(0);">{{__('Quarter')}}</a>
                            </li>
                            <li data-value="year" class="nav-item loadkpiChart1">
                                <a class="nav-link active" href="javascript: void(0);">{{__('Year')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="chart_body pb-5">
                    <canvas id="myChart" width="600" height="250" class="mylinechart"></canvas>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 px-0 mx-3">
                <div class="d-sm-flex flex-wrap">
                    <h4 class="card-title mb-4 chart_title"></h4>
                    <div class="ms-auto">
                        <ul class="nav nav-pills">
                            <li data-value="week" class="nav-item loadkpiChart2">
                                <a class="nav-link" href="javascript: void(0);">{{__('Week')}}</a>
                            </li>
                            <li data-value="month" class="nav-item loadkpiChart2">
                                <a class="nav-link" href="javascript: void(0);">{{__('Month')}}</a>
                            </li>
                            <li data-value="quarter" class="nav-item loadkpiChart2">
                                <a class="nav-link" href="javascript: void(0);">{{__('Quarter')}}</a>
                            </li>
                            <li data-value="year" class="nav-item loadkpiChart2">
                                <a class="nav-link active" href="javascript: void(0);">{{__('Year')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="chart_body pb-5">
                    <canvas id="myChart2" width="600" height="250" class="mylinechart"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 px-0 mx-3">
                <div class="d-sm-flex flex-wrap">
                    <h4 class="card-title mb-4 chart_title"></h4>
                    <div class="ms-auto">
                        <ul class="nav nav-pills">
                            <li data-value="week" class="nav-item loadkpiChart3">
                                <a class="nav-link" href="javascript: void(0);">{{__('Week')}}</a>
                            </li>
                            <li data-value="month" class="nav-item loadkpiChart3">
                                <a class="nav-link" href="javascript: void(0);">{{__('Month')}}</a>
                            </li>
                            <li data-value="quarter" class="nav-item loadkpiChart3">
                                <a class="nav-link" href="javascript: void(0);">{{__('Quarter')}}</a>
                            </li>
                            <li data-value="year" class="nav-item loadkpiChart3">
                                <a class="nav-link active" href="javascript: void(0);">{{__('Year')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="chart_body pb-5">
                    <canvas id="myChart3" width="600" height="250" class="mylinechart"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 px-0 mx-3">
                <div class="d-sm-flex flex-wrap">
                    <h4 class="card-title mb-4 chart_title"></h4>
                    <div class="ms-auto">
                        <ul class="nav nav-pills">
                            <li data-value="week" class="nav-item loadkpiChart4">
                                <a class="nav-link" href="javascript: void(0);">{{__('Week')}}</a>
                            </li>
                            <li data-value="month" class="nav-item loadkpiChart4">
                                <a class="nav-link" href="javascript: void(0);">{{__('Month')}}</a>
                            </li>
                            <li data-value="quarter" class="nav-item loadkpiChart4">
                                <a class="nav-link" href="javascript: void(0);">{{__('Quarter')}}</a>
                            </li>
                            <li data-value="year" class="nav-item loadkpiChart4">
                                <a class="nav-link active" href="javascript: void(0);">{{__('Year')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="chart_body pb-5">
                    <canvas id="myChart4" width="600" height="250" class="mylinechart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>  
@endsection

@section('scripts') 
<script type="text/javascript">
$(document).ready(function () {
    function initializeChart(chartId, titleText) {
        return new Chart(document.getElementById(chartId).getContext("2d"), {
            type: "bar",
            data: { labels: [], datasets: [] },
            options: {
                scales: { yAxes: [{ ticks: { beginAtZero: true } }] },
                title: { display: true, text: titleText },
                responsive: true,
                tooltips: {
                    callbacks: {
                        labelColor: () => ({
                            borderColor: "rgb(255, 0, 20)",
                            backgroundColor: "rgb(255, 20, 0)",
                        }),
                    },
                },
                legend: { labels: { fontColor: "Black" } },
            },
        });
    }

    const myChart = initializeChart("myChart", "Suppliers");
    const myChart2 = initializeChart("myChart2", "Orders");
    const myChart3 = initializeChart("myChart3", "Article No");
    const myChart4 = initializeChart("myChart4", "Orders");

    function updateChart(chart, data) {
        chart.data.labels = data.labels;
        chart.data.datasets = data.datasets;
        chart.update();
    }

    function fetchChartData(buttonClass, chart, url) {
        $(document).on("click", buttonClass, function () {
            $(buttonClass + " a").removeClass("active");
            $(this).find("a").addClass("active");
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                    days: $(this).data("value"),
                },
                success: (response) => updateChart(chart, response),
                error: (xhr, status, error) => console.error("Error fetching chart data:", error),
            });
        });
    }

    fetchChartData(".loadkpiChart1", myChart, "{{ url('supplierOrdersChart') }}");
    fetchChartData(".loadkpiChart2", myChart2, "{{ url('totalOrdersChart') }}");
    fetchChartData(".loadkpiChart3", myChart3, "{{ url('articleOrdersChart') }}");
    fetchChartData(".loadkpiChart4", myChart4, "{{ url('totalArticlesChart') }}");

    updateChart(myChart, @json($chartData));
    updateChart(myChart2, @json($chart2Data));
    updateChart(myChart3, @json($chart3Data));
    updateChart(myChart4, @json($chart4Data));

});
</script>
@endsection