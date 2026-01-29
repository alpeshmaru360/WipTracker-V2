@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/quality_manager.css') }}" />

<div class="quality_dashboard_page main_section bg-white m-4 pb-5">
    @include('manager.dashboard')

    <div class="row m-4 px-3">

        <div class="col-xl-12 col-sm-12 mb-4 pie_chart_section d-none">
            <div id="pie_chart" class="chart-container"></div>
        </div>

        <div class="col-sm-12 col-xl-12 mx-auto today_process mt-3">
            <div class="row">
                <span class="w-100 py-2 px-4 fs-22 font-weight-bolder">Today's Progess</span>
            </div>

            <div class="months_row_second mb-3 mt-3 mx-3">
                <span class="w-100 p-1 fs-14">Quality Check</span>
            </div>
            <div class="progress mb-3 mt-3 mx-3">
                <div class="progress-bar" role="progressbar" style="width: 66.67%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="30">
                    20/30
                </div>
            </div>

            <div class="months_row_second mb-3 mt-3 mx-3">
                <span class="w-100 p-1 fs-14">Painting</span>
            </div>
            <div class="progress mb-3 mt-3 mx-3">
                <div class="progress-bar" role="progressbar" style="width: 36.36%;" aria-valuenow="8" aria-valuemin="0" aria-valuemax="22">
                    8/22
                </div>
            </div>

            <div class="months_row_second mb-3 mt-3 mx-3">
                <span class="w-100 p-1 fs-14">Final Inspection</span>
            </div>
            <div class="progress mb-3 mt-3 mx-3">
                <div class="progress-bar" role="progressbar" style="width: 45.83%;" aria-valuenow="11" aria-valuemin="0" aria-valuemax="24">
                    11/24
                </div>
            </div>
        </div>
    </div>

    <div class="row mx-4 my-3 px-0 mt-5">
        <div class="col-xl-12 col-sm-12 mb-3">
            <div class="card shadow-sm p-4">
                <h4 class="fw-bold mb-3 text-left">Monthly Inspection</h4>
                <div class="chart-container px-3 pt-3" id="monthly_inspection"></div>
            </div>
        </div>
    </div>
    <div class="row mx-4 my-3 px-0">
        <div class="col-xl-12 col-sm-12 mb-3">
            <div class="card shadow-sm p-4">
                <h4 class="fw-bold mb-3 text-left">Monthly Inspection</h4>
                <div class="chart-container px-3 pt-3" id="monthly_inspection1"></div>
            </div>
        </div>
    </div>
    <div class="row mx-4 my-3 px-0">
        <div class="col-xl-12 col-sm-12 mb-3">
            <div class="card shadow-sm p-4">
                <h4 class="fw-bold mb-3 text-left">Monthly Inspection</h4>
                <div class="chart-container px-3 pt-3" id="monthly_inspection2"></div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')

<!-- Highcharts -->
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {

    window.pieChart = Highcharts.chart('pie_chart', {
        chart: {
            type: 'pie',
            backgroundColor: '#eef6f7'
        },
        title: { text: 'Non-Conformities Chart' },
        legend: {
            enabled: true,
            layout: 'horizontal',
            align: 'center',
            verticalAlign: 'bottom',
            itemStyle: {
                fontSize: '14px',
                fontWeight: 'normal'
            },
            useHTML: true,
            labelFormatter: function () {
                return `<span style="color: ${this.color}; font-weight: bold;font-size:18px">●</span> ${this.name}`;
            }
        },
        plotOptions: {
            pie: {
                startAngle: 90,
                showInLegend: true,
                allowPointSelect: true,
                cursor: 'pointer'
            }
        },
        series: [{
            name: 'Non-Conformities',
            data: {!! json_encode(array_map(function ($key, $value) {
                return [$key, $value];
            }, array_keys($categories), array_values($categories))) !!}
        }]
    });

    // Fix for hidden chart (inside d-none)
    setTimeout(() => {
        if (window.pieChart) window.pieChart.reflow();
    }, 500);

});
</script>

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

<script>
// ---------- Chart 1 ----------
window.monthlyInspectionChart = echarts.init(document.getElementById('monthly_inspection'));

var option1 = {
    title: { text: 'Monthly Inspection' },
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    legend: { data: ['On-Going', 'Rejected', 'Completed'] },
    xAxis: { data: ["P1", "P2", "P3", "P3", "P4", "P5"] },
    yAxis: {},
    series: [
        { name: 'On-Going', type: 'bar', data: [100, 20, 36, 10, 10, 20] },
        { name: 'Rejected', type: 'bar', data: [60, 12, 21, 6, 6, 12] },
        { name: 'Completed', type: 'line', data: [40, 8, 15, 4, 4, 8] }
    ]
};
monthlyInspectionChart.setOption(option1);

// ---------- Chart 2 ----------
window.monthlyInspectionChart1 = echarts.init(document.getElementById('monthly_inspection1'));

var option2 = {
    title: { text: 'Inprocess & Final Inspection' },
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    legend: { data: ['Sales', 'Cost', 'Net Sale'] },
    xAxis: { data: ["P1", "P2", "P3", "P3", "P4", "P5"] },
    yAxis: {},
    series: [
        { name: 'Sales', type: 'bar', data: [100, 20, 36, 10, 10, 20] },
        { name: 'Cost', type: 'bar', data: [60, 12, 21, 6, 6, 12] },
        { name: 'Net Sale', type: 'line', data: [40, 8, 15, 4, 4, 8] }
    ]
};
monthlyInspectionChart1.setOption(option2);

// ---------- Chart 3 ----------
window.monthlyInspectionChart2 = echarts.init(document.getElementById('monthly_inspection2'));

var option3 = {
    title: { text: 'Customer Complaint' },
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    legend: { data: ['Sales', 'Cost', 'Net Sale'] },
    xAxis: { data: ["P1", "P2", "P3", "P3", "P4", "P5"] },
    yAxis: {},
    series: [
        { name: 'Sales', type: 'bar', data: [100, 20, 36, 10, 10, 20] },
        { name: 'Cost', type: 'bar', data: [60, 12, 21, 6, 6, 12] },
        { name: 'Net Sale', type: 'line', data: [40, 8, 15, 4, 4, 8] }
    ]
};
monthlyInspectionChart2.setOption(option3);

// ---------- Global Resize Handler ----------
window.addEventListener("resize", function () {
    monthlyInspectionChart.resize();
    monthlyInspectionChart1.resize();
    monthlyInspectionChart2.resize();
    if (window.pieChart) window.pieChart.reflow();
});
</script>

@endsection
