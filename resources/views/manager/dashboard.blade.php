<link rel="stylesheet" href="{{ asset('css/manager.css') }}" />

{{-- This is new common Code --}}
<div class="project_status_screen container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="p-4">
                <h4 class="text-bold text-left text-uppercase mt-2 mb-4">Project Status</h4>
 
                @php
                    /* ----------------------------------------------------
                     |  Flatten the service payload so the Blade stays clean
                     |  $dashboardData comes from the View::composer in
                     |  AppServiceProvider.  We default to empty collections
                     |  so the template never breaks if a key is missing.
                     ---------------------------------------------------- */
                    $data       = $dashboardData ?? [];
                    $projects   = $data['projects']   ?? collect();
 
                    // Individual status maps keyed by project_id
                    $material_requisition_status = $data['material_requisition_status'] ?? [];
                    $assembly_status             = $data['assembly_status']             ?? [];
                    $final_inspection_status     = $data['final_inspection_status']     ?? [];
                    $packing_status              = $data['packing_status']              ?? [];
                    $project_creation_status     = $data['project_creation_status']     ?? [];
 
                    /*
                     | Map table row labels to the variable names above.
                     | This lets us loop instead of copy‑pasting 9 chunks.
                     */
                    $statusRows = [
                        'Project Completion'   => 'packing_status',
                        'Final Inspection'     => 'final_inspection_status',
                        'Assembly'             => 'assembly_status',
                        'Material Requisition' => 'material_requisition_status',
                        'Project Creation'     => 'project_creation_status',
                    ];
 
                    // Quick helper to get the coloured icon path
                    $icon = fn(string $colour) => [
                        'green'  => asset('images/check.svg'),
                        'yellow' => asset('images/yellow-sign.svg'),
                        'red' => asset('images/red.svg'),
                        'black' => asset('images/black.svg'),
                    ][$colour] ?? null;
                @endphp
 
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <tbody>
                            @foreach($statusRows as $label => $varName)
                                <tr>
                                    <td class="pl-2 text-bold fs-16 align-middle sticky-col">{{ $label }}</td>
 
                                    @foreach($projects as $project)
                                        @php
                                            // e.g. $packing_status[$project->id]  or []
                                            $map    = $$varName;      // variable variable 
                                            $status = $map[$project->id] ?? 'none';
                                            $src    = $icon($status);
                                        @endphp
                                        @if($src)
                                        	@php $imageName = basename($src); @endphp

                                            @if($imageName == 'black.svg')
                                                <td class="w-10 text-center align-middle" title="All BOM items are from stock, so this process is not required">
                                                    N/A
                                                </td>
                                            @else
                                                @if($imageName != 'yellow-sign.svg')
                                                <td class="w-10 text-center align-middle process_box">
                                                    <img class="status-image" src="{{ $src }}" alt="{{ ucfirst($status) }}" title="{{ $label }}" />
                                                </td>
                                                @else
                                                <td class="w-10 text-center align-middle">
                                                    <img class="status-image" src="{{ $src }}" alt="{{ ucfirst($status) }}" title="{{ $label }}" />
                                                </td>
                                                @endif
                                            @endif
                                        @else
                                        	<td class="w-10 text-center align-middle"></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
 
                        <tfoot class="bg-light">
                            <tr>
                                <th class="text-center"></th>
                                @foreach($projects as $project)
                                    <td class="text-center">
                                        {{ $project->project_name ?? '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>