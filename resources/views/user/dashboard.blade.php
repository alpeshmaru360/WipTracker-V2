@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/user.css') }}" />
<div class="user_dashboard_page main_section bg-white m-4">
    <div class="container-fluid mt-3 px-5">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h3 class="text-bold text-uppercase mb-0 mt-4">{{$page_title}}</h3>

            <div class="btn-group mt-4">
                <select class="project_priority_dd p-3" name="priority_status">
                    <option value="2" disabled selected>Select Priority Status</option>
                    <option value="1" class="priority">High Priority</option>
                    <option value="0" class="non-priority">Normal Priority</option>
                </select>

                <select class="project_status_dd p-3 ml-2" name="status">
                    <option class="All" value="3">Project Status</option>
                    <option class="Open" value="0">Open</option>
                    <option class="work_in_process" value="1">In Progress</option>
                    <option class="completed" value="2">Completed</option>
                </select>
            </div>
        </div>     
            
        <div class="table-responsive mt-3"> 
            <table class="table table-hover table-bordered w-100 text-center" id="order_table">
                <thead>
                  <tr>
                    <th  scope="col" class="project_table_heading p-1">Status</th>
                    <th  scope="col" class="project_table_heading">Order Date</th>    
                    <th  scope="col" class="project_table_heading p-1">Customer Ref.</th>
                    <th  scope="col" class="project_table_heading">Project No.</th>
                    <th  scope="col" class="project_table_heading">Project Name</th>
                    <th  scope="col" class="project_table_heading">Country</th>
                    <th  scope="col" class="project_table_heading">Customer name</th>
                    <th  scope="col" class="project_table_heading p-1">Priority</th>
                    <th  scope="col" class="project_table_heading p-1">Estimated Readiness</th>
                    <th  scope="col" class="project_table_heading">Confirmation Delivery Date</th>
                  </tr>
                </thead>
                <tbody>

                    @foreach($project as $val)
                    <tr>
                        <td>
                        @php
                            switch($val->status) {
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

                            <div class="mt-2">
                                <span class="project_status_label p-2" style="background: {{ $bg_color }}; color: {{ $color }};">{{ $status }}</span>
                            </div>
                        </td>
                        <td>{{ $val->created_at->format('d-m-y') }}</td>
                        <td>{{$val->customer_ref}}</td>
                        <td>{{$val->project_no}}</td>
                        <td>{{$val->project_name}}</td>
                        <td>{{$val->country}}</td>
                        <td>{{$val->customer_name}}</td>
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
                            @if ($val->is_priotize == 1)
                                <span class="high_priority_color" title="High Priority">High Priority</span>
                            @else
                                <span class="normal_priority_color" title="Normal Priority">Normal Priority</span>
                            @endif
                        </td>

                        <td>{{$val->estimated_readiness}}</td>
                        <td>{{$val->actual_readiness}}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>  
</div>
@endsection
@section('scripts') 
<script>
    $(document).ready(function() {
        const table = $('#order_table').DataTable({
            paging: true,          
            pageLength: 10,     
            lengthMenu: [2, 5, 10, 25, 50, 100], 
            ordering: false
        });

        $('.project_status_dd').on('change', function() {
            const selectedStatus = $(this).val();

            if (selectedStatus == 3) {
                table.columns(0).search('').draw(); 
            } else {
                const statusText = $('.project_status_dd option:selected').text(); 
                table.columns(0).search(statusText).draw(); 
            }
        });

        $('.project_priority_dd').on('change', function() {
            const selectedPriority = $(this).val();

            if (selectedPriority == 2) {
                table.columns(7).search('').draw(); 
            } else {
                const priorityText = selectedPriority == 1 ? "High Priority" : "Normal Priority";
                table.columns(7).search(priorityText).draw();
            }
        });
    });
</script>
@endsection
