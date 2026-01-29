@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/assembly_manager.css') }}" />
<div class="main_section bg-white m-4">
  @include('manager.dashboard')

    <h3 class="ml-5 pt-4 text-bold text-left text-uppercase">{{$page_title}}</h3>
    <hr class="mx-5" />
    <div class="mx-4">        
        <div class="chart-container mx-auto">
            <canvas id="bar-chart" width="300" height="300" class="mx-auto"></canvas>
        </div>
        <div class="row mx-2 mt-5">
            <div class="w-18 mx-auto mb-4">
                <div class="assembly_dashboard">
                    <div class="">
                        <div class="text-center">
                            <span class="fw-600  fs-30">{{$projects_complate_count}}</span>
                        </div>
                        <div class="text-center">
                            <span class="text--small fw-600 fs-17">Project Completed</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-18 mx-auto mb-4">
                <div class="assembly_dashboard">
                    
                    <div class="">
                        <div class="text-center">
                            <span class="fw-600  fs-30">{{$projects_working_count}}</span>
                        </div>
                        <div class="text-center">
                            <span class="text--small fw-600 fs-17">Projects On Hand</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-18 mx-auto mb-4">
                <div class="assembly_dashboard">
                    
                    <div class="">
                        <div class="text-center">
                            <span class="fw-600  fs-30">€ 2.35 M</span>
                        </div>
                        <div class="text-center">
                            <span class="text--small fw-600 fs-17">Production Value</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-18 mx-auto mb-4">
                <div class="assembly_dashboard">
                    
                    <div class="">
                        <div class="text-center">
                            <span class="fw-600  fs-30">€ 1.05 M</span>
                        </div>
                        <div class="text-center">
                            <span class="text--small fw-600 fs-17">Invoiced in 2024</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-18 mx-auto mb-4">
                <div class="assembly_dashboard">
                    
                    <div class="">
                        <div class="text-center">
                            <span class="fw-600  fs-30">€ 0.98 M</span>
                        </div>
                        <div class="text-center">
                            <span class="text--small fw-600 fs-17">Backlog Orders</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts') 
<script type="text/javascript">
  var chartData = <?php echo $chartJson; ?>;
  
  var numberWithCommas = function(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  };

  var bar_ctx = document.getElementById('bar-chart');
  var bar_chart = new Chart(bar_ctx, {
    type: 'bar',
    data: {
      labels: chartData.label,
      datasets: chartData.datasets
    },
    options: {
      scales: {
        xAxes: [{
          stacked: true,
          gridLines: { display: false },
          scaleLabel: {
            fontSize: 35
          },
          ticks: {
            fontSize: 35
          }
        }],
        yAxes: [{
          stacked: true,
          scaleLabel: {
            fontSize: 35
          },
          ticks: {
            fontSize: 35,
            callback: function(value) { return numberWithCommas(value); }
          }
        }]
      },
      legend: {
        display: true,
        labels: {
          fontSize: 35
        }
      }
    }
  });
</script>
@endsection