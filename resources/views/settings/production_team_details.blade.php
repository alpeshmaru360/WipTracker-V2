@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/production_team.css') }}" />

<div class="production_team_details_page main_container d-flex">
    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid production_container">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="setting_page_title py-2 text-left">Production Team Details</h1>
                <button class="btn btn-success custom_bg_color add_member" data-toggle="modal" data-target="#addMemberModal">+ Add Member</button>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
            @endif
            @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
            @endif
            
            <div class="table-responsive"><!-- A Code: 15-12-2025 -->
                <table class="table table-bordered text-center production-table" id="team_table"> 
                    <thead class="table-light">
                        <tr>
                            <th>Profile Picture</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Email</th>
                            <th style="width:20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @foreach($members as $member)
                        <tr data-id="{{ $member->id }}">
                            <td>
                                <div class="profile-pic-container">
                                    @if(file_exists(public_path('production_team/profile_pic/' . $member->profile_pic)) && !empty($member->profile_pic))
                                    <img src="{{ asset('production_team/profile_pic/' . $member->profile_pic) }}" alt="Profile Pic" class="profile-pic">
                                    @else
                                    <img id="profilePicPreview" src="{{ asset('images/default_user.jpg') }}" alt="Profile Picture" class="profile-pic mb-2" height="100" width="100">
                                    @endif
                                </div>
                            </td>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->designation }}</td>
                            <td>{{ $member->email }}</td>
                            <td class="action">
                                <button class="btn btn-primary btn-sm edit_button"
                                    data-toggle="modal"
                                    data-target="#editMemberModal"
                                    onclick="fetchMember({{ $member->id }})">
                                    <i class="fas fa-edit"></i> 
                                </button>

                                <button type="button" class="btn btn-danger btn-sm delete_button" onclick="confirmDelete({{ $member->id }})">
                                    <i class="fas fa-trash-alt"></i> 
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--Add Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">Add New Member</h5>
                    <button type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i> 
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('production.team.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <div class="col-12 mb-3 text-center">
                                <label for="profilePic" class="form-label">Profile Picture</label>
                                <div class="profile-pic-container">
                                    <img id="profilePicPreview"
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
                                    onchange="previewImage(event)">
                                <div id="errorMessage" class="error-message">Please upload only JPG, PNG, WEBP, or JPEG files. Your current profile picture will not be lost upon saving.</div>

                            </div>

                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Name</label><span class="text-danger">*</span>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="designation" class="form-label">Designation</label><span class="text-danger">*</span>
                                 <select class="form-select px-4" id="designation" name="designation" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->rolename }}">{{ $role->rolename }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email</label><span class="text-danger">*</span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                            </div>
                        </div>

                        <button type="submit" class="btn w-100 btn_green">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                    <button type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i> 
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editMemberForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST') 
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
                                <input type="file" class="form-control mt-2" id="editProfilePicInput" name="profile_pic" style="display: none;" accept="image/jpeg, image/png,image/jpg, image/webp" onchange="previewImage(event, 'editProfilePicPreview')">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="editName" class="form-label">Name</label><span class="text-danger">*</span>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="editDesignation" class="form-label">Designation</label><span class="text-danger">*</span>
                                <select class="form-select px-4" id="editDesignation" name="designation" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->rolename }}">{{ $role->rolename }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="editEmail" class="form-label">Email</label><span class="text-danger">*</span>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                            </div>
                        </div>
                        <button type="submit" class="btn w-100 btn_green">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteMemberModalLabel">Confirm Deletion</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this member?
                </div>
                <div class="modal-footer">

                    <form id="deleteMemberForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete-button">Delete</button>
                    </form>
                    <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function previewImage(event, targetId = 'profilePicPreview') {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById(targetId).src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }


    function fetchMember(id) {
        axios.get(`production-team-details/edit/${id}`)
            .then(function (response) {
                const member = response.data;
                document.getElementById('editName').value = member.name;
                document.getElementById('editDesignation').value = member.designation;
                document.getElementById('editEmail').value = member.email;

                document.getElementById('editProfilePicPreview').src = member.profile_pic ? 
                    `production_team/profile_pic/${member.profile_pic}` : '/images/default_user.jpg';

                const form = document.getElementById('editMemberForm');
                form.action = `production-team-details/update/${member.id}`;
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function confirmDelete(id) {
        const form = document.getElementById('deleteMemberForm');
        form.action = `production-team-details/delete/${id}`; 
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteMemberModal'));
        deleteModal.show(); 
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script>
    $(document).ready(function() {        
        $('#team_table').DataTable({
            "order": [
                [0, 'desc']
            ]
        });
        $('#team_table').removeClass('dataTable');
     });
</script>
@endsection