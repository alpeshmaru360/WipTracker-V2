@extends('layouts.main')
@section('content')

<!-- Bootstrap JS & Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script>
    window.LaravelRoutes = {
        update: "{{ route('admin.users.update', ':id') }}", // keep :id as placeholder
        delete: "{{ route('admin.users.destroy', ':id') }}",
        impersonateLogout: "{{ route('admin.impersonate.logout') }}"
    };
</script>
<script src="{{ asset('js/admin_index.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}" />

<section class="manage_user_page main_section p-5 bg-white m-4"><!-- A Code: 15-12-2025 -->
    <div class="d-flex justify-content-between">
        <h1>
            Manage Users
        </h1>
        <button class="btn btn-primary mb-4 float-right add-button" data-toggle="modal" data-target="#exampleModal">+ Add User</button>
    </div>

    <!-- A Code: 15-12-2025 Start -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <!-- A Code: 15-12-2025 End -->

    <div class="table-responsive">
        <table class="table table-hover table-border table-bordered w-100 text-center" id="user_table">
            <thead class="manager_table">
                <tr>
                    <th>Sr No</th>
                    <th>Profile Picture</th><!-- A Code: 15-12-2025 Code -->
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $sr_no = 0; @endphp
                @foreach($users as $user)
                @php $sr_no++; @endphp
                <tr>
                    <td>{{ $sr_no }}</td>
                    <!-- A Code: 15-12-2025 Start -->
                    <td>
                        <div class="profile-pic-container">
                            @if(file_exists(public_path('production_team/all_user_profile_pic/' . $user->profile_pic)) && !empty($user->profile_pic))
                            <img src="{{ asset('production_team/all_user_profile_pic/' . $user->profile_pic) }}" alt="Profile Pic" class="profile-pic">
                            @else
                            <img id="profilePicPreview" src="{{ asset('images/default_user.jpg') }}" alt="Profile Picture" class="profile-pic mb-2" height="100" width="100">
                            @endif
                        </div>
                    </td>
                    <!-- A Code: 15-12-2025 End -->
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td class="action">      
                        <!-- A Code: 22-12-2025 Start -->  
                        @php
                            $isRedirect = DB::table('roles')->where('rolename', $user->role)->value('is_redirect');
                        @endphp
                        @if($isRedirect == 1)
                        <button class="btn btn-info btn-sm redirect-buttton" onclick="redirectToRole('{{ $user->role }}','{{ Auth::user()->email }}')" >
                            Redirect
                        </button>
                        @endif                       

                        <!-- A Code: 15-12-2025 Start -->
                        <button class="btn btn-primary btn-sm edit-buttton"
                            onclick="openEditUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->profile_pic }}', '{{ $isRedirect }}')">
                            Edit
                        </button>
                        <!-- A Code: 15-12-2025 End -->

                        <!-- A Code: 22-12-2025 End -->

                        <button class="btn btn-danger btn-sm delete-button delete-button" onclick="openDeleteModal({{ $user->id }})">Delete</button>
                        <!-- <a href="{{ route('admin.impersonate', $user->id) }}" target="_blank" class="btn btn-primary">
                            Redirect to Dashboard
                        </a> -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade custom-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <!-- A Code: 15-12-2025 Start -->
                    <form action="{{ route('admin.users.store') }}" method="POST" id="addUserForm" enctype="multipart/form-data">
                    <!-- A Code: 15-12-2025 End -->

                        @csrf
                        <!-- A Code: 15-12-2025 Start -->
                        <div class="row">
                            <div class="col-12 mb-3 text-center">
                                <label for="profilePic" class="form-label">Profile Picture</label>
                                <div class="profile-pic-container">
                                    <img id="addprofilePicPreview"
                                        src="{{ asset('images/default_user.jpg') }}"
                                        alt="Profile Picture"
                                        class="profile-pic mb-2"
                                        height="100"
                                        width="100"
                                        onclick="document.getElementById('profilePicInput').click();">
                                </div>
                                <input type="file"
                                    class="form-control mt-2"
                                    id="profilePicInput"
                                    name="profile_pic"
                                    style="display: none;"
                                    accept="image/jpeg, image/png, image/webp, image/jpg"
                                    onchange="previewImage(event, 'addprofilePicPreview')"> 
                                <div id="errorMessage" class="error-message">
                                    Please upload only JPG, PNG, WEBP, or JPEG files. Your current profile picture will not be lost upon saving.
                                </div>
                            </div>
                        </div>
                        <!-- A Code: 15-12-2025 End -->

                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <div class="input_field">
                                    <label for="name" class="form-label">Name</label>
                                    <span class="text-danger">*</span>
                                    <input type="text" name="name" class="form-control" id="name" required>
                                </div>

                                <div class="input_field">
                                    <label for="email" class="form-label">Email</label>
                                    <span class="text-danger">*</span>
                                    <input type="email" name="email" class="form-control" id="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between">

                                <!-- A Code: 15-12-2025 Start -->
                                <div class="input_field position-relative" id="passwordWrapper">
                                    <label for="password" class="form-label">
                                        Password <span class="text-danger">*</span>
                                    </label>

                                    <input type="password" name="password" class="form-control pe-5" id="password" required>

                                    <span class="toggle_eye_icon position-absolute top-50 end-0 translate-middle-y me-3" 
                                        onclick="togglePassword('password','togglePasswordIcon')">
                                        <i class="fa fa-eye-slash" id="togglePasswordIcon"></i>
                                    </span>
                                </div>

                                <div class="input_field">
                                    <label for="role" class="form-label">Role</label>
                                    <span class="text-danger">*</span>
                                    <select name="role" class="form-select px-4" id="role" required>
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                        <option value="{{ $role->rolename }}" data-is_redirect="{{ $role->is_redirect }}">{{ $role->rolename }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- A Code: 15-12-2025 End -->
                                
                            </div>
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white edit-model-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>

                </div>
                <div class="modal-body">
                    <!-- A Code: 15-12-2025 Start -->
                    <form id="editUserForm" method="POST" action="{{ route('admin.users.update', 'user_id') }}" enctype="multipart/form-data">
                    <!-- A Code: 15-12-2025 End -->

                        @csrf
                        @method('PUT')

                        <!-- A Code: 15-12-2025 Start -->
                        <div class="row">
                            <div class="col-12 mb-3 text-center">
                                <label for="editProfilePic" class="form-label">Profile Picture</label>
                                <div class="profile-pic-container">
                                    <img id="editProfilePicPreview"
                                         src="{{ asset('images/default_user.jpg') }}"
                                         alt="Profile Picture"
                                         class="profile-pic mb-2"
                                         onclick="document.getElementById('editProfilePicInput').click();">
                                </div>
                                <input type="file" class="form-control mt-2" id="editProfilePicInput" name="profile_pic" style="display: none;" 
                                accept="image/jpeg, image/png,image/jpg, image/webp" onchange="previewImage(event, 'editProfilePicPreview')">
                            </div>
                        </div>
                        <!-- A Code: 15-12-2025 End -->

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="edit_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" id="edit_email" required>
                            </div>
                        </div>
                        <div class="row">

                            <!-- A Code: 15-12-2025 Start --> 
                            <div class="col-md-6 mb-3 edit_password_section" id="editPasswordWrapper">    
                                <div class="input_field position-relative">
                                    <label for="edit_password" class="form-label">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password" class="form-control pe-5 edit_pass"
                                        id="edit_password" placeholder="********" readonly>   
                                    <span class="toggle_eye_icon position-absolute top-50 end-0 translate-middle-y me-3"
                                        onclick="togglePassword('edit_password','edit_togglePasswordIcon')">
                                        <i class="fa fa-eye-slash" id="edit_togglePasswordIcon"></i>
                                    </span>
                                </div>

                                <!-- Change password trigger -->
                                <input type="hidden" name="change_password" id="change_password" value="0">
                                <small class="text-primary cursor-pointer mt-1 d-inline-block"
                                    onclick="enableEditPassword()">
                                    Change Password
                                </small>
                            </div>                            

                            <div class="col-md-6 mb-3">
                                <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select px-4" id="edit_role"required>
                                    <option value="" selected disabled>Select Role</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->rolename }}" data-is_redirect="{{ $role->is_redirect }}" >{{ $role->rolename }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- A Code: 15-12-2025 End -->
                        </div>
                        <div class="modal-footer">
                            <!-- A Code: 15-12-2025 Start --> 
                            <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal" onclick="disableEditPassword()">Cancel</button>
                            <!-- A Code: 15-12-2025 End -->
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title delete-modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>

                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Cancel</button>
                    <form id="deleteUserForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</section>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<script>
    // A Code: 15-12-2025 Start
    function previewImage(event, targetId = 'profilePicPreview') {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById(targetId).src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }  
    
    function togglePassword(inputId, iconId) {
        const password = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (!password || !icon) return;

        if (password.hasAttribute('readonly')) return; // prevent toggle if readonly

        if (password.type === "password") {
            password.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            password.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }

    function enableEditPassword() {
        const password = document.getElementById('edit_password');
        const flag = document.getElementById('change_password');

        password.removeAttribute('readonly');
        password.removeAttribute('placeholder');
        password.value = '';       // ensure not blank by mistake
        password.focus();

        flag.value = 1;            // tells backend password is required
    }

    function disableEditPassword() {
        const password = document.getElementById('edit_password');
        const flag = document.getElementById('change_password');

        password.setAttribute('readonly', true);
        password.setAttribute('type', 'password');
        password.value = '';
        password.setAttribute('placeholder', '********');

        // reset eye icon if used
        const icon = document.getElementById('edit_togglePasswordIcon');
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }

        flag.value = 0; // backend: password not required
    }

    function handleRolePasswordToggle(roleSelectId, passwordWrapperId, passwordInputId, changePasswordId = null) {
        const roleSelect = document.getElementById(roleSelectId);
        const passwordWrapper = document.getElementById(passwordWrapperId);
        const passwordInput = document.getElementById(passwordInputId);
        const changePasswordInput = changePasswordId ? document.getElementById(changePasswordId) : null;

        roleSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const isRedirect = selectedOption.getAttribute('data-is_redirect');

            if (isRedirect == 1) {
                passwordWrapper.style.display = 'block';

                if (passwordInput) {
                    passwordInput.required = true;
                    if(roleSelectId == 'edit_role'){
                        enableEditPassword();
                    }                    
                }
            } else {
                passwordWrapper.style.display = 'none';

                if (passwordInput && roleSelectId == 'edit_role') {
                    passwordInput.value = '';
                    passwordInput.required = false;
                    passwordInput.setAttribute('readonly', true);
                }

                if (changePasswordInput) {
                    changePasswordInput.value = 0;
                }
            }
        });

        // Hide initially
        passwordWrapper.style.display = 'none';
    }

    // EDIT FORM
    handleRolePasswordToggle(
        'edit_role',
        'editPasswordWrapper',
        'edit_password',
        'change_password'
    );

    // ADD FORM
    handleRolePasswordToggle(
        'role',
        'passwordWrapper',
        'password'
    );

    // A Code: 15-12-2025 End

    // A Code: 22-12-2025 Start
    function redirectToRole(RoleName, Email) {
        if (!confirm(`Redirect to this ${RoleName} Role dashboard?`)) return;

        $.ajax({
            url: "{{ route('AuthRedirectLogin') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                role_name: RoleName,
                email: Email
            },
            success: function (response) {
                if (response.url) {
                    alert(`You are now ${response.user_role}!`);
                    window.open(response.url, '_blank'); // NEW TAB
                }
            },
            error: function (err) {
                console.error(err);
                alert('Something went wrong!');
            }
        });
    }
    // A Code: 22-12-2025 End
</script>

@endsection