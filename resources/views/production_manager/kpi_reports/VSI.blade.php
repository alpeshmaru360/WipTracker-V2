<link href="{{ asset('css/kpi_reports.css') }}" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main_container d-flex">
    @include('layouts.kpireports')
    <div class="main_section bg-white flex-grow-1">
        <div class="container kpi_report">
            <h1>VSI</h1>
            <canvas id="vsichart"></canvas>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('vsichart').getContext('2d');
    var deliveryPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['YTD Prev Year', 'YTD Current Year', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [
                {
                    label: 'YTD Prev Year',
                    data: [90, null, null, null, null, null, null, null, null, null, null, null, null, null], 
                    backgroundColor: 'grey'
                },
                {
                    label: 'YTD Current Year',
                    data: [null, 92, null, null, null, null, null, null, null, null, null, null, null, null], 
                    backgroundColor: 'black'
                },
                {
                    label: 'Month Previous Year',
                    data: [null, null, 95, 96, 97, 98, 99, 100, 98, 97, 96, 95, 94, 96], 
                    backgroundColor: 'orange'
                },
                {
                    label: 'Month Current Year',
                    data: [null, null, 92, 94, null, null, null, null, null, null, null, null, null, null], 
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
                    suggestedMin: 80,
                    suggestedMax: 110,
                    ticks: {
                        stepSize: 10
                    }
                }
            }
        }
    });
</script>
