@foreach($project as $val)
<tr>
    <td>
        @php
        switch($val->status) {
            case "0":
            $status = "Open";
            $bg_color = "red";
            $color = "white"; // Missing quotes fixed
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
        <div class="mt-2">
            <span class="project_status_label p-2" style="background: {{ $bg_color }}; color: {{ $color }}; border-radius: 12px;">{{ $status }}</span>
        </div>
    </td>

    <td>{{ \Carbon\Carbon::parse($val->wip_project_create_date)->format('d-m-Y') }}</td>
    <td>{{$val->customer_ref}}</td>
    <td>{{$val->sales_order_number ?: 'N/A'  }}</td>
    <td>{{$val->project_no}}</td>
    <td>{{$val->project_name}}</td>
    <td>{{$val->country}}</td>
    <td>{{$val->customer_name}}</td>
    <td>{{$val->sales_name}}</td>

    @php
        $projectTypesJson = $val->product_type;
        $projectTypesArray = json_decode($projectTypesJson);
        if (is_array($projectTypesArray)) {
            $commaSeparatedprojectTypes = implode(", ", $projectTypesArray);
        } else {
            $commaSeparatedprojectTypes = $projectTypesJson;
        }
    @endphp 

    <td>
         {{ $val->estimated_readiness ? \Carbon\Carbon::parse($val->estimated_readiness)->format('d-m-y') : 'N/A' }}
    </td>

    @php
        $maxDate = DB::table('products_of_projects')
                    ->where('project_id', $val->id)
                    ->max('actual_readiness_date');

        $actual_readiness_date = $maxDate ?? $val->estimated_readiness;
        $display_date = \Carbon\Carbon::parse($actual_readiness_date)->format('d-m-Y');
    @endphp
    <td class="{{ $val->actual_readiness <= $val->estimated_readiness ? 'text-success' : 'text-danger' }}">
         {{ $display_date }}
    </td>

    <td>
        <a href="javascript:void(0);" class="open-documents-modal" data-id="{{ $val->id }}" title="Open Documents">
            <i class="p-2 m-1 fa fa-file project_icon"></i>
        </a>
    </td>

    <td>
        <a class="" href="{{route('CheckProjectStatus',['id'=>$val->id])}}" title="Check Project Status" aria-label="Check status of project {{ $val->project_name }}">
            <i class="p-2 m-1 fa fa-tasks project_view_icon project_icon"></i>
        </a>
    </td>

    <td>
        <a href="{{ route('download.qr', ['projectId' => $val->id, 'projectName' => $val->project_name]) }}">
            <i class="p-2 m-1 fa fa-qrcode other project_icon"></i>
        </a>        

        @if(Auth::user()->role == "Production Engineer")
        <a href="{{ route('ProductionManagerProjectEdit', ['id' => $val->id]) }}">
            <i class="p-2 m-1 fa fa-pen other project_icon"></i>
        </a>
        @endif

        <!-- A Code: 26-12-2025 Start -->
        @if(Auth::user()->role == "Admin" || Auth::user()->is_admin_login)            
            <button
                type="button"
                class="btn p-0 m-0 border-0 bg-transparent"
                onclick="openDeleteProjectReasonModal({{ $val->id }})">
                <i class="p-2 m-1 fa fa-trash other project_icon bg-danger text-white"></i>
            </button>
        @endif
        <!-- A Code: 26-12-2025 End -->
    </td>    
</tr>
@endforeach