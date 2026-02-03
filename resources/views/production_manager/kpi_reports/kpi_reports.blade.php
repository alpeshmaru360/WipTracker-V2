
<link href="{{ asset('css/kpi_reports.css') }}" rel="stylesheet" />
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main_container d-flex">
    @include('layouts.kpireports')
    <div class="main_section bg-white flex-grow-1">
        
        <div class="container kpi_report"> 
        <h1>Allocated Month  </h1>
            <div class="justify-content-end row">
                <div class="btn-group dropdown-width ">
                    
                    <select class="form-control" id="completed-projects">
                        <option value="" disabled selected>Select Completed Project</option>
                        @foreach($completedProjects as $project)
                            <option value="{{ $project->id }}" data-name="{{ $project->project_name }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>

                    <button class="btn btn-primary ml-2" id="allocated-month-btn">
                        <i class="fas fa-calendar-alt mr-2"></i> Allocate Month
                    </button>
                </div>
            </div>

            <div class="row kpi_reports mt-3">
                <div style="overflow-x: auto;">
                    <table class="table table-hover table-border w-100 text-center" id="project_table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Project no</th>
                                <th>Project Name</th>
                                <th>Country</th>
                                <th>Customer</th>
                                <th>Sales</th>
                                <th>Project Value</th>
                                <th>Allocated Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                                @if($project->allocated_month_kpi) 
                                    <tr data-id="{{ $project->id }}">
                                        <td>{{ $project->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $project->project_no }}</td>
                                        <td>{{ $project->project_name }}</td>
                                        <td>{{ $project->country }}</td>
                                        <td>{{ $project->customer_name }}</td>
                                        <td>{{ $project->sales_name }}</td>
                                        <td>{{ $project->project_value }}</td>
                                        <td class="allocated-month">
                                            {{ $project->allocated_month_kpi }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function () {
    let lastAllocatedMonth = null; // Store the last allocated month

    $('#allocated-month-btn').on('click', function () {
        let selectedProjectId = $('#completed-projects').val();
        let selectedProjectName = $('#completed-projects option:selected').data('name');

        if (!selectedProjectId) {
            Swal.fire('Please select a completed project first.');
            return;
        }

        let allocatedMonth = new Date().toLocaleString('default', { month: 'long', year: 'numeric' });

        let confirmMessage = `You are about to allocate the project name: ${selectedProjectName} to ${allocatedMonth}`;

        if (lastAllocatedMonth && lastAllocatedMonth !== allocatedMonth) {
            confirmMessage += `\n\n⚠️ The allocated month has changed from ${lastAllocatedMonth} to ${allocatedMonth}.`;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: confirmMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#169e88',
            cancelButtonColor: '#000000',
            confirmButtonText: 'Yes, allocate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                allocateProject(selectedProjectId, allocatedMonth);
                lastAllocatedMonth = allocatedMonth; 
            }
        });
    });

    function allocateProject(projectId, allocatedMonth) {
        $.ajax({
            url: '{{ route("kpi-reports.allocate") }}',  
            method: 'POST',
            data: {
                project_id: projectId,
                allocated_month: allocatedMonth,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    updateProjectTable(response.updatedProject);
                    $('#completed-projects option').each(function () {
                        if ($(this).val() == projectId) {
                            $(this).remove();
                        }
                    });
                    Swal.fire('Project allocated successfully!', '', 'success');
                } else {
                    Swal.fire('Failed to allocate the project. Please try again.', '', 'error');
                }
            },
            error: function () {
                Swal.fire('An error occurred. Please try again.', '', 'error');
            }
        });
    }

    function updateProjectTable(project) {
        let existingRow = $('#project_table tbody tr[data-id="' + project.id + '"]');

        if (existingRow.length) {
            existingRow.find('.allocated-month').text(project.allocated_month_kpi);
        } else {
            let newRow = `
                <tr data-id="${project.id}">
                    <td>${project.created_at}</td>
                    <td>${project.project_no}</td>
                    <td>${project.project_name}</td>
                    <td>${project.country}</td>
                    <td>${project.customer_name}</td>
                    <td>${project.sales_name}</td>
                    <td></td>
                    <td class="allocated-month">${project.allocated_month_kpi}</td>
                </tr>
            `;
            $('#project_table tbody').prepend(newRow);  
        }
    }
});

</script>


