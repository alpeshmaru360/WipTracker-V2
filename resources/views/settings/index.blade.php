@extends('layouts.main')
@section('content')
    <link href="{{ asset('css/settings.css') }}" rel="stylesheet" />
    <div class="main_section bg-white m-4">
        <div class="container settings_container">
            <h1>Settings Table</h1>

            @if(session('success'))
                <p style="color: green;">{{ session('success') }}</p>
            @endif
            <table class="table table-hover table-border table-bordered w-100 text-center settings-table">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Action</th>
                        <th>Email</th>
                        <th>Reminder Frequency</th>                   
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sr_no = 0; @endphp
                    @foreach($settings as $setting)
                        @php $sr_no++; @endphp
                        <tr>
                            <td>{{ $sr_no }}</td>
                            <td>{{ $setting->action }}</td> 
                            <td>{{ $setting->value }}</td> 
                            <td>{{ $setting->reminder_frequency }}</td> 
                            
                            <td>
                                <button type="button" class="btn edit-button" 
                                    data-id="{{ $setting->id }}" 
                                    data-email="{{ $setting->value }}"
                                    data-reminder_frequency="{{ $setting->reminder_frequency }}"> 
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title edit-modal-title" id="editModalLabel">Edit Settings</h5>
                    
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="value">Email</label>
                            <input type="email" class="form-control" id="value" name="value" >
                        </div>
                        <div class="form-group">
                            <label for="reminder_frequency">Reminder Frequency</label>
                            <input type="number" class="form-control" id="reminder_frequency" name="reminder_frequency" >
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary ">Save Changes</button>
                            <button type="button" class="btn btn-secondary canclebutton" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.edit-button').on('click', function() {
            var id = $(this).data('id');
            var label = $(this).data('label');
            var email = $(this).data('email');
            var reminder_frequency = $(this).data('reminder_frequency');  

            $('#editForm').attr('action', '{{ url('settings') }}/' + id);
            $('#value').val(email);
            $('#reminder_frequency').val(reminder_frequency);  

            $('#editModal').modal('show');
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const editForm = document.getElementById("editForm");

        editForm.addEventListener("submit", function (event) {
            event.preventDefault();

            document.querySelectorAll(".error-message").forEach(function (el) {
                el.remove();
            });

            const emailField = document.getElementById("value");
            const frequencyField = document.getElementById("reminder_frequency");

            let isValid = true;

            if (emailField.value.trim() === "") {
                showError(emailField, "Email is required.");
                isValid = false;
            }

            if (frequencyField.value.trim() === "") {
                showError(frequencyField, "Reminder Frequency is required.");
                isValid = false;
            }

            if (isValid) {
                editForm.submit();
            }
        });

        function showError(inputField, message) {
            const errorElement = document.createElement("div");
            errorElement.className = "error-message text-danger mt-1";
            errorElement.innerText = message;
            inputField.parentNode.appendChild(errorElement);
        }
    });
</script>

@endsection