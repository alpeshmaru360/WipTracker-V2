@php $sr_no = 0; @endphp
@foreach($processes as $process)  
    @php $sr_no++; @endphp
    <tr>
        <td>{{ $sr_no }}</td>
        <td>{{ $process->lable }}</td>  
        <td>{{ $process->process_code ?? '-' }}</td>
        <td>{{ $process->product_type ?? '-' }}</td>
        <td>{{ $process->process_name ?? '-' }}</td>
        <td>{{ $process->value }}</td>
        <td class="action text-center" style="width:23%;">
            <button class="btn edit-button" onclick="edit_process(this);" data-toggle="modal" data-target="#editProcessModal"
                data-id="{{ $process->id }}"
                data-process_type="{{ $process->lable }}"
                data-process_code="{{ $process->process_code }}"
                data-product_type="{{ $process->product_type }}"
                data-process_name="{{ $process->process_name }}"
                data-key="{{ $process->key }}"
                data-hrs="{{ $process->value }}">Edit</button>
            <button class="btn btn-danger" onclick="openDeleteModal('{{ $process->id }}', '{{ $process->key }}')">Delete</button>
        </td>
    </tr>
@endforeach

<script>
$(document).ready(function () {
    $('.edit-button').on('click', function () {
        const processId = $(this).data('id');
        const processType = $(this).data('process_type');
        const processCode = $(this).data('process_code');
        const productType = $(this).data('product_type');
        const processName = $(this).data('process_name');
        const processKey = $(this).data('key');
        const processHrs = $(this).data('hrs');

        $('#editProcessType').val(processType);

        if (processType === 'StandardProcessTimes') {
            $('.product_type_field').hide();
            $('#product_code_field').hide();
            $('#editProcessCode').val('');
        } else {
            $('.product_type_field').show();
            $('#product_code_field').show();
            $('#editProductType').val(productType);
            $('#editProcessCode').val(processCode);
        }

        $('#editProcessName').val(processName);
        $('#editKey').val(processKey);
        $('#editHrs').val(processHrs);

        const actionUrl = `{{ route('admin.hrs.update', ':id') }}`.replace(':id', processId);
        $('#editProcessForm').attr('action', actionUrl);
    });
});
</script>