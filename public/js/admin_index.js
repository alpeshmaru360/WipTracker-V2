// A Code: 15-12-2025 Start
window.openEditUserModal = function (userId, userName, userEmail, userRole, userProfilePic, userIsRedirect) {
    let formAction = window.LaravelRoutes.update.replace(':id', userId);
    document.getElementById('editUserForm').action = formAction;

    document.getElementById('editProfilePicPreview').src = userProfilePic ? 
                    `/public/production_team/all_user_profile_pic/${userProfilePic}` : '/images/default_user.jpg';
    document.getElementById('edit_name').value = userName;
    document.getElementById('edit_email').value = userEmail;
    document.getElementById('edit_role').value = userRole;
    document.getElementById('edit_password').value = '';  

    // A Code: 22-12-2025 Start
    const passwordSection = document.querySelector('#editUserModal .edit_password_section');
    if (passwordSection) {
        passwordSection.style.display =
            (userIsRedirect === true || userIsRedirect === 1 || userIsRedirect === '1')
                ? 'block'
                : 'none';
    }
    // A Code: 22-12-2025 End

    let editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
};
// A Code: 15-12-2025 End

window.openDeleteModal = function (userId) {
    let formAction = window.LaravelRoutes.delete.replace(':id', userId);
    document.getElementById('deleteUserForm').action = formAction;

    let deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
};

window.addEventListener('beforeunload', function () {
    navigator.sendBeacon(window.LaravelRoutes.impersonateLogout);
});

$(document).ready(function () {
    $('#user_table').DataTable();
    $('#user_table').removeClass('dataTable');
});
