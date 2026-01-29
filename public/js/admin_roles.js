    // roles.js

    // Run when DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-button');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const roleId = this.getAttribute('data-id');
                const roleName = this.getAttribute('data-rolename');
                const status = this.getAttribute('data-status');

                // Fill role name in edit form
                document.getElementById('editRoleName').value = roleName;

                // Fill status field
                const statusField = document.getElementById('editStatus');
                if (status === 'Active') {
                    statusField.value = 'Active';
                } else {
                    statusField.value = 'Deactive';
                }

                // Update form action URL dynamically
                const editForm = document.getElementById('editRoleForm');
                // Replace :id placeholder in Laravel route with real id
                editForm.action = LaravelRoutes.update.replace(':id', roleId);
            });
        });
    });

    // Function to open delete modal
    function openDeleteModal(roleId, roleName) {
        document.getElementById('role-name').textContent = roleName;

        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = LaravelRoutes.delete + "/" + roleId;

        $('#deleteModal').modal('show');
    }

    // Initialize DataTable after jQuery is ready
    $(document).ready(function () {
        $('#roles_table').DataTable();
        $('#roles_table').removeClass('dataTable');
    });