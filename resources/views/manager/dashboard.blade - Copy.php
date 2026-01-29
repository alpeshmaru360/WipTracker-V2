@extends('layouts.main')
@section('content')
<link href="{{ asset('css/manager.css') }}" rel="stylesheet" />
<div class="container">
	<table class="mx-auto">
	   <tbody>
	        <tr><td class="pl-2  text-bold fs-16">Packing</td>
	        	@foreach($project_status as $val)
	        	{{--<td class="w-10 h-50px {{$val->packing == '0' ? 'bg-white' : 'bg-green'}}">--}}
	        	<td class="w-10 h-50px bg-white">
	        		<span class="{{$val->packing == '0' ? '' : 'badge badge-primary project_status'}}">
	        			{{$val->packing == '0' ? '' : 'Done'}} &nbsp;
	        			<span class="{{$val->packing == '0' ? '' : 'project_link_span'}}">{{$val->packing == '0' ? '' : '>'}}</span>
	        		</span>

	        	</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Final Inspection</td>
	        	@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->final_inspection == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->final_inspection == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->final_inspection == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->final_inspection == '0' ? '' : 'project_link_span'}}">{{$val->final_inspection == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Serial No. Production</td>
	        	@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->serial_no_production == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->serial_no_production == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->serial_no_production == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->serial_no_production == '0' ? '' : 'project_link_span'}}">{{$val->serial_no_production == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Assembly</td>
	       		@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->assembly == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->assembly == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->assembly == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->assembly == '0' ? '' : 'project_link_span'}}">{{$val->assembly == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Initial Inspection</td>
	        	@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->initial_inspection == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->initial_inspection == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->initial_inspection == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->initial_inspection == '0' ? '' : 'project_link_span'}}">{{$val->initial_inspection == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Material Requisition</td>
	        	@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->material_requisition == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->material_requisition == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->material_requisition == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->material_requisition == '0' ? '' : 'project_link_span'}}">{{$val->material_requisition == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Items Received</td>
	        	@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->items_received == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->items_received == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->items_received == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->items_received == '0' ? '' : 'project_link_span'}}">{{$val->items_received == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Purchase Process</td>
	        	@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->purchase_process == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->purchase_process == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->purchase_process == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->purchase_process == '0' ? '' : 'project_link_span'}}">{{$val->purchase_process == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16">Project Creation</td>
	        	@foreach($project_status as $val)
	        		{{--<td class="w-10 h-50px {{$val->project_creation == '0' ? 'bg-white' : 'bg-green'}}"></td>--}}
	        		<td class="w-10 h-50px">
	        			<span class="{{$val->project_creation == '0' ? '' : 'badge badge-primary project_status'}}">{{$val->project_creation == '0' ? '' : 'Done'}}&nbsp;
	        			<span class="{{$val->project_creation == '0' ? '' : 'project_link_span'}}">{{$val->project_creation == '0' ? '' : '>'}}</span></span>
	        		</td>
	        	@endforeach
	        </tr>
	        <tr><td class="pl-2 text-bold fs-16"></td>
	        	@foreach($project_status as $val)
	        		<td class="text-bold fs-16">{{$val->name}}</td>
	        	@endforeach
	        </tr>
	    </tbody>
	</table>
</div>
@endsection
