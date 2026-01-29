@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />
<div class="production_manager_page main_section bg-white m-4">
    @include('manager.dashboard')

    <div class="container mt-3">
        <div class="row g-4">
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm p-4 text-center mb-3">
                    <div class="icon-box mb-3 text-primary">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                    <h2 class="fw-bold">{{ $project_working_on }}</h2>
                    <p class="text-muted">Projects On Hand</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm p-4 text-center mb-3">
                    <div class="icon-box mb-3 text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h2 class="fw-bold">{{ $project_completed }}</h2>
                    <p class="text-muted">Projects Completed</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="card border-0 shadow-sm p-4 text-center mb-3">
                    <div class="icon-box mb-3 text-warning">
                        <i class="fas fa-euro-sign fa-2x"></i>
                    </div>
                    <h2 class="fw-bold">€ 2.35 M</h2>
                    <p class="text-muted">Production Value</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm p-4">
                    <h4 class="fw-bold mb-4">Previous Month KPI</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">Manpower efficiency (TA/TP) [%]</th>
                                    <th class="text-center">T</th>
                                    <th class="text-center">A</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr>
                                    <td>Throughput Time</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                                <tr>
                                    <td>Delivery on time [%]</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                                <tr>
                                    <td>Finished goods per employee hour [pcs/hr]</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                                <tr>
                                    <td>Coverage Rate</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                                <tr>
                                    <td>VSI</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm p-4">
                    <h4 class="fw-bold mb-3">Monthly Inspection</h4>
                    <div id="monthly_inspection" class="table-responsive pt-3"></div>
                </div>
            </div>
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm p-4">
                    <h4 class="fw-bold mb-3">Monthly Inspection</h4>
                    <div id="monthly_inspection1" class="table-responsive pt-3"></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow-sm p-4">
                    <h4 class="fw-bold mb-3">Monthly Inspection</h4>
                    <div id="monthly_inspection2" class="table-responsive pt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts') 
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
  Highcharts.chart('pie_chart', {
    chart: { type: 'pie', backgroundColor: '#eef6f7' },
    title: { text: '2024 Non-Conformities' },
    plotOptions: { pie: { startAngle: 90 } },
    series: [{
        data: [
            ['Firefox', 44.2],
            ['IE7',     26.6],
            ['IE6',     20],
        ]
    }]
  });
</script>

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