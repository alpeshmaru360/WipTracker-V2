@if(count($trackingOperators) > 0)
    @php $sr_no = 1; @endphp
    @foreach($trackingOperators as $val)  
        <tr>
            <td>  
                {{--
                    $minStartDate = $val->projectProcessStdTimes
                    ->pluck('operators_time_tracking')
                    ->filter()
                    ->map(fn($json) => json_decode($json, true))
                    ->filter(fn($arr) => is_array($arr))
                    ->flatten(1)
                    ->pluck('started_at')
                    ->filter()
                    ->min();
                --}}
                
                @php
                $minStartDate = optional($val->projectProcessStdTimes->first())->timer_started_at;
                @endphp
            {{ $minStartDate ? \Carbon\Carbon::parse($minStartDate)->format('d-m-Y') : '-' }}
            </td>
            <td>{{ $val->projects['project_no'] }}</td>
            <td>{{ $val->projects['project_name'] }}</td>
            <td>{{ $val->full_article_number }}</td>
            <td>{{ $val->description }}</td>
            <td>{{ $val->product_type }}</td>
            <td>{{ $val->qty }}</td>

            @php
                $completed_qty = $val->projectProcessStdTimes
                    ->groupBy('order_qty')
                    ->filter(function ($group) {
                        // Check strictly — all must be exactly 1
                        return $group->every(fn($item) => (int)$item->project_status === 1);
                    })
                    ->count();

                $operatorIds = $val->projectProcessStdTimes->pluck('operators_time_tracking')
                    ->filter()
                    ->map(fn($json) => json_decode($json, true))
                    ->filter(fn($arr) => is_array($arr))
                    ->flatten(1)
                    ->pluck('id')
                    ->unique();

                $grouped = $val->projectProcessStdTimes->groupBy('order_qty');
                    foreach ($grouped as $qty => $group) {
                    }
            @endphp
            
            <td>{{ $completed_qty }}</td>
            
            <td>
                {{-- Total Operators --}}
                {{ $operatorIds->count() }}
            </td>

            <td>
                {{-- Wilo Operators --}}
                {{ \App\Models\User::whereIn('id', $operatorIds)->where('role', 'Wilo Operator')->count() }}
            </td>

            <td>
                {{-- 3rd Party Operators --}}
                {{ \App\Models\User::whereIn('id', $operatorIds)->where('role', '3rd Party Operator')->count() }}
            </td>

            @php
                $operatorTimes = $val->projectProcessStdTimes->pluck('operators_time_tracking')
                    ->filter()
                    ->map(fn($json) => json_decode($json, true))
                    ->filter(fn($arr) => is_array($arr))
                    ->flatten(1);
                $totalHours = $operatorTimes->sum('total_time') / 60;

                $wiloHours = $operatorTimes->filter(function($op){
                    return \App\Models\User::where('id', $op['id'])->where('role', 'Wilo Operator')->exists();
                })->sum('total_time') / 60;

                $thirdPartyHours = $operatorTimes->filter(function($op){
                    return \App\Models\User::where('id', $op['id'])->where('role', '3rd Party Operator')->exists();
                })->sum('total_time') / 60;
            @endphp

        <td>{{ number_format($totalHours, 2) }} HRS</td>
        <td>{{ number_format($wiloHours, 2) }} HRS</td>
        <td>{{ number_format($thirdPartyHours, 2) }} HRS</td>
        <td>{{ $completed_qty > 0 ? number_format($totalHours / $completed_qty, 2) : 0 }} HRS/Unit</td>
        </tr>
        @php $sr_no++ @endphp
    @endforeach

@else
    <tr>
        <td colspan="15" class="blank_record_message">
            No Pending Operator Tracking Records found.
        </td>
    </tr>
@endif