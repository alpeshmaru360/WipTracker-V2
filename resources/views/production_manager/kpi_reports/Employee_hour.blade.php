<link href="{{ asset('css/kpi_reports.css') }}" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="main_container d-flex">
    @include('layouts.kpireports')
    <div class="main_section bg-white flex-grow-1">
        <div class="container kpi_report">
            <h1>Finished good per employee hour [pcs/hr]</h1>
            <canvas id="finishedGoodsChart"></canvas>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('finishedGoodsChart').getContext('2d');
    var finishedGoodsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['YTD Prev Year', 'YTD Current Year', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [
                {
                    label: 'YTD Prev Year',
                    data: [95, null, null, null, null, null, null, null, null, null, null, null, null, null],
                    backgroundColor: 'gray'
                },
                {
                    label: 'YTD Current Year',
                    data: [null, 98, null, null, null, null, null, null, null, null, null, null, null, null],
                    backgroundColor: 'black'
                },
                {
                    label: 'Month Previous Year',
                    data: [null, null, 95, 94, 94, 94, 94, 94, 94, 94, 94, 94, 94, 94],
                    backgroundColor: 'orange'
                },
                {
                    label: 'Month Current Year',
                    data: [null, null, 92, 93, null, null, null, null, null, null, null, null, null, null],
                    backgroundColor: 'teal'
                },
                {
                    label: 'Target',
                    data: [90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90, 90],
                    type: 'line',
                    borderColor: 'red',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 120
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Finished Goods per Employee Hour [pcs/hr]',
                    font: {
                        size: 16
                    }
                }
            }
        }
    });
</script>

