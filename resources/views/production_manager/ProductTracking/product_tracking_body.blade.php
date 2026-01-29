@php $sr_no = 1; @endphp
@foreach($trackingProducts as $val)                       

    @php
    switch(@$val->projects['status']) {
        case "0":
        $status = "Open";
        $bg_color = "red";
        $color = "white";
        break;

        case "1":
        $status = "InProgress";
        $bg_color = "yellow";
        $color = "black";
        break;

        case "2":
        $status = "Completed";
        $bg_color = "green";
        $color = "white";
        break;
        
        default:
        $status = '';
        $bg_color = "green";
        $color = "white";
        break;
    }
    @endphp
    <tr>
        <td>{{ $sr_no }}</td>
        <td>
            <div class="mt-2">
                <span class="project_status_label p-2" 
                style="background: {{ $bg_color }}; color: {{ $color }}; border-radius: 12px;">{{ $status }}</span>
            </div>
        </td>
        <td>
            {{ @$val->projects['pl_uploaded_date'] 
                ? \Carbon\Carbon::parse($val->projects['pl_uploaded_date'])->format('d-m-Y') 
                : '' }}
        </td>
        <td>{{ @$val->projects['sales_order_number'] ?: 'N/A' }}</td>
        <td>{{ @$val->projects['project_no'] }}</td>
        <td>{{ @$val->projects['project_name'] }}</td>

        <td>{{ @$val->projects['country'] }}</td>
        <td>{{ @$val->full_article_number }}</td>
        <td>{{ @$val->description }}</td>
        <td>{{ @$val->product_type }}</td>

        <td>
            {{-- Product Family Number - Client will provide --}}
            {{ \App\Models\ProductType::where('project_type_name', $val->product_type)->value('product_family_number') }}
        </td>

        <td>{{ @$val->qty }}</td>

        <!-- A Code: 19-01-2026 Start -->
        <td>{{ @$val->unit_price }}</td>
        <td>{{ @$val->total_price }}</td>        
        <!-- A Code: 19-01-2026 End -->
         
    </tr>
    @php $sr_no++ @endphp
@endforeach
           