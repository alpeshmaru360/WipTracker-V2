<link href="{{ asset('css/kpi_reports.css') }}" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main_container d-flex">
    @include('layouts.kpireports')
    <div class="main_section bg-white flex-grow-1">
        <div class="container kpi_report">
            <h1>Manpower Efficiency(TA/TP) [%]</h1>
            <canvas id="manpowerChart"></canvas>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('manpowerChart').getContext('2d');
    var manpowerChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['YTD Prev Year', 'YTD Current Year', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'YTD Prev Year',
                    data: [93, null, null, null, null, null, null, null, null, null, null, null, null, null], 
                    backgroundColor: 'grey'
                },
                {
                    label: 'YTD Current Year',
                    data: [null, 94, null, null, null, null, null, null, null, null, null, null, null, null], 
                    backgroundColor: 'black'
                },
                {
                    label: 'Month Previous Year',
                    data: [null, null, 94, 93, 94, 94, 94, 94, 94, 94, 94, 94, 90, 91], 
                    backgroundColor: 'orange'
                },
                {
                    label: 'Month Current Year',
                    data: [null, null, 94, 93, null, null, null, null, null, null, null, null, null, null], 
                    backgroundColor: 'teal'
                },
                {
                    label: 'Target',
                    data: [90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90], 
                    type: 'line',
                    borderColor: 'red',
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    suggestedMin: 88,
                    suggestedMax: 95,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
