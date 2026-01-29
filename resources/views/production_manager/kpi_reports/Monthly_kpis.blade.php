<link href="{{ asset('css/Monthly_kpis.css') }}" rel="stylesheet" />
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main_container d-flex">
    @include('layouts.kpireports')

    <div class="main_section bg-white flex-grow-1">
        <div class="container kpi_report">
            <h1>MONTHLY KPIs TRACKING</h1>

            @php
                use Carbon\Carbon;
                $currentYear = Carbon::now()->year;
            @endphp

            <div class="mb-3 custom_container">
                <select id="yearSelect" name="year" class="form-select custom-dropdown w-auto">
                    <option value="" selected disabled>Choose YEAR</option> 
                    @for ($i = $currentYear; $i >= $currentYear - 10; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>


            <div class="table-responsive mt-3 pr-1">
                <table class="table table-bordered text-center">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th rowspan="2">Month</th>
                            <th colspan="2">Manpower Efficiency (%)</th>
                            <th colspan="2">Throughput Time</th>
                            <th colspan="2">Delivery on Time (%)</th>
                            <th colspan="2">Finished Good per Employee hr (pc/hr)</th>
                            <th colspan="2">Coverage Rate</th>
                            <th colspan="2">VSI</th>
                            <th colspan="2">Invoiced Value (Euro)</th>
                        </tr>
                        <tr>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Target</th>
                            <th>Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'] as $month)
                            <tr>
                                <td>{{ $month }}</td>
                                @for ($j = 0; $j < 14; $j++)
                                    <td></td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
